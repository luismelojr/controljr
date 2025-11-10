<?php

namespace App\Domain\Transactions\Services;

use App\Enums\RecurrenceTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\WalletTypeEnum;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
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
            ->with(['account', 'wallet', 'category']);

        return QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('wallet_id'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('month'), // will need to add this scope to Transaction model
            ])
            ->allowedSorts(['due_date', 'amount', 'created_at'])
            ->defaultSort('due_date')
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
            ->with(['account', 'wallet', 'category'])
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Mark a transaction as paid
     */
    public function markAsPaid(Transaction $transaction, ?Carbon $paidAt = null): Transaction
    {
        return DB::transaction(function () use ($transaction, $paidAt) {
            $transaction->update([
                'status' => TransactionStatusEnum::PAID,
                'paid_at' => $paidAt ?? now(),
            ]);

            // Release credit card limit if wallet is a credit card
            $this->releaseCreditCardLimit($transaction);

            // Check if account is installments and all are paid → complete account
            if ($transaction->account->recurrence_type === RecurrenceTypeEnum::INSTALLMENTS) {
                $this->checkAndCompleteAccount($transaction->account);
            }

            // If recurring, ensure 12 months ahead
            if ($transaction->account->recurrence_type === RecurrenceTypeEnum::RECURRING) {
                $this->ensureRecurringTransactions($transaction->account);
            }

            return $transaction->fresh();
        });
    }

    /**
     * Mark a transaction as unpaid (undo payment)
     */
    public function markAsUnpaid(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status' => TransactionStatusEnum::PENDING,
                'paid_at' => null,
            ]);

            // Occupy credit card limit again if wallet is a credit card
            $this->occupyCreditCardLimit($transaction);

            return $transaction->fresh();
        });
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
     * Check if all installments are paid and complete the account
     */
    protected function checkAndCompleteAccount($account): void
    {
        $allPaid = $account->transactions()
            ->where('status', '!=', TransactionStatusEnum::PAID)
            ->doesntExist();

        if ($allPaid) {
            $account->update(['status' => 'completed']);
        }
    }

    /**
     * Ensure recurring accounts always have 12 months of future transactions
     */
    protected function ensureRecurringTransactions($account): void
    {
        // Get last transaction due date
        $lastTransaction = $account->transactions()
            ->orderBy('due_date', 'desc')
            ->first();

        if (!$lastTransaction) {
            return;
        }

        $lastDueDate = Carbon::parse($lastTransaction->due_date);
        $targetDate = now()->addMonths(12);

        // Generate missing months
        while ($lastDueDate->lessThan($targetDate)) {
            $lastDueDate->addMonth();

            Transaction::create([
                'account_id' => $account->id,
                'user_id' => $account->user_id,
                'wallet_id' => $account->wallet_id,
                'category_id' => $account->category_id,
                'amount' => $account->total_amount,
                'due_date' => $lastDueDate->copy(),
                'status' => TransactionStatusEnum::PENDING,
            ]);
        }
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

    /**
     * Release credit card limit when a transaction is paid
     */
    protected function releaseCreditCardLimit(Transaction $transaction): void
    {
        $wallet = $transaction->wallet;

        // Only apply to credit cards
        if ($wallet->type !== WalletTypeEnum::CARD_CREDIT) {
            return;
        }

        // Release the transaction amount from the used limit
        $wallet->card_limit_used = $wallet->card_limit_used - $transaction->amount;
        $wallet->save();
    }

    /**
     * Occupy credit card limit when a transaction is marked as unpaid
     */
    protected function occupyCreditCardLimit(Transaction $transaction): void
    {
        $wallet = $transaction->wallet;

        // Only apply to credit cards
        if ($wallet->type !== WalletTypeEnum::CARD_CREDIT) {
            return;
        }

        // Occupy the transaction amount in the used limit
        $wallet->card_limit_used = $wallet->card_limit_used + $transaction->amount;
        $wallet->save();
    }
}
