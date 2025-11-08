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
    status: boolean;
    day_close?: number;
    best_shopping_day?: number;
    card_limit?: number;
    card_limit_used?: number;
}
