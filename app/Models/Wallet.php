<?php

namespace App\Models;

use App\Enums\WalletTypeEnum;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory, HasUuidCustom;

    protected $fillable = [
      'name',
      'uuid',
      'type',
      'day_close',
      'best_shopping_day',
      'card_limit',
      'card_limit_used',
      'initial_balance',
      'status'
    ];

    protected function casts(): array
    {
        return [
            'type' => WalletTypeEnum::class,
            'status' => 'boolean',
        ];
    }

    /**
     * @param $value
     * @return void
     */
    public function setCardLimitAttribute($value): void
    {
        $this->attributes['card_limit'] = $value * 100;
    }

    /**
     * @param $value
     * @return float|int
     */
    public function getCardLimitAttribute($value): float|int
    {
        return $value / 100;
    }

    public function setInitialBalanceAttribute($value): void
    {
        $this->attributes['initial_balance'] = $value * 100;
    }

    public function getInitialBalanceAttribute($value): float|int
    {
        return $value / 100;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function incomeTransactions()
    {
        return $this->hasManyThrough(IncomeTransaction::class, Income::class);
    }

    /**
     * Get the current balance of the wallet.
     * Balance = initial_balance + received_incomes - paid_transactions
     */
    public function getBalanceAttribute(): float
    {
        // Get initial balance in cents from database
        $initialBalanceInCents = $this->attributes['initial_balance'] ?? 0;

        // Add received income transactions (sum returns value in cents from DB)
        $receivedIncomesInCents = $this->incomeTransactions()
            ->where('is_received', true)
            ->sum('amount');

        // Subtract paid transactions (sum returns value in cents from DB)
        $paidTransactionsInCents = $this->transactions()
            ->where('status', \App\Enums\TransactionStatusEnum::PAID->value)
            ->sum('amount');

        // Calculate final balance in cents, then convert to reais
        $balanceInCents = $initialBalanceInCents + $receivedIncomesInCents - $paidTransactionsInCents;

        return $balanceInCents / 100;
    }
}
