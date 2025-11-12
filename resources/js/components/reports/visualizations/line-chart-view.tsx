import { ChartDataPoint } from '@/types/reports';
import {
    LineChart,
    Line,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
} from 'recharts';

interface LineChartViewProps {
    data: ChartDataPoint[];
    dataKeys?: string[]; // Multiple lines support
    nameKey?: string;
    colors?: string[];
}

const DEFAULT_COLORS = [
    '#3b82f6', // blue-500
    '#10b981', // green-500
    '#f59e0b', // amber-500
    '#ef4444', // red-500
    '#8b5cf6', // violet-500
];

export function LineChartView({
    data,
    dataKeys = ['value'],
    nameKey = 'name',
    colors = DEFAULT_COLORS,
}: LineChartViewProps) {
    // Format currency for axis and tooltip
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value / 100);
    };

    // Format compact numbers for axis
    const formatCompact = (value: number) => {
        const reais = value / 100;
        if (reais >= 1000000) {
            return `R$ ${(reais / 1000000).toFixed(1)}M`;
        }
        if (reais >= 1000) {
            return `R$ ${(reais / 1000).toFixed(1)}K`;
        }
        return formatCurrency(value);
    };

    // Format date labels
    const formatDate = (value: string) => {
        try {
            const date = new Date(value);
            return new Intl.DateTimeFormat('pt-BR', {
                month: 'short',
                year: '2-digit',
            }).format(date);
        } catch {
            return value;
        }
    };

    // Custom tooltip
    const CustomTooltip = ({ active, payload, label }: any) => {
        if (active && payload && payload.length) {
            return (
                <div className="bg-background border border-border rounded-lg shadow-lg p-3">
                    <p className="font-semibold text-sm mb-2">{label}</p>
                    {payload.map((entry: any, index: number) => (
                        <div key={index} className="flex items-center gap-2 mb-1">
                            <div
                                className="w-3 h-3 rounded-full"
                                style={{ backgroundColor: entry.color }}
                            />
                            <p className="text-sm">
                                <span className="text-muted-foreground">
                                    {entry.name}:
                                </span>{' '}
                                <span className="font-medium">
                                    {formatCurrency(entry.value)}
                                </span>
                            </p>
                        </div>
                    ))}
                </div>
            );
        }
        return null;
    };

    if (!data || data.length === 0) {
        return (
            <div className="flex items-center justify-center h-[400px] text-muted-foreground">
                Nenhum dado disponível para visualização
            </div>
        );
    }

    return (
        <ResponsiveContainer width="100%" height={400}>
            <LineChart
                data={data}
                margin={{ top: 20, right: 30, left: 20, bottom: 5 }}
            >
                <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                <XAxis
                    dataKey={nameKey}
                    tickFormatter={formatDate}
                    className="text-xs"
                />
                <YAxis tickFormatter={formatCompact} className="text-xs" />
                <Tooltip content={<CustomTooltip />} />
                <Legend />
                {dataKeys.map((key, index) => (
                    <Line
                        key={key}
                        type="monotone"
                        dataKey={key}
                        stroke={colors[index % colors.length]}
                        strokeWidth={2}
                        dot={{ r: 4 }}
                        activeDot={{ r: 6 }}
                        name={
                            key === 'value'
                                ? 'Valor'
                                : key.charAt(0).toUpperCase() + key.slice(1)
                        }
                    />
                ))}
            </LineChart>
        </ResponsiveContainer>
    );
}
