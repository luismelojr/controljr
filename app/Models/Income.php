<?php

namespace App\Models;

use App\Enums\IncomeRecurrenceTypeEnum;
use App\Enums\IncomeStatusEnum;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Income extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeFactory> */
    use HasFactory, HasUuidCustom;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'name',
        'notes',
        'total_amount',
        'recurrence_type',
        'installments',
        'start_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'recurrence_type' => IncomeRecurrenceTypeEnum::class,
            'status' => IncomeStatusEnum::class,
            'start_date' => 'date',
            'installments' => 'integer',
        ];
    }

    /**
     * Set total amount in cents
     */
    public function setTotalAmountAttribute($value): void
    {
        $this->attributes['total_amount'] = $value * 100;
    }

    /**
     * Get total amount in reais
     */
    public function getTotalAmountAttribute($value): float|int
    {
        return $value / 100;
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

    /**
     * IncomeTransactions relationship
     */
    public function incomeTransactions(): HasMany
    {
        return $this->hasMany(IncomeTransaction::class);
    }
}
