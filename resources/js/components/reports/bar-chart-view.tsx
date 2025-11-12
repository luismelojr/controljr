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
    Cell,
} from 'recharts';

interface BarChartData {
    name: string;
    value: number;
    percentage?: number;
}

interface BarChartViewProps {
    data: BarChartData[];
    orientation?: 'vertical' | 'horizontal';
}

const COLORS = [
    'hsl(0 84% 60%)',      // red
    'hsl(142 76% 36%)',    // green
    'hsl(221 83% 53%)',    // blue
    'hsl(48 96% 53%)',     // yellow
    'hsl(262 83% 58%)',    // purple
    'hsl(346 84% 61%)',    // pink
    'hsl(173 80% 40%)',    // teal
    'hsl(25 95% 53%)',     // orange
];

export function BarChartView({ data, orientation = 'vertical' }: BarChartViewProps) {
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
                            <span className="text-muted-foreground">Percentual: </span>
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

    // For horizontal bars, we might want to limit the number of items shown
    const displayData = orientation === 'horizontal' ? data.slice(0, 10) : data;

    return (
        <div className={orientation === 'horizontal' ? 'h-[600px]' : 'h-96'}>
            <ResponsiveContainer width="100%" height="100%">
                <BarChart
                    data={displayData}
                    layout={orientation === 'horizontal' ? 'vertical' : 'horizontal'}
                    margin={{ top: 20, right: 30, left: orientation === 'horizontal' ? 100 : 20, bottom: 5 }}
                >
                    <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />

                    {orientation === 'vertical' ? (
                        <>
                            <XAxis
                                dataKey="name"
                                className="text-xs"
                                tick={{ fill: 'hsl(var(--muted-foreground))' }}
                                angle={-45}
                                textAnchor="end"
                                height={100}
                            />
                            <YAxis
                                className="text-xs"
                                tick={{ fill: 'hsl(var(--muted-foreground))' }}
                                tickFormatter={formatCompact}
                            />
                        </>
                    ) : (
                        <>
                            <XAxis
                                type="number"
                                className="text-xs"
                                tick={{ fill: 'hsl(var(--muted-foreground))' }}
                                tickFormatter={formatCompact}
                            />
                            <YAxis
                                dataKey="name"
                                type="category"
                                className="text-xs"
                                tick={{ fill: 'hsl(var(--muted-foreground))' }}
                                width={90}
                            />
                        </>
                    )}

                    <Tooltip content={<CustomTooltip />} cursor={{ fill: 'hsl(var(--muted))' }} />
                    <Legend wrapperStyle={{ fontSize: '14px' }} />

                    <Bar
                        dataKey="value"
                        name="Valor"
                        radius={[4, 4, 0, 0]}
                    >
                        {displayData.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                        ))}
                    </Bar>
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}
