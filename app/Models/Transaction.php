<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory, HasUuidCustom;

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
     * Set amount - converts reais to cents for storage
     */
    public function setAmountAttribute($value): void
    {
        // Convert reais to cents (145.25 -> 14525)
        $this->attributes['amount'] = (int) round($value * 100);
    }

    /**
     * Get amount - converts cents to reais for display
     */
    public function getAmountAttribute($value): float
    {
        // Convert cents to reais (14525 -> 145.25)
        return round($value / 100, 2);
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
     */
    public function scopeAmountFrom($query, $amount)
    {
        // Convert to cents for comparison
        $amountInCents = $amount * 100;
        return $query->whereRaw('amount >= ?', [$amountInCents]);
    }

    /**
     * Scope to filter by amount range
     */
    public function scopeAmountTo($query, $amount)
    {
        // Convert to cents for comparison
        $amountInCents = $amount * 100;
        return $query->whereRaw('amount <= ?', [$amountInCents]);
    }
}
