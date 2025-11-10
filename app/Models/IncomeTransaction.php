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
     * Set amount in cents
     */
    public function setAmountAttribute($value): void
    {
        $this->attributes['amount'] = $value * 100;
    }

    /**
     * Get amount in reais
     */
    public function getAmountAttribute($value): float|int
    {
        return $value / 100;
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
