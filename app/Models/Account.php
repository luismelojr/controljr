<?php

namespace App\Models;

use App\Enums\AccountStatusEnum;
use App\Enums\RecurrenceTypeEnum;
use App\Traits\HasUuidCustom;
use App\Traits\HasMoneyAccessors;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory, HasUuidCustom, HasMoneyAccessors;

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
     * Interact with the account's total amount.
     * Uses HasMoneyAccessors trait for consistent money conversion
     */
    protected function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->centsToBRL($value),
            set: fn ($value) => $this->brlToCents($value),
        );
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
