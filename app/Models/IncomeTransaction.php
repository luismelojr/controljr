<?php

namespace App\Models;

use App\Enums\IncomeTransactionStatusEnum;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeTransactionFactory> */
    use HasFactory, HasUuidCustom;

    protected $fillable = [
        'uuid',
        'income_id',
        'user_id',
        'category_id',
        'month_reference',
        'amount',
        'expected_date',
        'received_at',
        'installment_number',
        'total_installments',
        'is_received',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => IncomeTransactionStatusEnum::class,
            'expected_date' => 'date',
            'received_at' => 'date',
            'installment_number' => 'integer',
            'total_installments' => 'integer',
            'is_received' => 'boolean',
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
     * Income relationship
     */
    public function income(): BelongsTo
    {
        return $this->belongsTo(Income::class);
    }

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
