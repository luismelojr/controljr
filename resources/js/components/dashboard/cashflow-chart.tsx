import { TrendingUp, TrendingDown } from 'lucide-react';
import { cn } from '@/lib/utils';
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
    TooltipProps,
} from 'recharts';

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
    className,
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

    // Transform data for Recharts
    const chartData = months.map((month, index) => ({
        month,
        Despesas: expenseData[index] || 0,
        Receitas: incomeData[index] || 0,
    }));

    // Custom Tooltip
    const CustomTooltip = ({ active, payload, label }: TooltipProps<number, string>) => {
        if (active && payload && payload.length) {
            return (
                <div className="rounded-lg border bg-background p-3 shadow-md">
                    <p className="mb-2 font-semibold">{label}</p>
                    {payload.map((entry, index) => (
                        <div key={index} className="flex items-center gap-2 text-sm">
                            <div
                                className="h-3 w-3 rounded-full"
                                style={{ backgroundColor: entry.color }}
                            />
                            <span className="text-muted-foreground">{entry.name}:</span>
                            <span className="font-semibold">{formatCurrency(entry.value as number)}</span>
                        </div>
                    ))}
                </div>
            );
        }
        return null;
    };

    const isPositive = percentageChange >= 0;

    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="mb-6 flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-semibold">Fluxo de Caixa</h3>
                    <div className="mt-1 flex items-center gap-2">
                        <span className="text-sm text-muted-foreground">Saldo Total</span>
                    </div>
                </div>
            </div>

            <div className="mb-4 flex items-baseline gap-2">
                <h4 className="text-3xl font-bold">{formatCurrency(totalBalance)}</h4>
                <span
                    className={cn(
                        'flex items-center gap-1 text-sm font-medium',
                        isPositive ? 'text-green-500' : 'text-red-500'
                    )}
                >
                    {isPositive ? (
                        <TrendingUp className="h-4 w-4" />
                    ) : (
                        <TrendingDown className="h-4 w-4" />
                    )}
                    {Math.abs(percentageChange)}%
                </span>
            </div>

            <div className="mb-6 flex gap-4 text-sm">
                <div className="flex items-center gap-2">
                    <div className="h-3 w-3 rounded-full bg-red-500" />
                    <span className="text-muted-foreground">Despesas</span>
                </div>
                <div className="flex items-center gap-2">
                    <div className="h-3 w-3 rounded-full bg-green-500" />
                    <span className="text-muted-foreground">Receitas</span>
                </div>
            </div>

            {/* Chart Area */}
            <div className="h-64">
                {chartData.length === 0 ? (
                    <div className="flex h-full items-center justify-center">
                        <p className="text-sm text-muted-foreground">Nenhum dado disponível</p>
                    </div>
                ) : (
                    <ResponsiveContainer width="100%" height="100%">
                        <BarChart data={chartData}>
                            <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                            <XAxis
                                dataKey="month"
                                className="text-xs"
                                tick={{ fill: 'hsl(var(--muted-foreground))' }}
                            />
                            <YAxis
                                className="text-xs"
                                tick={{ fill: 'hsl(var(--muted-foreground))' }}
                                tickFormatter={(value) =>
                                    new Intl.NumberFormat('pt-BR', {
                                        notation: 'compact',
                                        compactDisplay: 'short',
                                    }).format(value)
                                }
                            />
                            <Tooltip content={<CustomTooltip />} cursor={{ fill: 'hsl(var(--muted))' }} />
                            <Legend
                                wrapperStyle={{ fontSize: '14px' }}
                                iconType="circle"
                            />
                            <Bar
                                dataKey="Despesas"
                                fill="hsl(0 84% 60%)"
                                radius={[4, 4, 0, 0]}
                            />
                            <Bar
                                dataKey="Receitas"
                                fill="hsl(142 76% 36%)"
                                radius={[4, 4, 0, 0]}
                            />
                        </BarChart>
                    </ResponsiveContainer>
                )}
            </div>
        </div>
    );
}
