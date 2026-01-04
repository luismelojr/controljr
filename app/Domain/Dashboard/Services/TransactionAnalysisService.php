<?php

namespace App\Domain\Dashboard\Services;

use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use App\Models\IncomeTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service responsible for transaction analysis (upcoming, recent, ending accounts)
 */
class TransactionAnalysisService
{
    /**
     * Get upcoming transactions (next 30 days, limit 7)
     */
    public function getUpcomingTransactions(string $userId): array
    {
        return Transaction::with(['category', 'wallet', 'account'])
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PENDING)
            ->whereBetween('due_date', [now(), now()->addDays(30)])
            ->orderBy('due_date', 'asc')
            ->limit(7)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->uuid,
                    'name' => $transaction->account->name ?? 'Sem nome',
                    'category' => $transaction->category->name ?? 'Sem categoria',
                    'due_date' => $transaction->due_date->format('d/m/Y'),
                    'due_date_raw' => $transaction->due_date->format('Y-m-d'),
                    'amount' => $transaction->amount,
                    'status' => $transaction->status->value,
                    'installment_info' => $transaction->total_installments > 1
                        ? "{$transaction->installment_number}/{$transaction->total_installments}"
                        : null,
                ];
            })
            ->toArray();
    }

    /**
     * Get recent activities (last 7 transactions + income transactions)
     */
    public function getRecentActivities(string $userId): array
    {
        // Get recent paid transactions
        $transactions = Transaction::with(['category', 'wallet', 'account'])
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->orderBy('paid_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'name' => $transaction->account->name ?? 'Despesa',
                    'accountNumber' => $transaction->wallet->name ?? 'Sem carteira',
                    'date' => $transaction->paid_at->format('d M'),
                    'amount' => -abs($transaction->amount),
                    'type' => 'expense',
                    'icon' => $this->getCategoryIcon($transaction->category->name ?? ''),
                ];
            });

        // Get recent received income transactions
        $incomeTransactions = IncomeTransaction::with(['category', 'income'])
            ->where('user_id', $userId)
            ->where('is_received', true)
            ->orderBy('received_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function ($income) {
                return [
                    'id' => $income->id,
                    'name' => $income->income->name ?? 'Receita',
                    'accountNumber' => $income->category->name ?? 'Sem categoria',
                    'date' => $income->received_at->format('d M'),
                    'amount' => abs($income->amount),
                    'type' => 'income',
                    'icon' => 'income',
                ];
            });

        // Merge and sort by date
        $activities = $transactions->concat($incomeTransactions)
            ->sortByDesc('date')
            ->take(7)
            ->values()
            ->toArray();

        return $activities;
    }

    /**
     * Get accounts that end in the current month (last installment)
     * Shows accounts where the last installment is due this month and still pending
     */
    public function getAccountsEndingThisMonth(string $userId): array
    {
        return DB::table('accounts')
            ->join('transactions', 'accounts.id', '=', 'transactions.account_id')
            ->join('categories', 'accounts.category_id', '=', 'categories.id')
            ->join('wallets', 'accounts.wallet_id', '=', 'wallets.id')
            ->where('accounts.user_id', $userId)
            ->where('accounts.recurrence_type', 'installments')
            ->whereIn('accounts.status', ['active', 'completed']) // Include both active and completed
            ->where('transactions.status', 'pending') // Only pending transactions
            ->whereYear('transactions.due_date', now()->year)
            ->whereMonth('transactions.due_date', now()->month)
            ->whereRaw('transactions.installment_number = transactions.total_installments')
            ->select(
                'accounts.uuid',
                'accounts.name',
                'accounts.total_amount',
                'accounts.installments',
                'categories.name as category_name',
                'wallets.name as wallet_name',
                'transactions.due_date',
                'transactions.amount as installment_amount',
                'transactions.installment_number',
                'transactions.total_installments'
            )
            ->orderBy('transactions.due_date', 'asc')
            ->get()
            ->map(function ($account) {
                return [
                    'uuid' => $account->uuid,
                    'name' => $account->name,
                    'category' => $account->category_name,
                    'wallet' => $account->wallet_name,
                    'total_amount' => $account->total_amount / 100, // Convert cents to reais
                    'installment_amount' => $account->installment_amount / 100, // Convert cents to reais
                    'installments' => $account->installments,
                    'installment_info' => "{$account->installment_number}/{$account->total_installments}",
                    'due_date' => Carbon::parse($account->due_date)->format('d/m/Y'),
                ];
            })
            ->toArray();
    }

    /**
     * Get icon name based on category name
     */
    protected function getCategoryIcon(string $categoryName): string
    {
        $icons = [
            'Alimentação' => 'food',
            'Transporte' => 'transport',
            'Moradia' => 'home',
            'Saúde' => 'health',
            'Educação' => 'education',
            'Lazer' => 'entertainment',
            'Compras' => 'shopping',
        ];

        return $icons[$categoryName] ?? 'default';
    }
}
