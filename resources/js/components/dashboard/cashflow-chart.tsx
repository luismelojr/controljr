import { TrendingUp } from 'lucide-react';
import { cn } from '@/lib/utils';

interface CashflowChartProps {
    totalBalance: number;
    percentageChange: number;
    expenseAmount: number;
    incomeAmount: number;
    months?: string[];
    expenses?: number[];
    incomes?: number[];
    className?: string;
}

export function CashflowChart({
    totalBalance,
    percentageChange,
    expenseAmount,
    incomeAmount,
    months: propMonths,
    expenses: propExpenses,
    incomes: propIncomes,
    className
}: CashflowChartProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    // Use real data from backend or fallback to empty arrays
    const months = propMonths || [];
    const expenseData = propExpenses || [];
    const incomeData = propIncomes || [];

    const maxValue = Math.max(...expenseData, ...incomeData, 1); // min 1 to avoid division by zero

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
                {months.length === 0 ? (
                    <div className="flex h-full items-center justify-center">
                        <p className="text-sm text-muted-foreground">Nenhum dado disponível</p>
                    </div>
                ) : (
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
                            </div>

                            {/* Month Label */}
                            <span className="text-xs text-muted-foreground">{month}</span>
                        </div>
                    ))}
                    </div>
                )}
            </div>
        </div>
    );
}
