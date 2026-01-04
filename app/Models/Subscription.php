<?php

namespace App\Models;

use App\Enums\SubscriptionStatusEnum;
use App\Traits\HasUuidCustom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory, HasUuidCustom;

    protected $fillable = [
        'uuid',
        'user_id',
        'subscription_plan_id',
        'started_at',
        'ends_at',
        'cancelled_at',
        'status',
        'payment_gateway',
        'external_subscription_id',
        'external_customer_id',
        'failed_payments_count',
        'last_payment_failed_at',
        'payment_grace_period_ends_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_payment_failed_at' => 'datetime',
        'payment_grace_period_ends_at' => 'datetime',
    ];

    /**
     * Get status enum
     */
    protected function statusEnum(): Attribute
    {
        return Attribute::make(
            get: fn () => SubscriptionStatusEnum::tryFrom($this->status),
        );
    }

    /**
     * Get status label
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->statusEnum?->label() ?? $this->status,
        );
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === SubscriptionStatusEnum::ACTIVE->value
            && ($this->ends_at === null || $this->ends_at->isFuture())
            && $this->cancelled_at === null;
    }

    /**
     * Check if subscription is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === SubscriptionStatusEnum::CANCELLED->value
            || $this->cancelled_at !== null;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->status === SubscriptionStatusEnum::EXPIRED->value
            || ($this->ends_at !== null && $this->ends_at->isPast());
    }

    /**
     * Check if subscription is pending
     */
    public function isPending(): bool
    {
        return $this->status === SubscriptionStatusEnum::PENDING->value;
    }

    /**
     * Check if subscription is on grace period
     */
    public function onGracePeriod(): bool
    {
        return $this->ends_at !== null
            && $this->ends_at->isFuture()
            && $this->cancelled_at !== null;
    }

    /**
     * Get days remaining
     */
    public function daysRemaining(): int
    {
        if ($this->ends_at === null) {
            return 0;
        }

        return max(0, Carbon::now()->diffInDays($this->ends_at, false));
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->update([
            'status' => SubscriptionStatusEnum::CANCELLED->value,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Renew subscription
     */
    public function renew(Carbon $endsAt = null): void
    {
        $this->update([
            'status' => SubscriptionStatusEnum::ACTIVE->value,
            'ends_at' => $endsAt ?? now()->addMonth(),
            'cancelled_at' => null,
        ]);
    }

    /**
     * Resume cancelled subscription
     */
    public function resume(): void
    {
        if (! $this->isCancelled()) {
            return;
        }

        $this->update([
            'status' => SubscriptionStatusEnum::ACTIVE->value,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => SubscriptionStatusEnum::EXPIRED->value,
        ]);
    }

    /**
     * Mark payment as failed and start grace period
     */
    public function markPaymentAsFailed(int $gracePeriodDays = 7): void
    {
        $this->increment('failed_payments_count');

        $this->update([
            'status' => SubscriptionStatusEnum::PAYMENT_FAILED->value,
            'last_payment_failed_at' => now(),
            'payment_grace_period_ends_at' => now()->addDays($gracePeriodDays),
        ]);
    }

    /**
     * Reset payment failure tracking
     */
    public function resetPaymentFailures(): void
    {
        $this->update([
            'failed_payments_count' => 0,
            'last_payment_failed_at' => null,
            'payment_grace_period_ends_at' => null,
        ]);
    }

    /**
     * Check if subscription is in payment grace period
     */
    public function inPaymentGracePeriod(): bool
    {
        return $this->status === SubscriptionStatusEnum::PAYMENT_FAILED->value
            && $this->payment_grace_period_ends_at !== null
            && $this->payment_grace_period_ends_at->isFuture();
    }

    /**
     * Check if payment grace period has expired
     */
    public function paymentGracePeriodExpired(): bool
    {
        return $this->status === SubscriptionStatusEnum::PAYMENT_FAILED->value
            && $this->payment_grace_period_ends_at !== null
            && $this->payment_grace_period_ends_at->isPast();
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->whereNull('cancelled_at');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', SubscriptionStatusEnum::CANCELLED->value)
            ->orWhereNotNull('cancelled_at');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', SubscriptionStatusEnum::EXPIRED->value)
            ->orWhere(function ($q) {
                $q->whereNotNull('ends_at')
                    ->where('ends_at', '<=', now());
            });
    }

    public function scopePending($query)
    {
        return $query->where('status', SubscriptionStatusEnum::PENDING->value);
    }

    public function scopeOnGracePeriod($query)
    {
        return $query->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->whereNotNull('cancelled_at');
    }
}
