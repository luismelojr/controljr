<?php

namespace App\Domain\Transactions\Actions;

use App\Enums\TransactionStatusEnum;
use App\Enums\WalletTypeEnum;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class MarkTransactionAsUnpaidAction
{
    public function execute(Transaction $transaction): Transaction
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
