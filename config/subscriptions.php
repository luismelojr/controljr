<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Grace Period
    |--------------------------------------------------------------------------
    |
    | Number of days to give users after a payment failure before
    | cancelling their subscription. During this period, users
    | can update their payment method and retry payment.
    |
    */
    'grace_period_days' => env('SUBSCRIPTION_GRACE_PERIOD_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Maximum Failed Payment Attempts
    |--------------------------------------------------------------------------
    |
    | Maximum number of consecutive payment failures allowed before
    | taking action. Set to 0 to disable this limit.
    |
    */
    'max_failed_payments' => env('SUBSCRIPTION_MAX_FAILED_PAYMENTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Subscription Features
    |--------------------------------------------------------------------------
    |
    | Define features available for each subscription plan.
    |
    */
    'features' => [
        'free' => [
            'max_transactions' => 50,
            'max_budgets' => 3,
            'advanced_reports' => false,
            'export_data' => false,
            'priority_support' => false,
        ],
        'premium' => [
            'max_transactions' => 500,
            'max_budgets' => 10,
            'advanced_reports' => true,
            'export_data' => true,
            'priority_support' => false,
        ],
        'family' => [
            'max_transactions' => null, // unlimited
            'max_budgets' => null, // unlimited
            'advanced_reports' => true,
            'export_data' => true,
            'priority_support' => true,
        ],
    ],
];
