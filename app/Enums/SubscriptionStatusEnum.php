<?php

namespace App\Enums;

enum SubscriptionStatusEnum: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case PENDING = 'pending';

    /**
     * Get the Portuguese label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativa',
            self::CANCELLED => 'Cancelada',
            self::EXPIRED => 'Expirada',
            self::PENDING => 'Pendente',
        };
    }

    /**
     * Get the color for the status badge
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::CANCELLED => 'destructive',
            self::EXPIRED => 'secondary',
            self::PENDING => 'warning',
        };
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Get all statuses as array
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
