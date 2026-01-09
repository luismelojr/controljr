<?php

namespace App\Domain\Dashboard\Services;

use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use App\Models\IncomeTransaction;
use App\Models\Wallet;

/**
 * Service responsible for wallet balance calculations
 */
class WalletBalanceService
{
    /**
     * Get total balance from all wallets
     * Balance = sum of all wallet initial balances + all received incomes - all paid transactions
     */
    public function getTotalBalance(string $userId): float
    {
        // Get sum of all initial balances from wallets (in cents)
        $initialBalancesInCents = Wallet::where('user_id', $userId)
            ->sum('initial_balance');

        // Get all received income transactions for this user (in cents)
        $receivedIncomesInCents = IncomeTransaction::where('user_id', $userId)
            ->where('is_received', true)
            ->sum('amount');

        // Get all paid transactions for this user (in cents)
        $paidTransactionsInCents = Transaction::where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->sum('amount');

        // Calculate final balance in cents, then convert to reais
        $balanceInCents = $initialBalancesInCents + $receivedIncomesInCents - $paidTransactionsInCents;

        return $balanceInCents / 100;
    }

    /**
     * Get balance percentage change compared to last month
     */
    public function getBalancePercentageChange(string $userId, float $currentBalance, float $thisMonthIncome, float $thisMonthExpenses): float
    {
        // Calculate balance at the end of last month
        // Last month balance = current balance - (this month income - this month expenses)
        $lastMonthBalance = $currentBalance - ($thisMonthIncome - $thisMonthExpenses);

        if ($lastMonthBalance == 0) {
            return 0;
        }

        return round((($currentBalance - $lastMonthBalance) / abs($lastMonthBalance)) * 100, 1);
    }

    /**
     * Get wallets summary with balance and usage
     */
    public function getWalletsSummary(string $userId): array
    {
        return Wallet::where('user_id', $userId)
            ->where('status', true)
            ->get()
            ->map(function ($wallet) {
                $data = [
                    'id' => $wallet->uuid,
                    'name' => $wallet->name,
                    'type' => $wallet->type->value,
                    'balance' => $wallet->balance,
                ];

                // Add credit card specific data
                if ($wallet->type->value === 'card_credit' && $wallet->card_limit > 0) {
                    $data['card_limit'] = $wallet->card_limit;
                    $data['card_limit_used'] = $wallet->card_limit_used;
                    $data['usage_percentage'] = round(($wallet->card_limit_used / $wallet->card_limit) * 100, 1);
                }

                return $data;
            })
            ->toArray();
    }
}
