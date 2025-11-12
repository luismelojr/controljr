import {
    LineChart,
    Line,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
    TooltipProps,
} from 'recharts';

interface LineChartData {
    name: string;
    value: number;
    percentage?: number;
}

interface LineChartViewProps {
    data: LineChartData[];
}

export function LineChartView({ data }: LineChartViewProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const formatCompact = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            notation: 'compact',
            compactDisplay: 'short',
        }).format(value);
    };

    const CustomTooltip = ({ active, payload, label }: TooltipProps<number, string>) => {
        if (active && payload && payload.length) {
            const data = payload[0].payload;
            return (
                <div className="rounded-lg border bg-background p-3 shadow-md">
                    <p className="mb-1 font-semibold">{label}</p>
                    <p className="text-sm">
                        <span className="text-muted-foreground">Valor: </span>
                        <span className="font-semibold">{formatCurrency(data.value)}</span>
                    </p>
                    {data.percentage !== undefined && (
                        <p className="text-sm">
                            <span className="text-muted-foreground">Variação: </span>
                            <span className="font-semibold">{data.percentage.toFixed(1)}%</span>
                        </p>
                    )}
                </div>
            );
        }
        return null;
    };

    if (data.length === 0) {
        return (
            <div className="flex h-96 items-center justify-center">
                <p className="text-sm text-muted-foreground">Nenhum dado disponível para visualização</p>
            </div>
        );
    }

    return (
        <div className="h-96">
            <ResponsiveContainer width="100%" height="100%">
                <LineChart data={data} margin={{ top: 5, right: 30, left: 20, bottom: 5 }}>
                    <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                    <XAxis
                        dataKey="name"
                        className="text-xs"
                        tick={{ fill: 'hsl(var(--muted-foreground))' }}
                    />
                    <YAxis
                        className="text-xs"
                        tick={{ fill: 'hsl(var(--muted-foreground))' }}
                        tickFormatter={formatCompact}
                    />
                    <Tooltip content={<CustomTooltip />} />
                    <Legend wrapperStyle={{ fontSize: '14px' }} />
                    <Line
                        type="monotone"
                        dataKey="value"
                        name="Valor"
                        stroke="hsl(221 83% 53%)"
                        strokeWidth={2}
                        dot={{ fill: 'hsl(221 83% 53%)', r: 4 }}
                        activeDot={{ r: 6 }}
                    />
                </LineChart>
            </ResponsiveContainer>
        </div>
    );
}
