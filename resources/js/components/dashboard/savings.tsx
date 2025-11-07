import { TrendingUp } from 'lucide-react';
import { cn } from '@/lib/utils';

interface SavingGoal {
    id: number;
    name: string;
    target: number;
    current: number;
    color: string;
}

interface SavingsProps {
    total: number;
    percentageChange: number;
    goals: SavingGoal[];
    className?: string;
}

export function Savings({ total, percentageChange, goals, className }: SavingsProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const calculateProgress = (current: number, target: number) => {
        return Math.round((current / target) * 100);
    };

    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="mb-6 flex items-center justify-between">
                <h3 className="text-lg font-semibold">Savings</h3>
                <select className="rounded-md border bg-background px-3 py-1.5 text-sm">
                    <option>This Week</option>
                    <option>This Month</option>
                </select>
            </div>

            <div className="mb-6">
                <div className="mb-2 flex items-baseline gap-2">
                    <h4 className="text-3xl font-bold">{formatCurrency(total)}</h4>
                    <span className="flex items-center gap-1 text-sm font-medium text-green-500">
                        <TrendingUp className="h-4 w-4" />
                        {percentageChange}%
                    </span>
                </div>
            </div>

            <div className="space-y-4">
                {goals.map((goal) => {
                    const progress = calculateProgress(goal.current, goal.target);

                    return (
                        <div key={goal.id} className="space-y-2">
                            <div className="flex items-center justify-between text-sm">
                                <div>
                                    <p className="font-medium">{goal.name}</p>
                                    <p className="text-muted-foreground">
                                        Target: {formatCurrency(goal.target)}
                                    </p>
                                </div>
                                <div className="text-right">
                                    <p className="font-semibold">{formatCurrency(goal.current)}</p>
                                    <p className="text-xs text-muted-foreground">{progress}%</p>
                                </div>
                            </div>

                            <div className="relative h-2 overflow-hidden rounded-full bg-muted">
                                <div
                                    className={cn('h-full rounded-full transition-all', goal.color)}
                                    style={{ width: `${progress}%` }}
                                />
                                {/* Striped pattern for incomplete portion */}
                                <div
                                    className="absolute right-0 top-0 h-full bg-gradient-to-r from-transparent to-muted"
                                    style={{
                                        width: `${100 - progress}%`,
                                        backgroundImage: 'repeating-linear-gradient(45deg, transparent, transparent 4px, rgba(0,0,0,0.05) 4px, rgba(0,0,0,0.05) 8px)',
                                    }}
                                />
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
