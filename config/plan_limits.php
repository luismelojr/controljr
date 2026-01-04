<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plan Feature Limits
    |--------------------------------------------------------------------------
    |
    | Define feature limits for each subscription plan.
    | Use -1 for unlimited, 0 for disabled, or a positive number for limit.
    |
    | Available features:
    | - max_wallets: Maximum number of wallets (bank accounts, credit cards)
    | - max_categories: Maximum number of categories
    | - max_accounts: Maximum number of recurring accounts
    | - max_transactions_per_month: Maximum transactions per month
    | - max_budgets: Maximum number of budgets
    | - max_alerts: Maximum number of alerts
    | - financial_reports: Access to financial reports
    | - data_export: Access to data export (CSV/Excel)
    | - bank_reconciliation: Access to bank reconciliation feature
    | - multi_currency: Support for multiple currencies
    | - api_access: Access to API
    | - priority_support: Priority customer support
    | - max_team_members: Maximum team members (for family plan)
    |
    */

    'free' => [
        'max_wallets' => 1,
        'max_categories' => 10,
        'max_accounts' => 5,
        'max_transactions_per_month' => 50,
        'max_budgets' => 5,
        'max_alerts' => 2,
        'financial_reports' => false,
        'data_export' => false,
        'bank_reconciliation' => false,
        'multi_currency' => false,
        'api_access' => false,
        'priority_support' => false,
        'max_team_members' => 1,
        'max_tags' => 0,
        'max_attachments' => 0,
        'max_savings_goals' => 0,
        'max_custom_reports' => 0,
        'max_exports_per_month' => 5,
        'transactions_history_months' => 12,
        'ai_predictions' => false,
    ],

    'premium' => [
        'max_wallets' => -1, // Unlimited
        'max_categories' => -1, // Unlimited
        'max_accounts' => 30,
        'max_transactions_per_month' => -1, // Unlimited
        'max_budgets' => -1, // Unlimited
        'max_alerts' => 10,
        'financial_reports' => true,
        'data_export' => true,
        'bank_reconciliation' => true,
        'multi_currency' => true,
        'api_access' => false,
        'priority_support' => true,
        'max_team_members' => 1,
        'max_tags' => -1, // Unlimited
        'max_attachments' => 100,
        'max_savings_goals' => 20,
        'max_custom_reports' => 50,
        'max_exports_per_month' => -1, // Unlimited
        'transactions_history_months' => -1, // Unlimited
        'ai_predictions' => true,
    ],

    'family' => [
        'max_wallets' => -1, // Unlimited
        'max_categories' => -1, // Unlimited
        'max_accounts' => -1, // Unlimited
        'max_transactions_per_month' => -1, // Unlimited
        'max_budgets' => -1, // Unlimited
        'max_alerts' => -1, // Unlimited
        'financial_reports' => true,
        'data_export' => true,
        'bank_reconciliation' => true,
        'multi_currency' => true,
        'api_access' => true,
        'priority_support' => true,
        'max_team_members' => 5,
        'max_tags' => -1, // Unlimited
        'max_attachments' => 500,
        'max_savings_goals' => -1, // Unlimited
        'max_custom_reports' => -1, // Unlimited
        'max_exports_per_month' => -1, // Unlimited
        'transactions_history_months' => -1, // Unlimited
        'ai_predictions' => true,
    ],
];
