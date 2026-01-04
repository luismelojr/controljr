<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use App\Traits\HasMoneyAccessors;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory, HasUuidCustom, SoftDeletes, HasMoneyAccessors;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'amount',
        'period',
        'recurrence',
        'status',
    ];

    protected $casts = [
        'period' => 'date',
        'status' => 'boolean',
    ];

    /**
     * Interact with the budget's amount.
     * Uses HasMoneyAccessors trait for consistent money conversion
     */
    protected function amount(): Attribute
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
