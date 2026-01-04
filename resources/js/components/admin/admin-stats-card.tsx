import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { LucideIcon } from 'lucide-react';

interface AdminStatsCardProps {
    title: string;
    value: string | number;
    icon: LucideIcon;
    description?: string;
    trend?: {
        value: number;
        label: string;
        positive?: boolean;
    };
    className?: string;
}

export function AdminStatsCard({ title, value, icon: Icon, description, trend, className }: AdminStatsCardProps) {
    return (
        <Card className={cn('overflow-hidden', className)}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium text-muted-foreground">{title}</CardTitle>
                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 p-1.5">
                    <Icon className="h-4 w-4 text-primary" />
                </div>
            </CardHeader>
            <CardContent>
                <div className="text-2xl font-bold">{value}</div>
                {(description || trend) && (
                    <p className="mt-1 text-xs text-muted-foreground">
                        {trend && (
                            <span className={cn('mr-1 font-medium', trend.positive ? 'text-emerald-500' : 'text-red-500')}>
                                {trend.positive ? '+' : ''}
                                {trend.value}%
                            </span>
                        )}
                        {description}
                    </p>
                )}
            </CardContent>
        </Card>
    );
}
