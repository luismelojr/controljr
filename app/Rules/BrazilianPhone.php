<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BrazilianPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $value);

        // Check if the phone number has the correct length (10 or 11 digits)
        if (strlen($phone) < 10 || strlen($phone) > 11) {
            $fail('O :attribute deve ter 10 ou 11 dígitos.');
            return;
        }

        // Extract area code (first 2 digits)
        $areaCode = substr($phone, 0, 2);

        // Valid Brazilian area codes
        $validAreaCodes = [
            '11', '12', '13', '14', '15', '16', '17', '18', '19', // São Paulo
            '21', '22', '24', // Rio de Janeiro
            '27', '28', // Espírito Santo
            '31', '32', '33', '34', '35', '37', '38', // Minas Gerais
            '41', '42', '43', '44', '45', '46', // Paraná
            '47', '48', '49', // Santa Catarina
            '51', '53', '54', '55', // Rio Grande do Sul
            '61', // Distrito Federal
            '62', '64', // Goiás
            '63', // Tocantins
            '65', '66', // Mato Grosso
            '67', // Mato Grosso do Sul
            '68', // Acre
            '69', // Rondônia
            '71', '73', '74', '75', '77', // Bahia
            '79', // Sergipe
            '81', '87', // Pernambuco
            '82', // Alagoas
            '83', // Paraíba
            '84', // Rio Grande do Norte
            '85', '88', // Ceará
            '86', '89', // Piauí
            '91', '93', '94', // Pará
            '92', '97', // Amazonas
            '95', // Roraima
            '96', // Amapá
            '98', '99', // Maranhão
        ];

        // Check if area code is valid
        if (!in_array($areaCode, $validAreaCodes)) {
            $fail('O :attribute deve conter um código de área válido.');
            return;
        }

        // For 11 digits, check if it's a mobile number (9th digit should be 9)
        if (strlen($phone) === 11) {
            $ninthDigit = substr($phone, 2, 1);
            if ($ninthDigit !== '9') {
                $fail('O :attribute deve ter o nono dígito 9 para números de celular.');
                return;
            }
        }

        // For 10 digits, check if it's a landline (3rd digit should be 2-5 or 7-9)
        if (strlen($phone) === 10) {
            $thirdDigit = substr($phone, 2, 1);
            if (!in_array($thirdDigit, ['2', '3', '4', '5', '7', '8', '9'])) {
                $fail('O :attribute deve ter um formato válido para telefone fixo.');
                return;
            }
        }
    }
}
