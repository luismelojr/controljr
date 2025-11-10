/**
 * Category Resource from Laravel API
 */
export interface Category {
    uuid: string;
    name: string;
    is_default: boolean;
    status: boolean;
    can_edit: boolean;
    can_delete: boolean;
    created_at: string;
    updated_at: string;
}
