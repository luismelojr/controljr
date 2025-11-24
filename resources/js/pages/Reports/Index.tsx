import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import FinancialSummaryCards from '@/components/Reports/FinancialSummaryCards';
import CashFlowChart from '@/components/Reports/CashFlowChart';
import CategoryBreakdown from '@/components/Reports/CategoryBreakdown';
import { CalendarIcon, FilterIcon } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Props {
    filters: {
        start_date: string;
        end_date: string;
    };
    overview: any;
    cashFlow: any[];
    expensesByCategory: any[];
    incomeByCategory: any[];
}

export default function ReportsIndex({ filters, overview, cashFlow, expensesByCategory, incomeByCategory }: Props) {
    const [startDate, setStartDate] = useState(filters.start_date);
    const [endDate, setEndDate] = useState(filters.end_date);

    const handleFilter = () => {
        router.get(route('dashboard.reports.index'), {
            start_date: startDate,
            end_date: endDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <DashboardLayout title="Relatórios Financeiros">
            <Head title="Relatórios" />

            <div className="space-y-6">
                {/* Filters Section */}
                <Card className="border-none shadow-sm bg-card/50 backdrop-blur-sm">
                    <CardContent className="p-4">
                        <div className="flex flex-col md:flex-row items-end gap-4">
                            <div className="grid gap-2 w-full md:w-auto">
                                <Label htmlFor="start_date" className="flex items-center gap-2 text-muted-foreground">
                                    <CalendarIcon className="w-4 h-4" />
                                    Data Inicial
                                </Label>
                                <Input
                                    id="start_date"
                                    type="date"
                                    className="w-full md:w-[180px]"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                />
                            </div>
                            <div className="grid gap-2 w-full md:w-auto">
                                <Label htmlFor="end_date" className="flex items-center gap-2 text-muted-foreground">
                                    <CalendarIcon className="w-4 h-4" />
                                    Data Final
                                </Label>
                                <Input
                                    id="end_date"
                                    type="date"
                                    className="w-full md:w-[180px]"
                                    value={endDate}
                                    onChange={(e) => setEndDate(e.target.value)}
                                />
                            </div>
                            <Button 
                                onClick={handleFilter}
                                className="w-full md:w-auto bg-primary hover:bg-primary/90"
                            >
                                <FilterIcon className="w-4 h-4 mr-2" />
                                Filtrar Resultados
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* KPI Cards */}
                <FinancialSummaryCards data={overview} />

                {/* Charts Grid */}
                <div className="space-y-6">
                    {/* Category Breakdowns - Side by Side */}
                    <div className="grid gap-6 md:grid-cols-2">
                        <CategoryBreakdown 
                            title="Despesas por Categoria" 
                            data={expensesByCategory} 
                        />
                        <CategoryBreakdown 
                            title="Receitas por Categoria" 
                            data={incomeByCategory} 
                        />
                    </div>

                    {/* Cash Flow - Full Width */}
                    <CashFlowChart data={cashFlow} />
                </div>
            </div>
        </DashboardLayout>
    );
}
