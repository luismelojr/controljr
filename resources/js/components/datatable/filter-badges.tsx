import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ActiveFilters } from '@/types/datatable';
import { router } from '@inertiajs/react';
import { X } from 'lucide-react';

interface FilterBadgesProps {
    filters: ActiveFilters;
    filterConfigs: Array<{
        key: string;
        label: string;
        options?: Array<{ value: string | number | boolean; label: string }>;
    }>;
    currentSort?: string;
}

/**
 * Component to display active filters as badges
 * Each badge can be removed individually
 * Integrates directly with Inertia router
 */
export function FilterBadges({ filters, filterConfigs, currentSort }: FilterBadgesProps) {
    // Robust validation
    const safeFilters = filters && typeof filters === 'object' && !Array.isArray(filters) ? filters : {};
    const safeFilterConfigs = Array.isArray(filterConfigs) ? filterConfigs : [];

    // Safe filter keys extraction
    const activeFilterKeys = (() => {
        try {
            const keys = Object.keys(safeFilters);
            if (!Array.isArray(keys)) return [];
            return keys.filter((key) => {
                const value = safeFilters[key];
                return value !== null && value !== undefined && value !== '';
            });
        } catch (e) {
            console.error('Error extracting active filter keys:', e);
            return [];
        }
    })();

    if (activeFilterKeys.length === 0) {
        return null;
    }

    /**
     * Remove a specific filter and reload page with Inertia
     */
    const removeFilter = (filterKey: string) => {
        const newFilters = { ...safeFilters };
        delete newFilters[filterKey];

        const params: Record<string, any> = {};

        // Add remaining filters
        if (Object.keys(newFilters).length > 0) {
            params.filter = newFilters;
        }

        // Preserve current sort
        if (currentSort) {
            params.sort = currentSort;
        }

        router.get(window.location.pathname, params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    /**
     * Clear all filters at once
     */
    const clearAllFilters = () => {
        const params: Record<string, any> = {};

        // Preserve current sort
        if (currentSort) {
            params.sort = currentSort;
        }

        router.get(window.location.pathname, params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    /**
     * Get display value for a filter
     */
    const getFilterDisplayValue = (key: string, value: any): string => {
        const config = safeFilterConfigs.find((c) => c.key === key);

        if (!config) return String(value);

        // If options exist, find label
        if (config.options) {
            const option = config.options.find((opt) => String(opt.value) === String(value));
            if (option) return option.label;
        }

        // Boolean values
        if (typeof value === 'boolean') {
            return value ? 'Sim' : 'Não';
        }

        return String(value);
    };

    /**
     * Get filter label
     */
    const getFilterLabel = (key: string): string => {
        const config = safeFilterConfigs.find((c) => c.key === key);
        return config?.label || key;
    };

    return (
        <div className="flex flex-wrap items-center gap-2">
            <span className="text-sm text-muted-foreground">Filtros ativos:</span>

            {activeFilterKeys.map((key) => {
                const label = getFilterLabel(key);
                const value = getFilterDisplayValue(key, safeFilters[key]);

                return (
                    <Badge key={key} variant="secondary" className="gap-1.5 pr-1">
                        <span>
                            <span className="font-semibold">{label}:</span> {value}
                        </span>
                        <Button
                            variant="ghost"
                            size="icon-sm"
                            className="h-4 w-4 rounded-full hover:bg-secondary-foreground/20"
                            onClick={() => removeFilter(key)}
                        >
                            <X className="h-3 w-3" />
                        </Button>
                    </Badge>
                );
            })}

            {activeFilterKeys.length > 1 && (
                <Button variant="ghost" size="sm" onClick={clearAllFilters} className="h-7 text-xs">
                    Limpar todos
                </Button>
            )}
        </div>
    );
}
