<?php

namespace App\Domain\IncomeTransactions\Services;

use App\Enums\IncomeRecurrenceTypeEnum;
use App\Enums\IncomeTransactionStatusEnum;
use App\Models\IncomeTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IncomeTransactionService
{
    /**
     * Get all income transactions for a user with filters
     */
    public function getAllForUser(User $user, int $perPage = 15)
    {
        $baseQuery = IncomeTransaction::query()
            ->where('user_id', $user->id)
            ->with(['income', 'category']);

        return QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('category_id'),
            ])
            ->allowedSorts(['expected_date', 'amount', 'created_at'])
            ->defaultSort('expected_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get income transactions for a specific month
     */
    public function getIncomeTransactionsForMonth(User $user, int $year, int $month)
    {
        $monthReference = sprintf('%04d-%02d', $year, $month);

        return IncomeTransaction::query()
            ->where('user_id', $user->id)
            ->where('month_reference', $monthReference)
            ->with(['income', 'category'])
            ->orderBy('expected_date')
            ->get();
    }

    /**
     * Mark an income transaction as received
     */
    public function markAsReceived(IncomeTransaction $incomeTransaction, ?Carbon $receivedAt = null): IncomeTransaction
    {
        return DB::transaction(function () use ($incomeTransaction, $receivedAt) {
            $incomeTransaction->update([
                'status' => IncomeTransactionStatusEnum::RECEIVED,
                'is_received' => true,
                'received_at' => $receivedAt ?? now(),
            ]);

            // Check if income is installments and all are received → complete income
            if ($incomeTransaction->income->recurrence_type === IncomeRecurrenceTypeEnum::INSTALLMENTS) {
                $this->checkAndCompleteIncome($incomeTransaction->income);
            }

            // If recurring, ensure 12 months ahead
            if ($incomeTransaction->income->recurrence_type === IncomeRecurrenceTypeEnum::RECURRING) {
                $this->ensureRecurringIncomeTransactions($incomeTransaction->income);
            }

            return $incomeTransaction->fresh();
        });
    }

    /**
     * Mark an income transaction as not received (undo receipt)
     */
    public function markAsNotReceived(IncomeTransaction $incomeTransaction): IncomeTransaction
    {
        $incomeTransaction->update([
            'status' => IncomeTransactionStatusEnum::PENDING,
            'is_received' => false,
            'received_at' => null,
        ]);

        return $incomeTransaction->fresh();
    }

    /**
     * Mark overdue income transactions
     * Should be run daily via scheduled job
     */
    public function markOverdueIncomeTransactions(): int
    {
        return IncomeTransaction::query()
            ->where('status', IncomeTransactionStatusEnum::PENDING)
            ->where('expected_date', '<', now())
            ->update(['status' => IncomeTransactionStatusEnum::OVERDUE]);
    }

    /**
     * Check if all installments are received and complete the income
     */
    protected function checkAndCompleteIncome($income): void
    {
        $allReceived = $income->incomeTransactions()
            ->where('status', '!=', IncomeTransactionStatusEnum::RECEIVED)
            ->doesntExist();

        if ($allReceived) {
            $income->update(['status' => 'completed']);
        }
    }

    /**
     * Ensure recurring incomes always have 12 months of future transactions
     */
    protected function ensureRecurringIncomeTransactions($income): void
    {
        // Get last transaction expected date
        $lastTransaction = $income->incomeTransactions()
            ->orderBy('expected_date', 'desc')
            ->first();

        if (!$lastTransaction) {
            return;
        }

        $lastExpectedDate = Carbon::parse($lastTransaction->expected_date);
        $targetDate = now()->addMonths(12);

        // Generate missing months
        while ($lastExpectedDate->lessThan($targetDate)) {
            $lastExpectedDate->addMonth();

            IncomeTransaction::create([
                'income_id' => $income->id,
                'user_id' => $income->user_id,
                'category_id' => $income->category_id,
                'month_reference' => $lastExpectedDate->format('Y-m'),
                'amount' => $income->total_amount,
                'expected_date' => $lastExpectedDate->copy(),
                'status' => IncomeTransactionStatusEnum::PENDING,
                'is_received' => false,
            ]);
        }
    }

    /**
     * Get financial summary for a specific month (incomes only)
     */
    public function getMonthSummary(User $user, int $year, int $month): array
    {
        $incomeTransactions = $this->getIncomeTransactionsForMonth($user, $year, $month);

        $totalReceived = $incomeTransactions->where('status', IncomeTransactionStatusEnum::RECEIVED)->sum('amount');
        $totalPending = $incomeTransactions->where('status', IncomeTransactionStatusEnum::PENDING)->sum('amount');
        $totalOverdue = $incomeTransactions->where('status', IncomeTransactionStatusEnum::OVERDUE)->sum('amount');

        return [
            'total_received' => $totalReceived,
            'total_pending' => $totalPending,
            'total_overdue' => $totalOverdue,
            'total_expected' => $incomeTransactions->sum('amount'),
            'transactions_count' => $incomeTransactions->count(),
            'received_count' => $incomeTransactions->where('status', IncomeTransactionStatusEnum::RECEIVED)->count(),
            'pending_count' => $incomeTransactions->where('status', IncomeTransactionStatusEnum::PENDING)->count(),
            'overdue_count' => $incomeTransactions->where('status', IncomeTransactionStatusEnum::OVERDUE)->count(),
        ];
    }
}
