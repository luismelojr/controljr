<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when there's an error with Category operations
 */
class CategoryException extends Exception
{
    /**
     * Exception for when trying to modify a default category
     */
    public static function cannotModifyDefault(): self
    {
        return new self('Categorias padrão não podem ser editadas.');
    }

    /**
     * Exception for when trying to delete a category with associated transactions
     */
    public static function hasTransactions(): self
    {
        return new self('Não é possível excluir uma categoria que possui transações associadas.');
    }

    /**
     * Exception for when trying to delete a category with associated budgets
     */
    public static function hasBudgets(): self
    {
        return new self('Não é possível excluir uma categoria que possui orçamentos associados.');
    }
}
