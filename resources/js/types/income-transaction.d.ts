import { Category } from './category';
import { Income } from './income';

export type IncomeTransactionStatus = 'pending' | 'received' | 'overdue';

/**
 * IncomeTransaction Resource from Laravel API
 */
export interface IncomeTransaction {
    uuid: string;
    amount: number;
    month_reference: string;
    expected_date: string;
    received_at?: string;
    installment_number?: number;
    total_installments?: number;
    installment_label?: string;
    status: IncomeTransactionStatus;
    status_label: string;
    is_received: boolean;
    is_overdue: boolean;
    income?: Income;
    category?: Category;
    created_at: string;
    updated_at: string;
}

/**
 * Monthly income transaction summary
 */
export interface IncomeTransactionSummary {
    total_received: number;
    total_pending: number;
    total_overdue: number;
    total_expected: number;
    transactions_count: number;
    received_count: number;
    pending_count: number;
    overdue_count: number;
}
