export interface Budget {
    id: string;
    category: string;
    category_id: number;
    amount: number;
    spent: number;
    remaining: number;
    percentage: number;
    status: 'green' | 'yellow' | 'red';
    tags?: Array<{
        id: number;
        name: string;
        color: string;
    }>;
}

export interface CreateBudgetForm {
    category_id: string;
    amount: string;
    period: string;
    recurrence: 'monthly' | 'once';
}
