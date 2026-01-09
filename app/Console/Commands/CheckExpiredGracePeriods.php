<?php

namespace App\Console\Commands;

use App\Domain\Subscriptions\Services\SubscriptionService;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredGracePeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-grace-periods {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for subscriptions with expired payment grace periods and cancel them';

    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Checking for expired payment grace periods...');

        $isDryRun = $this->option('dry-run');

        // Find subscriptions with expired grace periods
        $subscriptions = Subscription::where('status', SubscriptionStatusEnum::PAYMENT_FAILED->value)
            ->whereNotNull('payment_grace_period_ends_at')
            ->where('payment_grace_period_ends_at', '<=', now())
            ->with('user', 'plan')
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('✅ No subscriptions with expired grace periods found.');

            return self::SUCCESS;
        }

        $this->warn("⚠️  Found {$subscriptions->count()} subscription(s) with expired grace periods");

        foreach ($subscriptions as $subscription) {
            $this->line('');
            $this->line("📋 Subscription ID: {$subscription->id}");
            $this->line("   User: {$subscription->user->name} ({$subscription->user->email})");
            $this->line("   Plan: {$subscription->plan->name}");
            $this->line("   Failed payments: {$subscription->failed_payments_count}");
            $this->line("   Grace period ended: {$subscription->payment_grace_period_ends_at->diffForHumans()}");

            if ($isDryRun) {
                $this->comment('   🔍 [DRY RUN] Would cancel this subscription');

                continue;
            }

            try {
                // Cancel subscription
                $this->subscriptionService->cancel($subscription, immediately: true);

                Log::warning('Subscription cancelled due to expired grace period', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'failed_payments_count' => $subscription->failed_payments_count,
                    'grace_period_ended_at' => $subscription->payment_grace_period_ends_at,
                ]);

                $this->error('   ❌ Subscription cancelled');

                // TODO: Send email notification to user about cancellation
            } catch (\Exception $e) {
                $this->error("   ⚠️  Error cancelling subscription: {$e->getMessage()}");

                Log::error('Failed to cancel subscription with expired grace period', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->line('');

        if ($isDryRun) {
            $this->info('🔍 Dry run completed. No changes were made.');
        } else {
            $this->info('✅ Finished processing expired grace periods.');
        }

        return self::SUCCESS;
    }
}
