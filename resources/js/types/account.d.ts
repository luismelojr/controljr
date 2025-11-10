import { Category } from './category';
import { Transaction } from './transaction';
import { WalletInterface } from './wallet';

export type RecurrenceType = 'one_time' | 'installments' | 'recurring';
export type AccountStatus = 'active' | 'completed' | 'cancelled';

/**
 * Account Resource from Laravel API
 */
export interface Account {
    uuid: string;
    name: string;
    description?: string;
    total_amount: number;
    recurrence_type: RecurrenceType;
    recurrence_type_label: string;
    installments?: number;
    start_date: string;
    status: AccountStatus;
    status_label: string;
    wallet?: WalletInterface;
    category?: Category;
    transactions?: Transaction[];
    transactions_count?: number;
    paid_transactions_count?: number;
    created_at: string;
    updated_at: string;
}

/**
 * Form data for creating an account
 */
export interface AccountFormData {
    wallet_id: string;
    category_id: string;
    name: string;
    description: string;
    total_amount: number | '';
    recurrence_type: RecurrenceType | '';
    installments: number | '';
    start_date: string;
}
