export type WalletType = 'card_credit' | 'bank_account' | 'other';

export interface WalletFormDataInterface {
    name: string;
    type: WalletType | '';
    day_close: string;
    best_shopping_day: string;
    card_limit: number;
}

export interface WalletInterface {
    uuid: string;
    name: string;
    type: 'card_credit' | 'bank_account' | 'other';
    type_label: string;
    is_credit_card: boolean;
    status: boolean;
    day_close?: number;
    best_shopping_day?: number;
    card_limit?: number;
    card_limit_used?: number;
    card_limit_available?: number;
    card_limit_percentage_used?: number;
    initial_balance: number;
    balance_incomes?: number;
    balance_expenses?: number;
    balance_available?: number;
    balance_percentage_used?: number;
    created_at: string;
    updated_at: string;
}
