<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asaas API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Asaas payment gateway integration
    |
    */

    'api_key' => env('ASAAS_API_KEY'),

    'environment' => env('ASAAS_ENVIRONMENT', 'sandbox'),

    'webhook_token' => env('ASAAS_WEBHOOK_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    */

    'api_url' => env('ASAAS_ENVIRONMENT', 'sandbox') === 'production'
        ? 'https://api.asaas.com/v3'
        : 'https://sandbox.asaas.com/api/v3',

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */

    'payment_methods' => [
        'pix' => true,
        'boleto' => true,
        'credit_card' => true,
    ],

    'boleto_due_days' => 3, // Dias até vencimento do boleto
    'pix_expiration_minutes' => 30, // Minutos até expiração do PIX

    /*
    |--------------------------------------------------------------------------
    | Webhook Events
    |--------------------------------------------------------------------------
    */

    'webhook_events' => [
        'PAYMENT_CREATED',
        'PAYMENT_UPDATED',
        'PAYMENT_CONFIRMED',
        'PAYMENT_RECEIVED',
        'PAYMENT_OVERDUE',
        'PAYMENT_DELETED',
        'PAYMENT_RESTORED',
        'PAYMENT_REFUNDED',
        'PAYMENT_RECEIVED_IN_CASH',
        'PAYMENT_CHARGEBACK_REQUESTED',
        'PAYMENT_CHARGEBACK_DISPUTE',
        'PAYMENT_AWAITING_CHARGEBACK_REVERSAL',
        'PAYMENT_DUNNING_RECEIVED',
        'PAYMENT_DUNNING_REQUESTED',
        'PAYMENT_BANK_SLIP_VIEWED',
        'PAYMENT_CHECKOUT_VIEWED',
    ],
];
