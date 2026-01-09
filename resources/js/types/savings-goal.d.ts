export interface SavingsGoal {
    id: number;
    uuid: string;
    user_id: number;
    category_id?: number | null;
    name: string;
    description?: string;
    target_amount_cents: number;
    current_amount_cents: number;
    target_date?: string;
    icon: string;
    color: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    // Accessors
    progress_percentage?: number;
    remaining_amount?: number;
    days_remaining?: number;
    remaining_amount_formatted?: string;
    category?: Category;
}
