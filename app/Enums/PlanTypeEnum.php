<?php

namespace App\Enums;

enum PlanTypeEnum: string
{
    case FREE = 'free';
    case PREMIUM = 'premium';

    /**
     * Get the Portuguese label for the plan type
     */
    public function label(): string
    {
        return match ($this) {
            self::FREE => 'Gratuito',
            self::PREMIUM => 'Premium',
        };
    }

    /**
     * Get the description for the plan
     */
    public function description(): string
    {
        return match ($this) {
            self::FREE => 'Plano gratuito com recursos básicos',
            self::PREMIUM => 'Plano premium com recursos avançados',
        };
    }

    /**
     * Get all plan types as array
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
