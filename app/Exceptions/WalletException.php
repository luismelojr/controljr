<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when there's an error with Wallet operations
 */
class WalletException extends Exception
{
    /**
     * Exception for when trying to use a credit card with insufficient limit
     */
    public static function insufficientLimit(): self
    {
        return new self('Limite do cartão de crédito insuficiente.');
    }

    /**
     * Exception for when trying to modify a wallet that has transactions
     */
    public static function hasTransactions(): self
    {
        return new self('Não é possível modificar uma carteira que possui transações associadas.');
    }

    /**
     * Exception for when the wallet type is invalid
     */
    public static function invalidType(): self
    {
        return new self('Tipo de carteira inválido.');
    }

    /**
     * Exception for when trying to set a negative card limit
     */
    public static function negativeLimit(): self
    {
        return new self('O limite do cartão não pode ser negativo.');
    }
}
