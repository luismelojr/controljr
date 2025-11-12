import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ActiveFilters, FilterConfig } from '@/types/datatable';
import { router } from '@inertiajs/react';
import { Filter } from 'lucide-react';
import { useEffect, useState } from 'react';

interface DataTableFiltersProps {
    filters: FilterConfig[];
    activeFilters: ActiveFilters;
    currentSort?: string;
}

/**
 * Filters component for DataTable
 * Supports text, select, boolean, date, and number filters
 * Integrates directly with Inertia router and Spatie Query Builder
 */
export function DataTableFilters({ filters, activeFilters, currentSort }: DataTableFiltersProps) {
    const [open, setOpen] = useState(false);

    // Robust validation
    const safeFilters = Array.isArray(filters) ? filters : [];
    const safeActiveFilters = activeFilters && typeof activeFilters === 'object' && !Array.isArray(activeFilters) ? activeFilters : {};

    const [localFilters, setLocalFilters] = useState<ActiveFilters>(safeActiveFilters);

    /**
     * Sync local filters with active filters when they change
     * This ensures the drawer shows the correct values when reopened
     */
    useEffect(() => {
        setLocalFilters(safeActiveFilters);
    }, [JSON.stringify(safeActiveFilters)]);

    // Safe active filters count
    const activeFiltersCount = (() => {
        try {
            const keys = Object.keys(safeActiveFilters);
            if (!Array.isArray(keys)) return 0;
            return keys.filter((key) => {
                const value = safeActiveFilters[key];
                return value !== null && value !== undefined && value !== '';
            }).length;
        } catch (e) {
            console.error('Error counting active filters:', e);
            return 0;
        }
    })();

    const hasActiveFilters = activeFiltersCount > 0;

    /**
     * Update local filter state
     */
    const updateLocalFilter = (key: string, value: any) => {
        setLocalFilters((prev) => ({
            ...prev,
            [key]: value,
        }));
    };

    /**
     * Apply filters and reload page with Inertia
     */
    const applyFilters = () => {
        // Remove empty/null values
        const cleanedFilters = Object.entries(localFilters).reduce(
            (acc, [key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    acc[key] = value;
                }
                return acc;
            },
            {} as Record<string, any>,
        );

        const params: Record<string, any> = {};

        // Add filters
        if (Object.keys(cleanedFilters).length > 0) {
            params.filter = cleanedFilters;
        }

        // Preserve current sort
        if (currentSort) {
            params.sort = currentSort;
        }

        router.get(window.location.pathname, params, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
            },
        });
    };

    /**
     * Clear all filters
     */
    const clearFilters = () => {
        setLocalFilters({});

        const params: Record<string, any> = {};

        // Preserve current sort
        if (currentSort) {
            params.sort = currentSort;
        }

        router.get(window.location.pathname, params, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
            },
        });
    };

    /**
     * Render filter input based on type
     */
    const renderFilterInput = (filter: FilterConfig) => {
        const value = localFilters[filter.key] ?? '';

        switch (filter.type) {
            case 'text':
                return (
                    <Input
                        id={filter.key}
                        type="text"
                        placeholder={filter.placeholder || `Filtrar por ${filter.label.toLowerCase()}`}
                        value={value as string}
                        onChange={(e) => updateLocalFilter(filter.key, e.target.value)}
                        onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                                applyFilters();
                            }
                        }}
                    />
                );

            case 'number':
                return (
                    <Input
                        id={filter.key}
                        type="number"
                        placeholder={filter.placeholder || `Filtrar por ${filter.label.toLowerCase()}`}
                        value={value as number}
                        onChange={(e) => updateLocalFilter(filter.key, e.target.value)}
                        onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                                applyFilters();
                            }
                        }}
                    />
                );

            case 'select':
            case 'boolean': {
                const selectValue = value === null || value === undefined || value === '' ? '__all__' : String(value);
                return (
                    <Select value={selectValue} onValueChange={(val) => updateLocalFilter(filter.key, val === '__all__' ? null : val)}>
                        <SelectTrigger id={filter.key} className={'w-full'}>
                            <SelectValue placeholder={filter.placeholder || `Selecione ${filter.label.toLowerCase()}`} />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="__all__">Todos</SelectItem>
                            {filter.options?.map((option) => (
                                <SelectItem key={String(option.value)} value={String(option.value)}>
                                    {option.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                );
            }

            case 'date':
                return <Input id={filter.key} type="date" value={value as string} onChange={(e) => updateLocalFilter(filter.key, e.target.value)} />;

            default:
                return null;
        }
    };

    return (
        <Sheet open={open} onOpenChange={setOpen}>
            <SheetTrigger asChild>
                <Button variant="outline" className="gap-2">
                    <Filter className="h-4 w-4" />
                    Filtros
                    {hasActiveFilters && (
                        <span className="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs text-primary-foreground">
                            {activeFiltersCount}
                        </span>
                    )}
                </Button>
            </SheetTrigger>
            <SheetContent className="flex w-full flex-col sm:max-w-md">
                <SheetHeader className="px-6 pt-6">
                    <SheetTitle>Filtros</SheetTitle>
                    <SheetDescription>Configure os filtros para refinar sua busca</SheetDescription>
                </SheetHeader>

                {/* Filter inputs */}
                <div className="flex-1 overflow-y-auto px-6 py-6">
                    <div className="space-y-5">
                        {safeFilters.map((filter) => (
                            <div key={filter.key} className="space-y-2">
                                <Label htmlFor={filter.key} className="text-sm font-medium">
                                    {filter.label}
                                </Label>
                                {renderFilterInput(filter)}
                            </div>
                        ))}
                    </div>
                </div>

                {/* Actions */}
                <SheetFooter className="mt-auto flex-col gap-3 border-t px-6 py-6 sm:flex-col">
                    <Button onClick={applyFilters} className="w-full" size="lg">
                        Aplicar Filtros
                    </Button>
                    {hasActiveFilters && (
                        <Button variant="outline" onClick={clearFilters} className="w-full" size="lg">
                            Limpar Filtros
                        </Button>
                    )}
                    <Button variant="ghost" onClick={() => setOpen(false)} className="w-full">
                        Cancelar
                    </Button>
                </SheetFooter>
            </SheetContent>
        </Sheet>
    );
}
