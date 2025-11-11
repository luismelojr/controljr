<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Alert extends Model
{
    use HasFactory, HasUuidCustom;

    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'alertable_type',
        'alertable_id',
        'trigger_value',
        'trigger_days',
        'notification_channels',
        'is_active',
        'last_triggered_at',
    ];

    protected function casts(): array
    {
        return [
            'trigger_days' => 'array',
            'notification_channels' => 'array',
            'is_active' => 'boolean',
            'trigger_value' => 'decimal:2',
            'last_triggered_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent alertable model (CreditCard, Account, Bill, etc).
     */
    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the notifications for this alert.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(AlertNotification::class);
    }

    /**
     * Scope a query to only include active alerts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
