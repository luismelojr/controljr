<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when there's an error with Transaction operations
 */
class TransactionException extends Exception
{
    /**
     * Exception for when trying to mark a transaction as paid with invalid data
     */
    public static function invalidPaymentDate(): self
    {
        return new self('A data de pagamento é inválida.');
    }

    /**
     * Exception for when trying to pay a transaction that's already paid
     */
    public static function alreadyPaid(): self
    {
        return new self('Esta transação já foi marcada como paga.');
    }

    /**
     * Exception for when trying to unpay a transaction that's not paid
     */
    public static function notPaid(): self
    {
        return new self('Esta transação não está marcada como paga.');
    }

    /**
     * Exception for when the transaction amount is invalid
     */
    public static function invalidAmount(): self
    {
        return new self('O valor da transação deve ser maior que zero.');
    }

    /**
     * Exception for when trying to modify a reconciled transaction
     */
    public static function isReconciled(): self
    {
        return new self('Não é possível modificar uma transação que já foi reconciliada.');
    }
}
