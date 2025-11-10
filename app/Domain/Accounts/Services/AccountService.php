<?php

namespace App\Domain\Accounts\Services;

use App\Domain\Accounts\DTO\CreateAccountData;
use App\Domain\Accounts\DTO\UpdateAccountData;
use App\Enums\AccountStatusEnum;
use App\Enums\RecurrenceTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\WalletTypeEnum;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountService
{
    /**
     * Create a new account and generate transactions
     */
    public function create(CreateAccountData $data, User $user): Account
    {
        return DB::transaction(function () use ($data, $user) {
            // Create account
            $account = $user->accounts()->create($data->toArray());

            // Update wallet limit_used if credit card
            $wallet = Wallet::findOrFail($data->wallet_id);
            if ($wallet->type === WalletTypeEnum::CARD_CREDIT) {
                $wallet->increment('card_limit_used', $data->total_amount * 100); // in cents
            }

            // Generate transactions based on recurrence type
            $this->generateTransactions($account, $wallet);

            return $account->load(['wallet', 'category', 'transactions']);
        });
    }

    /**
     * Generate transactions based on account recurrence type
     */
    protected function generateTransactions(Account $account, Wallet $wallet): void
    {
        $startDate = Carbon::parse($account->start_date);

        match ($account->recurrence_type) {
            RecurrenceTypeEnum::ONE_TIME => $this->generateOneTimeTransaction($account, $wallet, $startDate),
            RecurrenceTypeEnum::INSTALLMENTS => $this->generateInstallmentTransactions($account, $wallet, $startDate),
            RecurrenceTypeEnum::RECURRING => $this->generateRecurringTransactions($account, $wallet, $startDate),
        };
    }

    /**
     * Generate a single transaction for one-time purchases
     */
    protected function generateOneTimeTransaction(Account $account, Wallet $wallet, Carbon $startDate): void
    {
        $dueDate = $this->calculateDueDate($wallet, $startDate);

        Transaction::create([
            'account_id' => $account->id,
            'user_id' => $account->user_id,
            'wallet_id' => $account->wallet_id,
            'category_id' => $account->category_id,
            'amount' => $account->total_amount,
            'due_date' => $dueDate,
            'status' => TransactionStatusEnum::PENDING,
        ]);
    }

    /**
     * Generate transactions for installment purchases
     */
    protected function generateInstallmentTransactions(Account $account, Wallet $wallet, Carbon $startDate): void
    {
        $installmentAmount = $account->total_amount / $account->installments;
        $dueDate = $this->calculateDueDate($wallet, $startDate);

        for ($i = 1; $i <= $account->installments; $i++) {
            Transaction::create([
                'account_id' => $account->id,
                'user_id' => $account->user_id,
                'wallet_id' => $account->wallet_id,
                'category_id' => $account->category_id,
                'amount' => $installmentAmount,
                'due_date' => $dueDate->copy(),
                'installment_number' => $i,
                'total_installments' => $account->installments,
                'status' => TransactionStatusEnum::PENDING,
            ]);

            // Next month
            $dueDate->addMonth();
        }
    }

    /**
     * Generate transactions for recurring purchases (12 months ahead)
     */
    protected function generateRecurringTransactions(Account $account, Wallet $wallet, Carbon $startDate): void
    {
        $dueDate = $this->calculateDueDate($wallet, $startDate);

        for ($i = 0; $i < 12; $i++) {
            Transaction::create([
                'account_id' => $account->id,
                'user_id' => $account->user_id,
                'wallet_id' => $account->wallet_id,
                'category_id' => $account->category_id,
                'amount' => $account->total_amount,
                'due_date' => $dueDate->copy(),
                'status' => TransactionStatusEnum::PENDING,
            ]);

            // Next month
            $dueDate->addMonth();
        }
    }

    /**
     * Calculate due date based on wallet type and closing day
     *
     * For credit cards:
     * - If start_date > closing_day → first transaction falls on next month
     * - If start_date <= closing_day → falls on current month
     */
    protected function calculateDueDate(Wallet $wallet, Carbon $startDate): Carbon
    {
        if ($wallet->type !== WalletTypeEnum::CARD_CREDIT) {
            return $startDate->copy();
        }

        $closingDay = $wallet->day_close;
        $dueDate = $startDate->copy();

        // If purchase is after closing day, move to next month
        if ($startDate->day > $closingDay) {
            $dueDate->addMonth();
        }

        // Set day to closing day (or last day of month if closing day doesn't exist)
        $dueDate->day = min($closingDay, $dueDate->daysInMonth);

        return $dueDate;
    }

    /**
     * Update an existing account
     * Note: Only allows updating name, description, and status
     */
    public function update(Account $account, UpdateAccountData $data): Account
    {
        $account->update($data->toArray());

        return $account->fresh();
    }

    /**
     * Delete an account and all its transactions
     * Also refunds the wallet limit if credit card
     */
    public function delete(Account $account): bool
    {
        return DB::transaction(function () use ($account) {
            $wallet = $account->wallet;

            // Refund wallet limit_used if credit card
            if ($wallet->type === WalletTypeEnum::CARD_CREDIT) {
                $wallet->decrement('card_limit_used', $account->total_amount * 100); // in cents
            }

            // Delete transactions (cascade will handle this, but explicit for clarity)
            $account->transactions()->delete();

            // Delete account
            return $account->delete();
        });
    }

    /**
     * Toggle account status
     */
    public function toggleStatus(Account $account): Account
    {
        $newStatus = $account->status === AccountStatusEnum::ACTIVE
            ? AccountStatusEnum::CANCELLED
            : AccountStatusEnum::ACTIVE;

        $account->update(['status' => $newStatus]);

        return $account->fresh();
    }

    /**
     * Complete an account (mark as completed)
     * Used when all installments are paid
     */
    public function complete(Account $account): Account
    {
        $account->update(['status' => AccountStatusEnum::COMPLETED]);

        return $account->fresh();
    }
}
