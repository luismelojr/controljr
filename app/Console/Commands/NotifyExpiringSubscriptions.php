<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifyExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:notify-expiring {days=3 : Days until expiration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users about upcoming subscription expiration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->argument('days');
        $targetDate = now()->addDays($days)->format('Y-m-d');

        $this->info("Checking for subscriptions expiring on {$targetDate} ({$days} days from now)...");

        $expiringSubscriptions = \App\Models\Subscription::query()
            ->where('status', 'cancelled') // Only warn if already cancelled (stopped auto-renew)
            ->whereDate('ends_at', $targetDate)
            ->with(['user', 'plan'])
            ->get();

        $count = $expiringSubscriptions->count();

        if ($count === 0) {
            $this->info('No expiring subscriptions found.');
            return;
        }

        $this->info("Found {$count} expiring subscriptions.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($expiringSubscriptions as $subscription) {
            try {
                \Illuminate\Support\Facades\Mail::to($subscription->user)
                    ->send(new \App\Mail\SubscriptionExpiringMail($subscription->user, $subscription, $days));
                
                $this->info(" Email sent to {$subscription->user->email}");
            } catch (\Exception $e) {
                $this->error(" Failed to send to {$subscription->user->email}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');
    }
}
