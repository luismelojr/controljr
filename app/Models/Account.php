<?php

namespace App\Models;

use App\Enums\AccountStatusEnum;
use App\Enums\RecurrenceTypeEnum;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory, HasUuidCustom;

    protected $fillable = [
        'uuid',
        'user_id',
        'wallet_id',
        'category_id',
        'name',
        'description',
        'total_amount',
        'recurrence_type',
        'installments',
        'paid_installments',
        'start_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'recurrence_type' => RecurrenceTypeEnum::class,
            'status' => AccountStatusEnum::class,
            'start_date' => 'date',
            'installments' => 'integer',
            'paid_installments' => 'integer',
        ];
    }

    /**
     * Set total amount - converts reais to cents for storage
     */
    public function setTotalAmountAttribute($value): void
    {
        // Convert reais to cents (145.25 -> 14525)
        $this->attributes['total_amount'] = (int) round($value * 100);
    }

    /**
     * Get total amount - converts cents to reais for display
     */
    public function getTotalAmountAttribute($value): float
    {
        // Convert cents to reais (14525 -> 145.25)
        return round($value / 100, 2);
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
     * Transactions relationship
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
