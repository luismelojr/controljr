import { ArrowDownLeft, ArrowUpRight, ChevronDown } from 'lucide-react';
import { cn } from '@/lib/utils';

interface Activity {
    id: number;
    name: string;
    accountNumber: string;
    date: string;
    amount: number;
    type: 'income' | 'expense';
    icon: string;
}

interface RecentlyActivityProps {
    activities: Activity[];
    className?: string;
}

export function RecentlyActivity({ activities, className }: RecentlyActivityProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const getActivityIcon = (icon: string) => {
        const iconClasses = 'flex h-10 w-10 items-center justify-center rounded-full';

        const iconMap: Record<string, { bg: string; content: React.ReactElement }> = {
            paypal: {
                bg: 'bg-blue-100 dark:bg-blue-950',
                content: (
                    <svg className="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.067 8.478c.492.88.556 2.014.3 3.327-.74 3.806-3.276 5.12-6.514 5.12h-.5a.805.805 0 00-.794.68l-.04.22-.63 3.993-.028.15a.806.806 0 01-.794.68H7.721a.483.483 0 01-.477-.558L7.418 21h.002l1.264-8.025.076-.48a.805.805 0 01.794-.681h1.667c3.238 0 5.774-1.314 6.514-5.12.26-1.313.195-2.447-.3-3.327a2.815 2.815 0 00-.287-.412c-.19.943-.602 1.726-1.24 2.345-.98.95-2.395 1.387-4.188 1.387h-3.21a.991.991 0 00-.978.836l-1.32 8.361a.594.594 0 00.586.681h2.345a.991.991 0 00.978-.836l.131-.832.63-3.993a.805.805 0 01.794-.681h.5c3.238 0 5.774-1.314 6.514-5.12z" />
                    </svg>
                ),
            },
            wise: {
                bg: 'bg-green-100 dark:bg-green-950',
                content: <span className="text-sm font-semibold text-green-600">W</span>,
            },
            atlassian: {
                bg: 'bg-blue-100 dark:bg-blue-950',
                content: <span className="text-sm font-semibold text-blue-600">A</span>,
            },
            dropbox: {
                bg: 'bg-blue-100 dark:bg-blue-950',
                content: <span className="text-sm font-semibold text-blue-600">D</span>,
            },
        };

        const iconData = iconMap[icon.toLowerCase()] || iconMap.paypal;

        return (
            <div className={cn(iconClasses, iconData.bg)}>
                {iconData.content}
            </div>
        );
    };

    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="mb-6 flex items-center justify-between">
                <h3 className="text-lg font-semibold">Recently Activity</h3>
                <select className="rounded-md border bg-background px-3 py-1.5 text-sm">
                    <option>This Week</option>
                    <option>This Month</option>
                </select>
            </div>

            <div className="space-y-1">
                <div className="grid grid-cols-[auto_1fr_auto] gap-4 rounded-lg bg-muted/50 px-3 py-2 text-sm font-medium text-muted-foreground">
                    <button className="flex items-center gap-1">
                        Name <ChevronDown className="h-4 w-4" />
                    </button>
                    <button className="flex items-center gap-1 justify-self-start">
                        Date <ChevronDown className="h-4 w-4" />
                    </button>
                    <span className="justify-self-end">Amount</span>
                </div>

                {activities.map((activity) => (
                    <div key={activity.id} className="grid grid-cols-[auto_1fr_auto] gap-4 rounded-lg px-3 py-3 transition-colors hover:bg-muted/50">
                        <div className="flex items-center gap-3">
                            {getActivityIcon(activity.icon)}
                            <div>
                                <p className="font-medium">{activity.name}</p>
                                <p className="text-sm text-muted-foreground">{activity.accountNumber}</p>
                            </div>
                        </div>

                        <div className="flex items-center">
                            <span className="text-sm text-muted-foreground">{activity.date}</span>
                        </div>

                        <div className="flex items-center justify-end">
                            <span className={cn('font-semibold', activity.type === 'income' ? 'text-green-600' : 'text-red-600')}>
                                {activity.type === 'income' ? '+' : '-'}
                                {formatCurrency(Math.abs(activity.amount))}
                            </span>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
