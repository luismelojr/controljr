<?php

namespace App\Domain\Subscriptions\Services;

use App\Domain\Payments\Services\PaymentGatewayService;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Payment;
use App\Models\Subscription;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(
        protected PaymentGatewayService $paymentGatewayService
    ) {
    }

    /**
     * Create a new subscription for a user
     */
    public function create(User $user, string $planSlug, string $paymentGateway = 'asaas'): Subscription
    {
        $plan = SubscriptionPlan::active()->bySlug($planSlug)->firstOrFail();

        return DB::transaction(function () use ($user, $plan, $paymentGateway) {
            // Cancel any active subscriptions
            $this->cancelActiveSubscriptions($user);

            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'started_at' => now(),
                'ends_at' => $plan->isFree() ? null : now()->addMonth(),
                'status' => SubscriptionStatusEnum::PENDING->value,
                'payment_gateway' => $paymentGateway,
            ]);

            // If free plan, activate immediately
            if ($plan->isFree()) {
                $subscription->update([
                    'status' => SubscriptionStatusEnum::ACTIVE->value,
                ]);

                // Update user's current subscription
                $user->update([
                    'current_subscription_id' => $subscription->id,
                ]);
            }

            return $subscription->fresh();
        });
    }

    /**
     * Activate a subscription (after payment confirmation)
     */
    public function activate(Subscription $subscription): Subscription
    {
        DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => SubscriptionStatusEnum::ACTIVE->value,
            ]);

            // Update user's current subscription
            $subscription->user->update([
                'current_subscription_id' => $subscription->id,
            ]);
        });

        return $subscription->fresh();
    }

    /**
     * Upgrade user to a new plan
     */
    /**
     * Upgrade user to a new plan (Step 1: Create pending subscription)
     */
    public function upgrade(User $user, string $newPlanSlug): Subscription
    {
        $newPlan = SubscriptionPlan::active()->bySlug($newPlanSlug)->firstOrFail();
        $currentSubscription = $user->currentSubscription;

        if (! $currentSubscription) {
            return $this->create($user, $newPlanSlug);
        }

        if ($newPlan->price_cents <= $currentSubscription->plan->price_cents) {
            throw new \Exception('Para realizar um downgrade, aguarde o final do ciclo ou cancele a assinatura atual.');
        }

        // Create new pending subscription without cancelling the old one
        return Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $newPlan->id,
            'started_at' => now(),
            'ends_at' => $currentSubscription->ends_at,
            'status' => SubscriptionStatusEnum::PENDING->value,
            'payment_gateway' => 'asaas',
        ]);
    }

    /**
     * Process upgrade payment (Step 2: Charge prorated amount + Schedule future sub)
     */
    public function processUpgradePayment(Subscription $newSubscription, string $paymentMethod): Payment
    {
        $user = $newSubscription->user;
        $currentSubscription = $user->currentSubscription;

        // If no current subscription, just create standard payment
        if (! $currentSubscription || !$currentSubscription->isActive()) {
            return $this->paymentGatewayService->createSubscriptionPayment($newSubscription, $paymentMethod);
        }

        return DB::transaction(function () use ($currentSubscription, $newSubscription, $paymentMethod) {
            // 1. Calculate Prorated Amount
            $proratedAmount = $this->calculateProratedAmount($currentSubscription, $newSubscription->plan);

            // 2. Create Upgrade Subscription
            $nextDueDate = $currentSubscription->ends_at ?? now()->addMonth();

            return $this->paymentGatewayService->createUpgradeSubscription(
                $newSubscription,
                $proratedAmount,
                $paymentMethod,
                $nextDueDate
            );
        });
    }

    /**
     * Calculate prorated amount for upgrade
     */
    public function calculateProratedAmount(Subscription $currentSubscription, SubscriptionPlan $newPlan): float
    {
        // If current plan is free or expired, or no end date, full price
        if ($currentSubscription->plan->isFree() || $currentSubscription->isExpired() || ! $currentSubscription->ends_at) {
            return $newPlan->getAmountInReais();
        }

        $now = now();
        $endsAt = $currentSubscription->ends_at;

        // Safety check: if ends_at is past, full price
        if ($endsAt->isPast()) {
            return $newPlan->getAmountInReais();
        }

        // Days remaining in current cycle
        $remainingDays = $now->diffInDays($endsAt, false); // false = absolute difference
        
        if ($remainingDays <= 0) {
             return $newPlan->getAmountInReais();
        }

        // Total days in cycle (approx 30)
        $totalDays = 30;

        // Ratio of unused time
        $ratio = $remainingDays / $totalDays;

        // Difference in price
        $currentPrice = $currentSubscription->plan->getAmountInReais();
        $newPrice = $newPlan->getAmountInReais();

        $diff = $newPrice - $currentPrice;

        if ($diff <= 0) {
            return 0.0;
        }

        $prorated = $diff * $ratio;

        return round($prorated, 2);
    }

    /**
     * Downgrade user to a new plan (at end of current period)
     */
    public function downgrade(User $user, string $newPlanSlug): Subscription
    {
        $newPlan = SubscriptionPlan::active()->bySlug($newPlanSlug)->firstOrFail();
        $currentSubscription = $user->currentSubscription;

        if (! $currentSubscription) {
            return $this->create($user, $newPlanSlug);
        }

        // Schedule downgrade at end of current period
        // For now, just create the new subscription at end date
        return DB::transaction(function () use ($user, $currentSubscription, $newPlan) {
            // Mark current subscription to end at current period
            $currentSubscription->update([
                'ends_at' => $currentSubscription->ends_at ?? now()->addMonth(),
            ]);

            // Create new subscription starting at end of current period
            $newSubscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $newPlan->id,
                'started_at' => $currentSubscription->ends_at,
                'ends_at' => $newPlan->isFree() ? null : $currentSubscription->ends_at->copy()->addMonth(),
                'status' => SubscriptionStatusEnum::PENDING->value,
                'payment_gateway' => 'asaas',
            ]);

            return $newSubscription;
        });
    }

    /**
     * Cancel a subscription
     */
    public function cancel(Subscription $subscription, bool $immediately = false): Subscription
    {
        // Cancel recurring subscription in Asaas if exists
        $this->paymentGatewayService->cancelRecurringSubscription($subscription);

        if ($immediately) {
            $subscription->cancel();
            $subscription->markAsExpired();

            // Set user back to free plan
            $freePlan = SubscriptionPlan::free()->first();
            if ($freePlan) {
                $freeSubscription = $this->create($subscription->user, $freePlan->slug);
                $this->activate($freeSubscription);
            }
        } else {
            // Cancel at end of billing period
            $subscription->cancel();
        }

        return $subscription->fresh();
    }

    /**
     * Resume a cancelled subscription
     */
    public function resume(Subscription $subscription): Subscription
    {
        if (! $subscription->onGracePeriod()) {
            throw new \Exception('Assinatura não pode ser retomada');
        }

        $subscription->resume();

        return $subscription->fresh();
    }

    /**
     * Renew a subscription
     */
    public function renew(Subscription $subscription): Subscription
    {
        $endsAt = $subscription->ends_at
            ? $subscription->ends_at->copy()->addMonth()
            : now()->addMonth();

        $subscription->renew($endsAt);

        return $subscription->fresh();
    }

    /**
     * Check and expire subscriptions
     */
    public function checkExpiredSubscriptions(): int
    {
        $expiredCount = 0;

        $subscriptions = Subscription::active()
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->markAsExpired();

            // Move user to free plan
            $freePlan = SubscriptionPlan::free()->first();
            if ($freePlan) {
                $freeSubscription = $this->create($subscription->user, $freePlan->slug);
                $this->activate($freeSubscription);
            }

            $expiredCount++;
        }

        return $expiredCount;
    }

    /**
     * Cancel all active subscriptions for a user
     */
    protected function cancelActiveSubscriptions(User $user): void
    {
        $user->subscriptions()
            ->active()
            ->each(function ($subscription) {
                $subscription->cancel();
            });
    }

    /**
     * Link subscription to external service (Asaas)
     */
    public function linkExternal(
        Subscription $subscription,
        string $externalSubscriptionId,
        string $externalCustomerId = null
    ): Subscription {
        $subscription->update([
            'external_subscription_id' => $externalSubscriptionId,
            'external_customer_id' => $externalCustomerId,
        ]);

        return $subscription->fresh();
    }

    /**
     * Get active subscription for user
     */
    public function getActiveSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()
            ->active()
            ->with('plan')
            ->first();
    }

    /**
     * Get subscription history for user
     */
    public function getHistory(User $user)
    {
        return $user->subscriptions()
            ->with('plan')
            ->orderByDesc('created_at')
            ->get();
    }
}
