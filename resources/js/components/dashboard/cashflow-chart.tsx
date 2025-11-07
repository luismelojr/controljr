import { TrendingUp } from 'lucide-react';
import { cn } from '@/lib/utils';

interface CashflowChartProps {
    totalBalance: number;
    percentageChange: number;
    expenseAmount: number;
    incomeAmount: number;
    className?: string;
}

export function CashflowChart({ totalBalance, percentageChange, expenseAmount, incomeAmount, className }: CashflowChartProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const months = ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'];

    // Dados simulados para o gráfico (altura relativa)
    const expenseData = [15, 20, 18, 25, 22, 30, 28, 35, 32, 38];
    const incomeData = [25, 30, 28, 35, 32, 40, 38, 45, 42, 48];

    const maxValue = Math.max(...expenseData, ...incomeData);

    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="mb-6 flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-semibold">Cashflow</h3>
                    <div className="mt-1 flex items-center gap-2">
                        <span className="text-sm text-muted-foreground">Total Balance</span>
                    </div>
                </div>
                <select className="rounded-md border bg-background px-3 py-1.5 text-sm">
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>Last 3 Months</option>
                </select>
            </div>

            <div className="mb-4 flex items-baseline gap-2">
                <h4 className="text-3xl font-bold">{formatCurrency(totalBalance)}</h4>
                <span className="flex items-center gap-1 text-sm font-medium text-green-500">
                    <TrendingUp className="h-4 w-4" />
                    {percentageChange}%
                </span>
            </div>

            <div className="mb-6 flex gap-4 text-sm">
                <div className="flex items-center gap-2">
                    <div className="h-3 w-3 rounded-full bg-blue-500" />
                    <span className="text-muted-foreground">Expense</span>
                </div>
                <div className="flex items-center gap-2">
                    <div className="h-3 w-3 rounded-full bg-yellow-500" />
                    <span className="text-muted-foreground">Income</span>
                </div>
            </div>

            {/* Chart Area */}
            <div className="relative h-48">
                <div className="absolute inset-0 flex items-end justify-between gap-2">
                    {months.map((month, index) => (
                        <div key={month} className="relative flex flex-1 flex-col items-center gap-1">
                            {/* Bars */}
                            <div className="relative w-full flex-1">
                                {/* Income Bar (background) */}
                                <div
                                    className="absolute bottom-0 w-full rounded-t bg-yellow-500/20"
                                    style={{
                                        height: `${(incomeData[index] / maxValue) * 100}%`,
                                    }}
                                />
                                {/* Expense Bar (foreground) */}
                                <div
                                    className="absolute bottom-0 w-full rounded-t bg-blue-500"
                                    style={{
                                        height: `${(expenseData[index] / maxValue) * 100}%`,
                                    }}
                                />

                                {/* Tooltip on hover for May */}
                                {month === 'May' && (
                                    <>
                                        <div className="absolute -top-12 left-1/2 -translate-x-1/2 rounded bg-blue-500 px-2 py-1 text-xs text-white">
                                            {formatCurrency(expenseAmount)}
                                        </div>
                                        <div className="absolute -top-24 left-1/2 -translate-x-1/2 rounded bg-yellow-500 px-2 py-1 text-xs text-white">
                                            {formatCurrency(incomeAmount)}
                                        </div>
                                    </>
                                )}
                            </div>

                            {/* Month Label */}
                            <span className="text-xs text-muted-foreground">{month}</span>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
