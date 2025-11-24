<?php

namespace App\Domain\Transactions\Actions;

use App\Enums\RecurrenceTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\WalletTypeEnum;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarkTransactionAsPaidAction
{
    public function execute(Transaction $transaction, ?Carbon $paidAt = null): Transaction
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

    protected function checkAndCompleteAccount($account): void
    {
        $allPaid = $account->transactions()
            ->where('status', '!=', TransactionStatusEnum::PAID)
            ->doesntExist();

        if ($allPaid) {
            $account->update(['status' => 'completed']);
        }
    }

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
}
