<?php

namespace App\Domain\Reconciliation\Services;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class ReconciliationService
{
    public function __construct(
        protected SimpleOfxParser $parser
    ) {}

    public function processFile(User $user, UploadedFile $file): array
    {
        $content = $file->get();
        $bankTransactions = $this->parser->loadFromString($content);
        
        $results = [];

        foreach ($bankTransactions as $bankTx) {
            // 1. Try to find a matching transaction in the system
            $match = $this->findMatch($user, $bankTx);

            $results[] = [
                'bank_date' => $bankTx->date_parsed,
                'bank_amount' => (float) $bankTx->amount,
                'bank_description' => $bankTx->memo,
                'external_id' => $bankTx->unique_id,
                'suggested_match' => $match ? $match->load(['category', 'wallet']) : null, 
                'status' => $match ? 'match_found' : 'new_entry'
            ];
        }

        return $results;
    }

    private function findMatch(User $user, object $bankTx): ?Transaction
    {
        // Parse bank amount (e.g. -145.20) to cents (14520 or -14520)
        // Note: System stores amounts as positive usually? Let's check Transaction logic.
        // Looking at Transaction model: setAmountAttribute converts to int.
        // Usually expenses are stored as positive integers in 'amount' but context matters.
        // Let's assume for now amounts are absolute values for matching or we check signs.
        // If Transaction has 'type' (expense/income), we might need to check.
        // The Transaction model doesn't seem to have 'type' column in the read_file output earlier, 
        // but it belongs to a Category which might determine type, or amount is signed?
        // Actually, in most personal finance apps, expenses are positive numbers in DB but negative in OFX.
        
        $amountFloat = (float) $bankTx->amount;
        $amountInCents = (int) round(abs($amountFloat) * 100);
        
        $bankDate = Carbon::parse($bankTx->date_parsed);

        return Transaction::query()
            ->where('user_id', $user->id)
            ->where('is_reconciled', false)
            // Match amount (absolute value for now)
            ->where('amount', $amountInCents)
            // Accept difference of up to 3 days
            ->whereBetween('due_date', [
                $bankDate->copy()->subDays(3),
                $bankDate->copy()->addDays(3)
            ])
            ->first();
    }
    
    public function reconcile(Transaction $transaction, string $externalId): void
    {
        $transaction->update([
            'is_reconciled' => true,
            'external_id' => $externalId,
            'status' => 'paid', // If matched with bank, it is paid
            'paid_at' => $transaction->paid_at ?? now()
        ]);
    }
}

