import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Bar, BarChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

import { cn } from '@/lib/utils';

interface AdminRevenueChartProps {
    data: {
        date: string;
        value: number;
    }[];
    className?: string;
}

export function AdminRevenueChart({ data, className }: AdminRevenueChartProps) {
    return (
        <Card className={cn('col-span-4', className)}>
            <CardHeader>
                <CardTitle>Receita Mensal</CardTitle>
                <CardDescription>Receita diária nos últimos 30 dias.</CardDescription>
            </CardHeader>
            <CardContent className="pl-2">
                <ResponsiveContainer width="100%" height={350}>
                    <BarChart data={data}>
                        <XAxis dataKey="date" stroke="#888888" fontSize={12} tickLine={false} axisLine={false} />
                        <YAxis stroke="#888888" fontSize={12} tickLine={false} axisLine={false} tickFormatter={(value) => `R$${value}`} />
                        <Tooltip
                            cursor={{ fill: 'transparent' }}
                            content={({ active, payload }) => {
                                if (active && payload && payload.length) {
                                    return (
                                        <div className="rounded-lg border bg-background p-2 shadow-sm">
                                            <div className="grid grid-cols-2 gap-2">
                                                <div className="flex flex-col">
                                                    <span className="text-[0.70rem] text-muted-foreground uppercase">Receita</span>
                                                    <span className="font-bold text-muted-foreground">
                                                        {new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(
                                                            payload[0].value as number,
                                                        )}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                }
                                return null;
                            }}
                        />
                        <Bar dataKey="value" fill="currentColor" radius={[4, 4, 0, 0]} className="fill-primary" />
                    </BarChart>
                </ResponsiveContainer>
            </CardContent>
        </Card>
    );
}
