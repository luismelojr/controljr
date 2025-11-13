<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use App\Models\Account;
use App\Enums\WalletTypeEnum;
use App\Enums\RecurrenceTypeEnum;
use Illuminate\Console\Command;

class RecalculateCardLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallets:recalculate-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate card_limit_used for all credit card wallets based on actual accounts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Recalculating card limits for all credit card wallets...');
        $this->newLine();

        $creditCardWallets = Wallet::where('type', WalletTypeEnum::CARD_CREDIT)->get();

        if ($creditCardWallets->isEmpty()) {
            $this->info('No credit card wallets found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$creditCardWallets->count()} credit card wallet(s).");
        $this->newLine();

        foreach ($creditCardWallets as $wallet) {
            $this->line("Processing: {$wallet->name} (ID: {$wallet->id})");
            $this->line("  Current limit_used: R$ " . number_format($wallet->card_limit_used, 2, ',', '.'));

            // Get all active accounts for this wallet
            $accounts = Account::where('wallet_id', $wallet->id)
                ->where('status', 'active')
                ->get();

            $totalToConsume = 0;

            foreach ($accounts as $account) {
                $amountToConsume = $this->calculateAmountToConsume($account);
                $totalToConsume += $amountToConsume;

                $this->line("    - Account: {$account->name}");
                $this->line("      Type: {$account->recurrence_type->value}");
                if ($account->recurrence_type === RecurrenceTypeEnum::INSTALLMENTS) {
                    $remaining = $account->installments - $account->paid_installments;
                    $this->line("      Installments: {$remaining}/{$account->installments} remaining");
                }
                $this->line("      Consuming: R$ " . number_format($amountToConsume, 2, ',', '.'));
            }

            $this->newLine();
            $this->line("  Calculated total: R$ " . number_format($totalToConsume, 2, ',', '.'));

            // Update directly to the database (bypassing mutators to avoid double conversion)
            \DB::table('wallets')
                ->where('id', $wallet->id)
                ->update(['card_limit_used' => $totalToConsume * 100]); // Store in cents

            $this->info("  ✓ Updated!");
            $this->newLine();
        }

        $this->info('All credit card limits recalculated successfully! ✓');

        return Command::SUCCESS;
    }

    /**
     * Calculate the amount that will be consumed from the wallet limit
     */
    protected function calculateAmountToConsume(Account $account): float
    {
        // For installments, calculate only remaining installments
        if ($account->recurrence_type === RecurrenceTypeEnum::INSTALLMENTS && $account->installments > 0) {
            $installmentAmount = $account->total_amount / $account->installments;
            $remainingInstallments = $account->installments - $account->paid_installments;
            return ($installmentAmount * $remainingInstallments) / 100; // Convert cents to reais
        }

        // For one-time and recurring, use the total amount
        return $account->total_amount / 100; // Convert cents to reais
    }
}
