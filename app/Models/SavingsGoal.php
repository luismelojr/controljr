<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use App\Traits\HasMoneyAccessors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingsGoal extends Model
{
    use HasFactory, HasUuidCustom, HasMoneyAccessors;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'name',
        'description',
        'target_amount_cents',
        'current_amount_cents',
        'target_date',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'target_amount_cents' => 'integer',
        'current_amount_cents' => 'integer',
        'target_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Accessors
    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->target_amount_cents > 0
                ? min(100, round(($this->current_amount_cents / $this->target_amount_cents) * 100, 2))
                : 0,
        );
    }

    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->target_amount_cents - $this->current_amount_cents),
        );
    }

    protected function daysRemaining(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->target_date ? now()->diffInDays($this->target_date, false) : null,
        );
    }

    // Methods
    public function addProgress(int $amountCents): self
    {
        $this->increment('current_amount_cents', $amountCents);

        if ($this->current_amount_cents >= $this->target_amount_cents) {
            $this->complete();
        }

        return $this->fresh();
    }

    public function removeProgress(int $amountCents): self
    {
        $this->decrement('current_amount_cents', $amountCents);
        return $this->fresh();
    }

    public function complete(): self
    {
        $this->update(['is_active' => false]);
        return $this->fresh();
    }

    // Cleaning up target amount when setting
    protected function targetAmountCents(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => (int) $value,
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_active', false);
    }
}
