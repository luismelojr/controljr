<?php

namespace App\Traits;

/**
 * Trait for handling money conversions between cents (int) and BRL (float)
 *
 * This trait provides consistent methods for converting monetary values
 * stored as integers (cents) to decimal values (BRL) and vice versa.
 *
 * All monetary values should be stored as integers in the database to
 * avoid floating-point arithmetic errors.
 */
trait HasMoneyAccessors
{
    /**
     * Convert cents (integer) to BRL (float)
     *
     * @param int $cents Amount in cents
     * @return float Amount in BRL with 2 decimal places
     */
    protected function centsToBRL(int $cents): float
    {
        return round($cents / 100, 2);
    }

    /**
     * Convert BRL (float) to cents (integer)
     *
     * @param float $brl Amount in BRL
     * @return int Amount in cents
     */
    protected function brlToCents(float $brl): int
    {
        return (int) round($brl * 100);
    }

    /**
     * Format cents as BRL currency string
     *
     * @param int $cents Amount in cents
     * @return string Formatted currency (e.g., "R$ 1.234,56")
     */
    protected function formatCentsAsBRL(int $cents): string
    {
        $brl = $this->centsToBRL($cents);

        return 'R$ '.number_format($brl, 2, ',', '.');
    }

    /**
     * Get accessor for converting cents to BRL
     * Use this in model attribute casts
     *
     * @param int|null $value
     * @return float|null
     */
    protected function getCentsAttribute(?int $value): ?float
    {
        return $value !== null ? $this->centsToBRL($value) : null;
    }

    /**
     * Set accessor for converting BRL to cents
     * Use this in model attribute casts
     *
     * @param float|null $value
     * @return int|null
     */
    protected function setCentsAttribute(?float $value): ?int
    {
        return $value !== null ? $this->brlToCents($value) : null;
    }
}
