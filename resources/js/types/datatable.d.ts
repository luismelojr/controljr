import { ReactNode } from 'react';

/**
 * Laravel Pagination Meta Information
 */
export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

/**
 * Laravel Pagination Links
 */
export interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

/**
 * Laravel Paginated Response Structure
 */
export interface PaginatedResponse<T> {
    data: T[];
    links: PaginationLinks;
    meta: PaginationMeta;
}

/**
 * Filter types supported by Spatie Query Builder
 */
export type FilterType = 'text' | 'select' | 'boolean' | 'date' | 'number';

/**
 * Filter configuration for DataTable
 */
export interface FilterConfig {
    key: string;
    label: string;
    type: FilterType;
    placeholder?: string;
    options?: Array<{
        value: string | number | boolean;
        label: string;
    }>;
}

/**
 * Sort configuration for columns
 */
export interface SortConfig {
    key: string;
    direction: 'asc' | 'desc' | null;
}

/**
 * Active filters with values
 */
export interface ActiveFilters {
    [key: string]: string | number | boolean | null;
}

/**
 * Column definition for DataTable
 */
export interface ColumnDef<T> {
    key: string;
    label: string;
    sortable?: boolean;
    sortKey?: string; // Custom sort key (for aliased fields)
    render?: (item: T) => ReactNode;
    className?: string;
}

/**
 * Action button configuration
 */
export interface ActionButton {
    label: string;
    onClick: () => void;
    icon?: ReactNode;
    variant?: 'default' | 'outline' | 'destructive' | 'secondary' | 'ghost' | 'link';
}

/**
 * DataTable Props
 */
export interface DataTableProps<T> {
    data: T[];
    columns: ColumnDef<T>[];
    pagination?: PaginationMeta;
    paginationLinks?: PaginationLinks;
    filters?: FilterConfig[];
    activeFilters?: ActiveFilters;
    activeSort?: SortConfig;
    onFilterChange?: (filters: ActiveFilters) => void;
    onSortChange?: (sort: SortConfig) => void;
    onPageChange?: (url: string) => void;
    actions?: ActionButton[];
    emptyState?: ReactNode;
    loading?: boolean;
}
