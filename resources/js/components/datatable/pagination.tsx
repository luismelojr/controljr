import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PaginationLinks, PaginationMeta } from '@/types/datatable';
import { router } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-react';

interface DataTablePaginationProps {
    meta: PaginationMeta;
    links: PaginationLinks;
}

/**
 * Pagination component for DataTable
 * Integrates directly with Laravel pagination and Inertia router
 */
export function DataTablePagination({ meta, links }: DataTablePaginationProps) {
    const { current_page, last_page, from, to, total, per_page } = meta;

    /**
     * Navigate to a specific page using Inertia router
     */
    const goToPage = (url: string | null) => {
        if (!url) return;

        router.get(
            url,
            {},
            {
                preserveState: true,
                preserveScroll: false, // Scroll to top on page change
            },
        );
    };

    /**
     * Change items per page
     */
    const changePerPage = (value: string) => {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.delete('page'); // Reset to first page

        router.get(
            url.toString(),
            {},
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    };

    return (
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            {/* Results info */}
            <div className="text-sm text-muted-foreground">
                Mostrando <span className="font-medium text-foreground">{from}</span> até <span className="font-medium text-foreground">{to}</span> de{' '}
                <span className="font-medium text-foreground">{total}</span> resultados
            </div>

            {/* Pagination controls */}
            <div className="flex items-center gap-2">
                {/* Per page selector */}
                <div className="flex items-center gap-2">
                    <span className="text-sm text-muted-foreground">Por página:</span>
                    <Select value={String(per_page)} onValueChange={changePerPage}>
                        <SelectTrigger className="h-8 w-[70px]">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="10">10</SelectItem>
                            <SelectItem value="15">15</SelectItem>
                            <SelectItem value="25">25</SelectItem>
                            <SelectItem value="50">50</SelectItem>
                            <SelectItem value="100">100</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                {/* Page info */}
                <div className="text-sm text-muted-foreground">
                    Página <span className="font-medium text-foreground">{current_page}</span> de{' '}
                    <span className="font-medium text-foreground">{last_page}</span>
                </div>

                {/* Navigation buttons */}
                <div className="flex items-center gap-1">
                    <Button
                        variant="outline"
                        size="icon-sm"
                        onClick={() => goToPage(links.first)}
                        disabled={!links.first || current_page === 1}
                        title="Primeira página"
                    >
                        <ChevronsLeft className="h-4 w-4" />
                    </Button>

                    <Button variant="outline" size="icon-sm" onClick={() => goToPage(links.prev)} disabled={!links.prev} title="Página anterior">
                        <ChevronLeft className="h-4 w-4" />
                    </Button>

                    <Button variant="outline" size="icon-sm" onClick={() => goToPage(links.next)} disabled={!links.next} title="Próxima página">
                        <ChevronRight className="h-4 w-4" />
                    </Button>

                    <Button
                        variant="outline"
                        size="icon-sm"
                        onClick={() => goToPage(links.last)}
                        disabled={!links.last || current_page === last_page}
                        title="Última página"
                    >
                        <ChevronsRight className="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>
    );
}
