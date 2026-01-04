<?php

namespace App\Models;

use App\Enums\PlanTypeEnum;
use App\Traits\HasMoneyAccessors;
use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory, HasUuidCustom, HasMoneyAccessors;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'price_cents',
        'billing_period',
        'features',
        'description',
        'is_active',
        'max_users',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price_cents' => 'integer',
        'max_users' => 'integer',
    ];

    /**
     * Interact with the plan's price.
     * Uses HasMoneyAccessors trait for consistent money conversion
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->centsToBRL($this->price_cents),
        );
    }

    /**
     * Get formatted price as BRL string
     */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatCentsAsBRL($this->price_cents),
        );
    }

    /**
     * Get plan type enum
     */
    protected function planType(): Attribute
    {
        return Attribute::make(
            get: fn () => PlanTypeEnum::tryFrom($this->slug),
        );
    }

    /**
     * Check if plan is free
     */
    public function isFree(): bool
    {
        return $this->slug === PlanTypeEnum::FREE->value;
    }

    /**
     * Check if plan is premium
     */
    public function isPremium(): bool
    {
        return $this->slug === PlanTypeEnum::PREMIUM->value;
    }

    /**
     * Check if plan is family
     */
    public function isFamily(): bool
    {
        return $this->slug === PlanTypeEnum::FAMILY->value;
    }

    /**
     * Get feature limit
     */
    public function getFeatureLimit(string $feature): int|bool
    {
        return $this->features[$feature] ?? false;
    }

    /**
     * Get amount in reais (BRL) for Asaas API
     * Asaas expects decimal value (e.g., 39.90), not cents
     */
    public function getAmountInReais(): float
    {
        return $this->price_cents / 100;
    }

    /**
     * Relationships
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeFree($query)
    {
        return $query->where('slug', PlanTypeEnum::FREE->value);
    }

    public function scopePremium($query)
    {
        return $query->where('slug', PlanTypeEnum::PREMIUM->value);
    }

    public function scopeFamily($query)
    {
        return $query->where('slug', PlanTypeEnum::FAMILY->value);
    }
}
