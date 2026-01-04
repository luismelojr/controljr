import { Category } from './category';
import { IncomeTransaction } from './income-transaction';
import { WalletInterface } from './wallet';

export type IncomeRecurrenceType = 'one_time' | 'installments' | 'recurring';
export type IncomeStatus = 'active' | 'completed' | 'cancelled';

/**
 * Income Resource from Laravel API
 */
export interface Income {
    uuid: string;
    name: string;
    notes?: string;
    total_amount: number;
    recurrence_type: IncomeRecurrenceType;
    recurrence_type_label: string;
    installments?: number;
    start_date: string;
    status: IncomeStatus;
    status_label: string;
    wallet_id?: number;
    category?: Category;
    wallet?: WalletInterface;
    incomeTransactions?: IncomeTransaction[];
    transactions_count?: number;
    received_transactions_count?: number;
    created_at: string;
    updated_at: string;
    // Adjusting based on standard resource type structure
    tags?: Array<{
        id: number;
        name: string;
        color: string;
    }>;
}

/**
 * Form data for creating an income
 */
export interface IncomeFormData {
    wallet_id: string;
    category_id: string;
    name: string;
    notes: string;
    total_amount: number | '';
    recurrence_type: IncomeRecurrenceType | '';
    installments: number | '';
    start_date: string;
}
