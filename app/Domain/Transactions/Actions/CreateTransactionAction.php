<?php

namespace App\Domain\Transactions\Actions;

use App\Enums\AccountStatusEnum;
use App\Enums\RecurrenceTypeEnum;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTransactionAction
{
    public function execute(User $user, array $data): Transaction
    {
        return DB::transaction(function () use ($user, $data) {
            $accountId = $data['account_id'] ?? null;

            // If no account_id provided, create a one-time account
            if (!$accountId) {
                $account = Account::create([
                    'user_id' => $user->id,
                    'wallet_id' => $data['wallet_id'],
                    'category_id' => $data['category_id'],
                    'name' => 'Conciliação Bancária - ' . now()->format('d/m/Y H:i'),
                    'description' => 'Transação criada via conciliação bancária',
                    'total_amount' => $data['amount'],
                    'recurrence_type' => RecurrenceTypeEnum::ONE_TIME,
                    'start_date' => $data['due_date'],
                    'status' => AccountStatusEnum::COMPLETED, // Since it's likely already paid or single
                ]);
                $accountId = $account->id;
            }

            return Transaction::create([
                'user_id' => $user->id,
                'account_id' => $accountId,
                'amount' => $data['amount'],
                'due_date' => $data['due_date'],
                'paid_at' => $data['paid_at'] ?? ($data['status'] === 'paid' ? $data['due_date'] : null),
                'category_id' => $data['category_id'],
                'wallet_id' => $data['wallet_id'],
                'status' => $data['status'],
                'is_reconciled' => $data['is_reconciled'] ?? false,
                'external_id' => $data['external_id'] ?? null,
            ]);
        });
    }
}
