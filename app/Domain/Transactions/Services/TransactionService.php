<?php

namespace App\Domain\Transactions\Services;

use App\Enums\RecurrenceTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\WalletTypeEnum;
use App\Models\Transaction;
use App\Models\User;
use App\QueryFilters\AccountNameFilter;
use App\QueryFilters\WalletTypeFilter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionService
{
    /**
     * Get all transactions for a user with filters
     */
    public function getAllForUser(User $user, int $perPage = 15)
    {
        $baseQuery = Transaction::query()
            ->where('user_id', $user->id)
            ->with(['account', 'wallet', 'category', 'tags']);

        return QueryBuilder::for($baseQuery)
            ->allowedFilters([
                // Exact filters
                AllowedFilter::exact('status'),
                AllowedFilter::exact('category_id'),

                // Custom filters
                AllowedFilter::custom('account_name', new AccountNameFilter()),
                AllowedFilter::custom('wallet_type', new WalletTypeFilter()),

                // Scope filters for date range
                AllowedFilter::scope('due_date_from'),
                AllowedFilter::scope('due_date_to'),

                // Scope filters for amount range
                AllowedFilter::scope('amount_from'),
                AllowedFilter::scope('amount_to'),
            ])
            ->allowedSorts([
                'due_date',
                'amount',
                'created_at',
                AllowedSort::field('account', 'account_id'),
                AllowedSort::field('wallet', 'wallet_id'),
                AllowedSort::field('category', 'category_id'),
            ])
            ->defaultSort('-due_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get transactions for a specific month
     */
    public function getTransactionsForMonth(User $user, int $year, int $month)
    {
        return Transaction::query()
            ->where('user_id', $user->id)
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->with(['account', 'wallet', 'category', 'tags'])
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Mark overdue transactions
     * Should be run daily via scheduled job
     */
    public function markOverdueTransactions(): int
    {
        return Transaction::query()
            ->where('status', TransactionStatusEnum::PENDING)
            ->where('due_date', '<', now())
            ->update(['status' => TransactionStatusEnum::OVERDUE]);
    }

    /**
     * Get financial summary for a specific month
     */
    public function getMonthSummary(User $user, int $year, int $month): array
    {
        $transactions = $this->getTransactionsForMonth($user, $year, $month);

        $totalSpent = $transactions->where('status', TransactionStatusEnum::PAID)->sum('amount');
        $totalPending = $transactions->where('status', TransactionStatusEnum::PENDING)->sum('amount');
        $totalOverdue = $transactions->where('status', TransactionStatusEnum::OVERDUE)->sum('amount');

        return [
            'total_spent' => $totalSpent,
            'total_pending' => $totalPending,
            'total_overdue' => $totalOverdue,
            'total_expected' => $transactions->sum('amount'),
            'transactions_count' => $transactions->count(),
            'paid_count' => $transactions->where('status', TransactionStatusEnum::PAID)->count(),
            'pending_count' => $transactions->where('status', TransactionStatusEnum::PENDING)->count(),
            'overdue_count' => $transactions->where('status', TransactionStatusEnum::OVERDUE)->count(),
        ];
    }
}
