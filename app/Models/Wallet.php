<?php

namespace App\Models;

use App\Enums\WalletTypeEnum;
use App\Traits\HasUuidCustom;
use App\Traits\HasMoneyAccessors;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory, HasUuidCustom, HasMoneyAccessors;

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
     * Interact with the wallet's card limit.
     * Uses HasMoneyAccessors trait for consistent money conversion
     */
    protected function cardLimit(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->centsToBRL($value),
            set: fn ($value) => $this->brlToCents($value),
        );
    }

    /**
     * Interact with the wallet's initial balance.
     * Uses HasMoneyAccessors trait for consistent money conversion
     */
    protected function initialBalance(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->centsToBRL($value),
            set: fn ($value) => $this->brlToCents($value),
        );
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
     * Uses HasMoneyAccessors trait for consistent money conversion
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

        return $this->centsToBRL($balanceInCents);
    }
}
