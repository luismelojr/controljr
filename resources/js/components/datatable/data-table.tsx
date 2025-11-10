import { ColumnDef, SortConfig } from '@/types/datatable';
import { router } from '@inertiajs/react';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-react';
import { ReactNode } from 'react';
import { cn } from '@/lib/utils';

interface DataTableProps<T> {
    data: T[];
    columns: ColumnDef<T>[];
    activeSort?: SortConfig;
    currentFilters?: Record<string, any>;
    emptyState?: ReactNode;
    loading?: boolean;
}

/**
 * Main DataTable component
 * Displays data in a sortable table format
 * Integrates directly with Inertia router and Spatie Query Builder
 */
export function DataTable<T extends Record<string, any>>({
    data,
    columns,
    activeSort,
    currentFilters = {},
    emptyState,
    loading = false,
}: DataTableProps<T>) {
    // Robust validation
    const safeData = Array.isArray(data) ? data : [];
    const safeColumns = Array.isArray(columns) ? columns : [];
    const safeCurrentFilters = currentFilters && typeof currentFilters === 'object' && !Array.isArray(currentFilters) ? currentFilters : {};

    /**
     * Handle column sort
     * Toggles between asc, desc, and null
     */
    const handleSort = (column: ColumnDef<T>) => {
        if (!column.sortable) return;

        const sortKey = column.sortKey || column.key;
        let newDirection: 'asc' | 'desc' | null = 'asc';

        // Determine new sort direction
        if (activeSort?.key === sortKey) {
            if (activeSort.direction === 'asc') {
                newDirection = 'desc';
            } else if (activeSort.direction === 'desc') {
                newDirection = null;
            }
        }

        const params: Record<string, any> = {};

        // Add current filters
        if (Object.keys(safeCurrentFilters).length > 0) {
            params.filter = safeCurrentFilters;
        }

        // Add sort if not null
        if (newDirection) {
            const sortValue = newDirection === 'desc' ? `-${sortKey}` : sortKey;
            params.sort = sortValue;
        }

        router.get(window.location.pathname, params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    /**
     * Get sort icon for column
     */
    const getSortIcon = (column: ColumnDef<T>) => {
        if (!column.sortable) return null;

        const sortKey = column.sortKey || column.key;
        const isActive = activeSort?.key === sortKey;

        if (!isActive) {
            return <ArrowUpDown className="ml-2 h-4 w-4 text-muted-foreground" />;
        }

        if (activeSort?.direction === 'asc') {
            return <ArrowUp className="ml-2 h-4 w-4" />;
        }

        if (activeSort?.direction === 'desc') {
            return <ArrowDown className="ml-2 h-4 w-4" />;
        }

        return <ArrowUpDown className="ml-2 h-4 w-4 text-muted-foreground" />;
    };

    // Empty state
    if (safeData.length === 0 && !loading) {
        return (
            <div className="rounded-lg border bg-card">
                {emptyState || (
                    <div className="flex min-h-[400px] flex-col items-center justify-center p-8 text-center">
                        <div className="rounded-full bg-muted p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="h-6 w-6 text-muted-foreground"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                />
                            </svg>
                        </div>
                        <h3 className="mt-4 text-lg font-semibold">Nenhum registro encontrado</h3>
                        <p className="mt-2 text-sm text-muted-foreground">
                            {Object.keys(safeCurrentFilters).length > 0
                                ? 'Tente ajustar os filtros para encontrar o que procura.'
                                : 'Comece criando seu primeiro registro.'}
                        </p>
                    </div>
                )}
            </div>
        );
    }

    return (
        <div className="rounded-lg border bg-card">
            <div className="overflow-x-auto">
                <table className="w-full border-collapse">
                    {/* Table header */}
                    <thead>
                        <tr className="border-b bg-muted/50">
                            {safeColumns.map((column) => (
                                <th
                                    key={column.key}
                                    className={cn(
                                        'px-4 py-3 text-left text-sm font-medium text-muted-foreground',
                                        column.sortable && 'cursor-pointer select-none hover:text-foreground',
                                        column.className
                                    )}
                                    onClick={() => column.sortable && handleSort(column)}
                                >
                                    <div className="flex items-center">
                                        {column.label}
                                        {getSortIcon(column)}
                                    </div>
                                </th>
                            ))}
                        </tr>
                    </thead>

                    {/* Table body */}
                    <tbody>
                        {loading ? (
                            // Loading skeleton
                            Array.from({ length: 5 }).map((_, index) => (
                                <tr key={index} className="border-b">
                                    {safeColumns.map((column) => (
                                        <td key={column.key} className="px-4 py-3">
                                            <div className="h-4 w-full animate-pulse rounded bg-muted" />
                                        </td>
                                    ))}
                                </tr>
                            ))
                        ) : (
                            // Data rows
                            safeData.map((item, rowIndex) => (
                                <tr key={item.uuid || item.id || rowIndex} className="border-b transition-colors hover:bg-muted/50">
                                    {safeColumns.map((column) => (
                                        <td key={column.key} className={cn('px-4 py-3 text-sm', column.className)}>
                                            {column.render ? column.render(item) : item[column.key]}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
