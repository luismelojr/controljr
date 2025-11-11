<?php

namespace App\Console\Commands;

use App\Domain\Alerts\Services\AlertService;
use App\Models\Alert;
use App\Models\Wallet;
use App\Enums\WalletTypeEnum;
use Illuminate\Console\Command;

class CheckAlertsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check {--type=all : Type of alert to check (all, credit-card, bills, accounts)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and process alerts manually';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alertService): int
    {
        $type = $this->option('type');

        $this->info('🔔 Checking alerts...');
        $this->newLine();

        // Show current state
        $this->showCurrentState();
        $this->newLine();

        // Check alerts based on type
        match ($type) {
            'credit-card' => $this->checkCreditCardAlerts($alertService),
            'bills' => $this->checkBillAlerts($alertService),
            'accounts' => $this->checkAccountAlerts($alertService),
            default => $this->checkAllAlerts($alertService),
        };

        $this->newLine();
        $this->info('✅ Alert check completed!');

        return self::SUCCESS;
    }

    protected function showCurrentState(): void
    {
        $this->info('📊 Current State:');

        // Show alerts
        $alerts = Alert::with('user')->get();
        $this->table(
            ['ID', 'User', 'Type', 'Trigger', 'Active', 'Last Triggered'],
            $alerts->map(fn($alert) => [
                $alert->id,
                $alert->user->name ?? 'N/A',
                $alert->type,
                $alert->trigger_value ?? implode(',', $alert->trigger_days ?? []),
                $alert->is_active ? '✓' : '✗',
                $alert->last_triggered_at?->format('Y-m-d H:i') ?? 'Never',
            ])
        );

        // Show credit cards
        $this->newLine();
        $this->info('💳 Credit Cards:');
        $cards = Wallet::where('type', WalletTypeEnum::CARD_CREDIT)->get();

        if ($cards->isEmpty()) {
            $this->warn('No credit cards found!');
        } else {
            $this->table(
                ['ID', 'Name', 'Used', 'Limit', 'Usage %', 'Status'],
                $cards->map(function($card) {
                    $usagePercent = $card->card_limit > 0
                        ? round(($card->card_limit_used / $card->card_limit) * 100, 2)
                        : 0;

                    return [
                        $card->id,
                        $card->name,
                        'R$ ' . number_format($card->card_limit_used, 2, ',', '.'),
                        'R$ ' . number_format($card->card_limit, 2, ',', '.'),
                        $usagePercent . '%',
                        $usagePercent >= 80 ? '🔴 TRIGGER' : '🟢 OK',
                    ];
                })
            );
        }
    }

    protected function checkAllAlerts(AlertService $alertService): void
    {
        $this->checkCreditCardAlerts($alertService);
        $this->checkBillAlerts($alertService);
        $this->checkAccountAlerts($alertService);
    }

    protected function checkCreditCardAlerts(AlertService $alertService): void
    {
        $this->info('💳 Checking credit card alerts...');

        try {
            $alertService->checkCreditCardUsageAlerts();
            $this->info('✓ Credit card alerts checked successfully');
        } catch (\Exception $e) {
            $this->error('✗ Error checking credit card alerts: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }

    protected function checkBillAlerts(AlertService $alertService): void
    {
        $this->info('📅 Checking bill due date alerts...');

        try {
            $alertService->checkBillDueDateAlerts();
            $this->info('✓ Bill alerts checked successfully');
        } catch (\Exception $e) {
            $this->error('✗ Error checking bill alerts: ' . $e->getMessage());
        }
    }

    protected function checkAccountAlerts(AlertService $alertService): void
    {
        $this->info('💰 Checking account balance alerts...');

        try {
            $alertService->checkAccountBalanceAlerts();
            $this->info('✓ Account alerts checked successfully');
        } catch (\Exception $e) {
            $this->error('✗ Error checking account alerts: ' . $e->getMessage());
        }
    }
}
