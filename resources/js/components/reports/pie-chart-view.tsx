import { PieChart, Pie, Cell, ResponsiveContainer, Legend, Tooltip, TooltipProps } from 'recharts';

interface PieChartData {
    name: string;
    value: number;
    percentage?: number;
}

interface PieChartViewProps {
    data: PieChartData[];
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

export function PieChartView({ data }: PieChartViewProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const CustomTooltip = ({ active, payload }: TooltipProps<number, string>) => {
        if (active && payload && payload.length) {
            const data = payload[0].payload;
            return (
                <div className="rounded-lg border bg-background p-3 shadow-md">
                    <p className="mb-1 font-semibold">{data.name}</p>
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

    const CustomLabel = ({
        cx,
        cy,
        midAngle,
        innerRadius,
        outerRadius,
        percent,
    }: {
        cx: number;
        cy: number;
        midAngle: number;
        innerRadius: number;
        outerRadius: number;
        percent: number;
    }) => {
        const RADIAN = Math.PI / 180;
        const radius = innerRadius + (outerRadius - innerRadius) * 0.5;
        const x = cx + radius * Math.cos(-midAngle * RADIAN);
        const y = cy + radius * Math.sin(-midAngle * RADIAN);

        if (percent < 0.05) return null; // Don't show label if less than 5%

        return (
            <text
                x={x}
                y={y}
                fill="white"
                textAnchor={x > cx ? 'start' : 'end'}
                dominantBaseline="central"
                className="text-xs font-semibold"
            >
                {`${(percent * 100).toFixed(0)}%`}
            </text>
        );
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
                <PieChart>
                    <Pie
                        data={data}
                        cx="50%"
                        cy="50%"
                        labelLine={false}
                        label={CustomLabel}
                        outerRadius={120}
                        fill="#8884d8"
                        dataKey="value"
                    >
                        {data.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                        ))}
                    </Pie>
                    <Tooltip content={<CustomTooltip />} />
                    <Legend
                        verticalAlign="bottom"
                        height={36}
                        formatter={(value, entry: any) => (
                            <span className="text-sm">
                                {value} - {formatCurrency(entry.payload.value)}
                            </span>
                        )}
                    />
                </PieChart>
            </ResponsiveContainer>
        </div>
    );
}
