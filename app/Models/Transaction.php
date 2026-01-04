<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Traits\HasUuidCustom;
use App\Traits\HasMoneyAccessors;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory, HasUuidCustom, HasMoneyAccessors;

    protected $fillable = [
        'uuid',
        'account_id',
        'user_id',
        'wallet_id',
        'category_id',
        'amount',
        'due_date',
        'paid_at',
        'installment_number',
        'total_installments',
        'status',
        'is_reconciled',
        'external_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => TransactionStatusEnum::class,
            'due_date' => 'date',
            'paid_at' => 'date',
            'installment_number' => 'integer',
            'total_installments' => 'integer',
        ];
    }

    /**
     * Interact with the transaction's amount.
     * Uses HasMoneyAccessors trait for consistent money conversion
     */
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->centsToBRL($value),
            set: fn ($value) => $this->brlToCents($value),
        );
    }

    /**
     * Account relationship
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Wallet relationship
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Category relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope to filter by due date range
     */
    public function scopeDueDateFrom($query, $date)
    {
        return $query->where('due_date', '>=', $date);
    }

    /**
     * Scope to filter by due date range
     */
    public function scopeDueDateTo($query, $date)
    {
        return $query->where('due_date', '<=', $date);
    }

    /**
     * Scope to filter by amount range
     * Uses HasMoneyAccessors trait for consistent conversion
     */
    public function scopeAmountFrom($query, $amount)
    {
        $amountInCents = $this->brlToCents($amount);
        return $query->whereRaw('amount >= ?', [$amountInCents]);
    }

    /**
     * Scope to filter by amount range
     * Uses HasMoneyAccessors trait for consistent conversion
     */
    public function scopeAmountTo($query, $amount)
    {
        $amountInCents = $this->brlToCents($amount);
        return $query->whereRaw('amount <= ?', [$amountInCents]);
    }
}
