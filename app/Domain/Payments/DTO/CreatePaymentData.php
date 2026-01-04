<?php

namespace App\Domain\Payments\DTO;

use Spatie\LaravelData\Data;

class CreatePaymentData extends Data
{
    public function __construct(
        public int $user_id,
        public ?int $subscription_id,
        public int $amount_cents,
        public string $payment_method, // pix, boleto, credit_card
        public ?string $due_date = null,
        public ?string $description = null,
        public ?array $credit_card_data = null, // Para cartão de crédito
    ) {
    }

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'subscription_id' => ['nullable', 'exists:subscriptions,id'],
            'amount_cents' => ['required', 'integer', 'min:100'], // Mínimo R$ 1,00
            'payment_method' => ['required', 'in:pix,boleto,credit_card'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'description' => ['nullable', 'string', 'max:500'],
            'credit_card_data' => ['nullable', 'array'],
            'credit_card_data.holder_name' => ['required_if:payment_method,credit_card', 'string'],
            'credit_card_data.number' => ['required_if:payment_method,credit_card', 'string'],
            'credit_card_data.expiry_month' => ['required_if:payment_method,credit_card', 'string', 'size:2'],
            'credit_card_data.expiry_year' => ['required_if:payment_method,credit_card', 'string', 'size:4'],
            'credit_card_data.cvv' => ['required_if:payment_method,credit_card', 'string', 'size:3'],
        ];
    }

    public function getAmountInReais(): float
    {
        return $this->amount_cents / 100;
    }
}
