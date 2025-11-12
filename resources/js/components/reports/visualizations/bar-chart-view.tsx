import { ChartDataPoint } from '@/types/reports';
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
} from 'recharts';

interface BarChartViewProps {
    data: ChartDataPoint[];
    dataKey?: string;
    nameKey?: string;
    color?: string;
    layout?: 'horizontal' | 'vertical';
}

export function BarChartView({
    data,
    dataKey = 'value',
    nameKey = 'name',
    color = '#3b82f6',
    layout = 'vertical',
}: BarChartViewProps) {
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
                    {data.count !== undefined && (
                        <p className="text-sm text-muted-foreground">
                            Quantidade: {data.count}
                        </p>
                    )}
                    {data.percentage !== undefined && (
                        <p className="text-sm text-muted-foreground">
                            Percentual: {data.percentage.toFixed(1)}%
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
            <BarChart
                data={data}
                layout={layout}
                margin={{ top: 20, right: 30, left: 20, bottom: 5 }}
            >
                <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                {layout === 'vertical' ? (
                    <>
                        <XAxis
                            type="number"
                            tickFormatter={formatCompact}
                            className="text-xs"
                        />
                        <YAxis
                            type="category"
                            dataKey={nameKey}
                            width={150}
                            className="text-xs"
                        />
                    </>
                ) : (
                    <>
                        <XAxis
                            type="category"
                            dataKey={nameKey}
                            className="text-xs"
                        />
                        <YAxis
                            type="number"
                            tickFormatter={formatCompact}
                            className="text-xs"
                        />
                    </>
                )}
                <Tooltip content={<CustomTooltip />} />
                <Legend />
                <Bar
                    dataKey={dataKey}
                    fill={color}
                    radius={[4, 4, 0, 0]}
                    name="Valor"
                />
            </BarChart>
        </ResponsiveContainer>
    );
}
