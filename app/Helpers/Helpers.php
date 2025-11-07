<?php

namespace App\Helpers;

class Helpers
{
    public static function formatStringRemoveCharactersSpecial(string $string): string
    {
        if ($string == '') return $string;

        return preg_replace('/\D/', '', $string);
    }

    public static function formatPhone(string $number): string
    {
        $digits = preg_replace('/\D/', '', $number);

        if (strlen($digits) === 11) {
            return sprintf("(%s) %s %s-%s",
                substr($digits, 0, 2),  // DDD
                substr($digits, 2, 1),  // dígito 9
                substr($digits, 3, 4),  // primeira parte
                substr($digits, 7)      // segunda parte
            );
        }

        if (strlen($digits) === 10) {
            return sprintf("(%s) %s-%s",
                substr($digits, 0, 2),  // DDD
                substr($digits, 2, 4),  // primeira parte
                substr($digits, 6)      // segunda parte
            );
        }

        return $number;
    }
}
