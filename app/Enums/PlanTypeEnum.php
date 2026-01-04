<?php

namespace App\Enums;

enum PlanTypeEnum: string
{
    case FREE = 'free';
    case PREMIUM = 'premium';
    case FAMILY = 'family';

    /**
     * Get the Portuguese label for the plan type
     */
    public function label(): string
    {
        return match ($this) {
            self::FREE => 'Gratuito',
            self::PREMIUM => 'Premium',
            self::FAMILY => 'Família',
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
            self::FAMILY => 'Plano familiar para até 5 pessoas',
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
