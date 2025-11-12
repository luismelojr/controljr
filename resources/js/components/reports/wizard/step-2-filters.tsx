import { ReportType, ReportFilters } from '@/types/reports';
import { Category } from '@/types/category';
import { WalletInterface } from '@/types/wallet';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import TextSelect from '@/components/ui/text-select';
import TextMultiSelect from '@/components/ui/text-multi-select';
import { useState, useEffect } from 'react';
import { format } from 'date-fns';

interface Step2FiltersProps {
    reportType: ReportType | null;
    categories: Category[];
    wallets: WalletInterface[];
    filters: ReportFilters;
    onChange: (filters: ReportFilters) => void;
    onNext: () => void;
    onBack: () => void;
}

export function Step2Filters({
    reportType,
    categories,
    wallets,
    filters,
    onChange,
    onNext,
    onBack,
}: Step2FiltersProps) {
    const [periodType, setPeriodType] = useState<string>(
        filters.period_type || 'last_month'
    );
    const [startDate, setStartDate] = useState<string>(
        filters.start_date || ''
    );
    const [endDate, setEndDate] = useState<string>(filters.end_date || '');

    // Initialize dates on mount if not set
    useEffect(() => {
        if (!startDate || !endDate) {
            handlePeriodChange(periodType);
        }
    }, []);

    // Determine which filters are available based on report type
    const availableFilters = getAvailableFilters(reportType);

    // Handle period type change
    const handlePeriodChange = (type: string) => {
        setPeriodType(type);

        if (type === 'custom') {
            onChange({
                ...filters,
                period_type: null,
                start_date: startDate,
                end_date: endDate,
            });
            return;
        }

        const today = new Date();
        let start: Date;
        const end = today;

        switch (type) {
            case 'last_month':
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                break;
            case 'last_3_months':
                start = new Date(today.getFullYear(), today.getMonth() - 3, 1);
                break;
            case 'last_6_months':
                start = new Date(today.getFullYear(), today.getMonth() - 6, 1);
                break;
            case 'last_year':
                start = new Date(today.getFullYear() - 1, today.getMonth(), 1);
                break;
            case 'current_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                break;
            case 'current_year':
                start = new Date(today.getFullYear(), 0, 1);
                break;
            default:
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        }

        const formattedStart = format(start, 'yyyy-MM-dd');
        const formattedEnd = format(end, 'yyyy-MM-dd');

        setStartDate(formattedStart);
        setEndDate(formattedEnd);

        onChange({
            ...filters,
            period_type: type as any,
            start_date: formattedStart,
            end_date: formattedEnd,
        });
    };

    // Handle custom date changes
    const handleDateChange = (
        field: 'start_date' | 'end_date',
        value: string
    ) => {
        if (field === 'start_date') {
            setStartDate(value);
        } else {
            setEndDate(value);
        }

        onChange({
            ...filters,
            [field]: value,
        });
    };

    const periodOptions = [
        { value: 'last_month', label: 'Último mês' },
        { value: 'last_3_months', label: 'Últimos 3 meses' },
        { value: 'last_6_months', label: 'Últimos 6 meses' },
        { value: 'last_year', label: 'Último ano' },
        { value: 'current_month', label: 'Mês atual' },
        { value: 'current_year', label: 'Ano atual' },
        { value: 'custom', label: 'Personalizado' },
    ];

    const statusOptions = [
        { value: 'all', label: 'Todas' },
        { value: 'paid', label: 'Pagas' },
        { value: 'pending', label: 'Pendentes' },
    ];

    const limitOptions = [
        { value: '5', label: 'Top 5' },
        { value: '10', label: 'Top 10' },
        { value: '20', label: 'Top 20' },
        { value: '50', label: 'Top 50' },
    ];

    return (
        <div className="space-y-8">
            <div className="space-y-3">
                <h2 className="text-2xl font-bold">Aplicar Filtros</h2>
                <p className="text-muted-foreground text-base">
                    Configure os filtros para o seu relatório
                </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Period Filter - Always available */}
                <TextSelect
                    label="Período"
                    id="period_type"
                    options={periodOptions}
                    value={periodType}
                    onValueChange={handlePeriodChange}
                    placeholder="Selecione o período"
                />

                {/* Custom Date Range - Show if custom period selected */}
                {periodType === 'custom' && (
                    <>
                        <div className="space-y-2">
                            <Label htmlFor="start_date">Data Início</Label>
                            <Input
                                id="start_date"
                                type="date"
                                value={startDate}
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                                    handleDateChange('start_date', e.target.value)
                                }
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="end_date">Data Fim</Label>
                            <Input
                                id="end_date"
                                type="date"
                                value={endDate}
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                                    handleDateChange('end_date', e.target.value)
                                }
                            />
                        </div>
                    </>
                )}

                {/* Categories Filter */}
                {availableFilters.includes('categories') && (
                    <TextMultiSelect
                        label="Categorias"
                        id="category_ids"
                        options={categories.map((cat) => ({
                            value: cat.uuid,
                            label: cat.name,
                        }))}
                        selected={filters.category_ids || []}
                        onChange={(ids) =>
                            onChange({ ...filters, category_ids: ids })
                        }
                        placeholder="Selecione as categorias"
                    />
                )}

                {/* Wallets Filter */}
                {availableFilters.includes('wallets') && (
                    <TextMultiSelect
                        label="Carteiras"
                        id="wallet_ids"
                        options={wallets.map((wallet) => ({
                            value: wallet.uuid,
                            label: wallet.name,
                        }))}
                        selected={filters.wallet_ids || []}
                        onChange={(ids) =>
                            onChange({ ...filters, wallet_ids: ids })
                        }
                        placeholder="Selecione as carteiras"
                    />
                )}

                {/* Status Filter */}
                {availableFilters.includes('status') && (
                    <TextSelect
                        label="Status"
                        id="status"
                        options={statusOptions}
                        value={filters.status || 'all'}
                        onValueChange={(value: string) =>
                            onChange({
                                ...filters,
                                status: value as any,
                            })
                        }
                        placeholder="Selecione o status"
                    />
                )}

                {/* Amount Range Filter */}
                {availableFilters.includes('amount_range') && (
                    <>
                        <div className="space-y-2">
                            <Label htmlFor="min_amount">Valor Mínimo (R$)</Label>
                            <Input
                                id="min_amount"
                                type="number"
                                step="0.01"
                                min="0"
                                value={filters.min_amount || ''}
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                                    onChange({
                                        ...filters,
                                        min_amount: e.target.value
                                            ? parseFloat(e.target.value)
                                            : null,
                                    })
                                }
                                placeholder="0,00"
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="max_amount">Valor Máximo (R$)</Label>
                            <Input
                                id="max_amount"
                                type="number"
                                step="0.01"
                                min="0"
                                value={filters.max_amount || ''}
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                                    onChange({
                                        ...filters,
                                        max_amount: e.target.value
                                            ? parseFloat(e.target.value)
                                            : null,
                                    })
                                }
                                placeholder="0,00"
                            />
                        </div>
                    </>
                )}

                {/* Limit Filter (for Top N reports) */}
                {availableFilters.includes('limit') && (
                    <TextSelect
                        label="Quantidade de Registros"
                        id="limit"
                        options={limitOptions}
                        value={String(filters.limit || '10')}
                        onValueChange={(value: string) =>
                            onChange({
                                ...filters,
                                limit: parseInt(value),
                            })
                        }
                        placeholder="Selecione a quantidade"
                    />
                )}
            </div>

            <div className="flex justify-between pt-4">
                <Button variant="outline" onClick={onBack} size="lg" className="px-8">
                    Voltar
                </Button>
                <Button onClick={onNext} size="lg" className="px-8">
                    Próximo
                </Button>
            </div>
        </div>
    );
}

/**
 * Determine which filters are available for each report type
 */
function getAvailableFilters(reportType: ReportType | null): string[] {
    if (!reportType) return ['period'];

    const filterMap: Record<ReportType, string[]> = {
        expenses_by_category: [
            'period',
            'categories',
            'wallets',
            'status',
            'amount_range',
        ],
        expenses_by_wallet: [
            'period',
            'wallets',
            'categories',
            'status',
            'amount_range',
        ],
        expenses_evolution: ['period', 'categories', 'wallets'],
        top_expenses: ['period', 'categories', 'wallets', 'status', 'limit'],
        income_by_category: ['period', 'categories', 'status'],
        income_by_wallet: ['period', 'wallets', 'categories', 'status'],
        income_evolution: ['period', 'categories'],
        cashflow: ['period'],
    };

    return filterMap[reportType] || ['period'];
}
