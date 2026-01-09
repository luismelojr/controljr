<?php

namespace App\Models;

use App\Enums\IncomeTransactionStatusEnum;
use App\Traits\HasUuidCustom;
use App\Traits\HasMoneyAccessors;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeTransactionFactory> */
    use HasFactory, HasUuidCustom, HasMoneyAccessors;

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
     * Interact with the income transaction's amount.
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
