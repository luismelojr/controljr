import { ChartDataPoint } from '@/types/reports';
import { PieChart, Pie, Cell, ResponsiveContainer, Legend, Tooltip } from 'recharts';

interface PieChartViewProps {
    data: ChartDataPoint[];
    dataKey?: string;
    nameKey?: string;
}

// Paleta de cores vibrantes e profissionais
const COLORS = [
    '#3b82f6', // blue-500
    '#10b981', // green-500
    '#f59e0b', // amber-500
    '#ef4444', // red-500
    '#8b5cf6', // violet-500
    '#ec4899', // pink-500
    '#14b8a6', // teal-500
    '#f97316', // orange-500
    '#6366f1', // indigo-500
    '#06b6d4', // cyan-500
];

export function PieChartView({
    data,
    dataKey = 'value',
    nameKey = 'name',
}: PieChartViewProps) {
    // Format currency for tooltip
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value / 100); // Convert from cents to reais
    };

    // Custom label to show percentage
    const renderLabel = (entry: any) => {
        const percentage = entry.percentage || entry.percent || 0;
        return `${percentage.toFixed(1)}%`;
    };

    // Custom tooltip
    const CustomTooltip = ({ active, payload }: any) => {
        if (active && payload && payload.length) {
            const data = payload[0].payload;
            return (
                <div className="bg-background border border-border rounded-lg shadow-lg p-3">
                    <p className="font-semibold text-sm mb-1">{data[nameKey]}</p>
                    <p className="text-sm text-muted-foreground">
                        Valor: {formatCurrency(data[dataKey])}
                    </p>
                    {data.percentage !== undefined && (
                        <p className="text-sm text-muted-foreground">
                            Percentual: {data.percentage.toFixed(1)}%
                        </p>
                    )}
                    {data.count !== undefined && (
                        <p className="text-sm text-muted-foreground">
                            Quantidade: {data.count}
                        </p>
                    )}
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
            <PieChart>
                <Pie
                    data={data}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={renderLabel}
                    outerRadius={130}
                    fill="#8884d8"
                    dataKey={dataKey}
                    nameKey={nameKey}
                >
                    {data.map((entry, index) => (
                        <Cell
                            key={`cell-${index}`}
                            fill={COLORS[index % COLORS.length]}
                        />
                    ))}
                </Pie>
                <Tooltip content={<CustomTooltip />} />
                <Legend
                    verticalAlign="bottom"
                    height={36}
                    iconType="circle"
                />
            </PieChart>
        </ResponsiveContainer>
    );
}
