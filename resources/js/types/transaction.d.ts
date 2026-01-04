import { Account } from './account';
import { Category } from './category';
import { WalletInterface } from './wallet';

export type TransactionStatus = 'pending' | 'paid' | 'overdue';

/**
 * Transaction Resource from Laravel API
 */
export interface Transaction {
    uuid: string;
    amount: number;
    due_date: string;
    paid_at?: string;
    installment_number?: number;
    total_installments?: number;
    installment_label?: string;
    status: TransactionStatus;
    status_label: string;
    is_paid: boolean;
    is_overdue: boolean;
    account?: Account;
    wallet?: WalletInterface;
    category?: Category;
    tags?: Array<{
        id: number;
        name: string;
        color: string;
    }>;
    created_at: string;
    updated_at: string;
}

/**
 * Monthly transaction summary
 */
export interface TransactionSummary {
    total_spent: number;
    total_pending: number;
    total_overdue: number;
    total_expected: number;
    transactions_count: number;
    paid_count: number;
    pending_count: number;
    overdue_count: number;
}
