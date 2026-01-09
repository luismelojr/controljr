import { TrendingUp, TrendingDown, Calendar } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/lib/format';

interface StatsCardProps {
    title: string;
    value: number;
    percentageChange: number;
    comparedTo?: string;
    month?: string;
    className?: string;
}

export function StatsCard({ title, value, percentageChange, comparedTo = 'Compared to last month', month = 'July 16', className }: StatsCardProps) {
    const isPositive = percentageChange >= 0;

    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="mb-4 flex items-center justify-between">
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                    <Calendar className="h-4 w-4" />
                    <span>{month}</span>
                </div>
            </div>

            <div className="mb-1 flex items-center gap-2 text-sm text-muted-foreground">
                <span>{title}</span>
                <div className="h-4 w-4 rounded-full border border-muted-foreground/30 flex items-center justify-center">
                    <span className="text-xs">?</span>
                </div>
            </div>

            <h3 className="mb-2 text-3xl font-bold">{formatCurrency(value)}</h3>

            <div className="flex items-center gap-1 text-sm">
                {isPositive ? (
                    <>
                        <TrendingUp className="h-4 w-4 text-green-500" />
                        <span className="font-medium text-green-500">+{percentageChange}%</span>
                    </>
                ) : (
                    <>
                        <TrendingDown className="h-4 w-4 text-red-500" />
                        <span className="font-medium text-red-500">{percentageChange}%</span>
                    </>
                )}
                <span className="text-muted-foreground">{comparedTo}</span>
            </div>
        </div>
    );
}
