<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when there's an error with Budget operations
 */
class BudgetException extends Exception
{
    /**
     * Exception for when a budget already exists for a category/period combination
     */
    public static function alreadyExists(): self
    {
        return new self('Já existe um orçamento para esta categoria neste período.');
    }

    /**
     * Exception for when trying to set an invalid budget amount
     */
    public static function invalidAmount(): self
    {
        return new self('O valor do orçamento deve ser maior que zero.');
    }

    /**
     * Exception for when the budget period is invalid
     */
    public static function invalidPeriod(): self
    {
        return new self('O período do orçamento é inválido.');
    }

    /**
     * Exception for when trying to access a budget that doesn't belong to the user
     */
    public static function unauthorized(): self
    {
        return new self('Você não tem permissão para acessar este orçamento.');
    }
}
