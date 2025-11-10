<?php

namespace App\Domain\Incomes\Services;

use App\Domain\Incomes\DTO\CreateIncomeData;
use App\Domain\Incomes\DTO\UpdateIncomeData;
use App\Enums\IncomeRecurrenceTypeEnum;
use App\Enums\IncomeStatusEnum;
use App\Enums\IncomeTransactionStatusEnum;
use App\Models\Income;
use App\Models\IncomeTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeService
{
    /**
     * Create a new income and generate income transactions
     */
    public function create(CreateIncomeData $data, User $user): Income
    {
        return DB::transaction(function () use ($data, $user) {
            // Create income
            $income = $user->incomes()->create($data->toArray());

            // Generate income transactions based on recurrence type
            $this->generateIncomeTransactions($income);

            return $income->load(['category', 'incomeTransactions']);
        });
    }

    /**
     * Generate income transactions based on recurrence type
     */
    protected function generateIncomeTransactions(Income $income): void
    {
        $startDate = Carbon::parse($income->start_date);

        match ($income->recurrence_type) {
            IncomeRecurrenceTypeEnum::ONE_TIME => $this->generateOneTimeTransaction($income, $startDate),
            IncomeRecurrenceTypeEnum::INSTALLMENTS => $this->generateInstallmentTransactions($income, $startDate),
            IncomeRecurrenceTypeEnum::RECURRING => $this->generateRecurringTransactions($income, $startDate),
        };
    }

    /**
     * Generate a single income transaction for one-time income
     */
    protected function generateOneTimeTransaction(Income $income, Carbon $startDate): void
    {
        IncomeTransaction::create([
            'income_id' => $income->id,
            'user_id' => $income->user_id,
            'category_id' => $income->category_id,
            'month_reference' => $startDate->format('Y-m'),
            'amount' => $income->total_amount,
            'expected_date' => $startDate,
            'status' => IncomeTransactionStatusEnum::PENDING,
            'is_received' => false,
        ]);
    }

    /**
     * Generate income transactions for installment income
     */
    protected function generateInstallmentTransactions(Income $income, Carbon $startDate): void
    {
        $installmentAmount = $income->total_amount / $income->installments;
        $expectedDate = $startDate->copy();

        for ($i = 1; $i <= $income->installments; $i++) {
            IncomeTransaction::create([
                'income_id' => $income->id,
                'user_id' => $income->user_id,
                'category_id' => $income->category_id,
                'month_reference' => $expectedDate->format('Y-m'),
                'amount' => $installmentAmount,
                'expected_date' => $expectedDate->copy(),
                'installment_number' => $i,
                'total_installments' => $income->installments,
                'status' => IncomeTransactionStatusEnum::PENDING,
                'is_received' => false,
            ]);

            // Next month
            $expectedDate->addMonth();
        }
    }

    /**
     * Generate income transactions for recurring income (12 months ahead)
     */
    protected function generateRecurringTransactions(Income $income, Carbon $startDate): void
    {
        $expectedDate = $startDate->copy();

        for ($i = 0; $i < 12; $i++) {
            IncomeTransaction::create([
                'income_id' => $income->id,
                'user_id' => $income->user_id,
                'category_id' => $income->category_id,
                'month_reference' => $expectedDate->format('Y-m'),
                'amount' => $income->total_amount,
                'expected_date' => $expectedDate->copy(),
                'status' => IncomeTransactionStatusEnum::PENDING,
                'is_received' => false,
            ]);

            // Next month
            $expectedDate->addMonth();
        }
    }

    /**
     * Update an existing income
     * Note: Only allows updating name, notes, and status
     */
    public function update(Income $income, UpdateIncomeData $data): Income
    {
        $income->update($data->toArray());

        return $income->fresh();
    }

    /**
     * Delete an income and all its transactions
     */
    public function delete(Income $income): bool
    {
        return DB::transaction(function () use ($income) {
            // Delete income transactions
            $income->incomeTransactions()->delete();

            // Delete income
            return $income->delete();
        });
    }

    /**
     * Toggle income status
     */
    public function toggleStatus(Income $income): Income
    {
        $newStatus = $income->status === IncomeStatusEnum::ACTIVE
            ? IncomeStatusEnum::CANCELLED
            : IncomeStatusEnum::ACTIVE;

        $income->update(['status' => $newStatus]);

        return $income->fresh();
    }

    /**
     * Complete an income (mark as completed)
     * Used when all installments are received
     */
    public function complete(Income $income): Income
    {
        $income->update(['status' => IncomeStatusEnum::COMPLETED]);

        return $income->fresh();
    }
}
