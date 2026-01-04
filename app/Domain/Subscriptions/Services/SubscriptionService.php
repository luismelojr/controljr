<?php

namespace App\Domain\Subscriptions\Services;

use App\Domain\Payments\Services\PaymentGatewayService;
use App\Enums\SubscriptionStatusEnum;
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
     *
     * IMPORTANTE: A assinatura atual só será cancelada quando o pagamento
     * da nova assinatura for confirmado via webhook
     */
    public function upgrade(User $user, string $newPlanSlug): Subscription
    {
        $newPlan = SubscriptionPlan::active()->bySlug($newPlanSlug)->firstOrFail();
        $currentSubscription = $user->currentSubscription;

        if (! $currentSubscription) {
            return $this->create($user, $newPlanSlug);
        }

        return DB::transaction(function () use ($user, $currentSubscription, $newPlan) {
            // Calculate prorated amount if needed (implement later)

            // Create new subscription (PENDING)
            // A assinatura atual permanece ATIVA até o pagamento ser confirmado
            $newSubscription = $this->create($user, $newPlan->slug);

            // NÃO cancelar a assinatura atual aqui!
            // Ela será cancelada automaticamente quando o webhook confirmar
            // o pagamento e ativar a nova subscription

            return $newSubscription;
        });
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
