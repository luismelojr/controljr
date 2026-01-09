import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { formatCurrency } from '@/lib/utils';
import { SavingsGoal } from '@/types';
import { Link } from '@inertiajs/react';
import { Target } from 'lucide-react';

interface GoalsWidgetProps {
    goals: SavingsGoal[];
}

export function GoalsWidget({ goals }: GoalsWidgetProps) {
    // Only show up to 3 goals
    const displayGoals = goals.slice(0, 3);

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium">Metas de Economia</CardTitle>
                <Link href={route('dashboard.savings-goals.index')}>
                    <Button variant="ghost" size="icon" className="h-8 w-8 text-muted-foreground">
                        <Target className="h-4 w-4" />
                    </Button>
                </Link>
            </CardHeader>
            <CardContent>
                <div className="space-y-4">
                    {displayGoals.length === 0 ? (
                        <div className="py-4 text-center text-sm text-muted-foreground">
                            <p>Nenhuma meta ativa.</p>
                            <Link href={route('dashboard.savings-goals.index')} className="mt-2 inline-block text-primary hover:underline">
                                Criar meta
                            </Link>
                        </div>
                    ) : (
                        displayGoals.map((goal) => {
                            const percentage = goal.progress_percentage ?? 0;
                            return (
                                <div key={goal.id} className="space-y-1">
                                    <div className="flex items-center justify-between text-sm">
                                        <div className="flex items-center gap-2">
                                            <span>{goal.icon}</span>
                                            <span className="font-medium">{goal.name}</span>
                                        </div>
                                        <span className="text-muted-foreground">{percentage}%</span>
                                    </div>
                                    <Progress
                                        value={percentage}
                                        className="h-1.5"
                                        indicatorClassName={goal.color ? `bg-[${goal.color}]` : undefined}
                                        style={{ '--progress-background': goal.color } as any}
                                    />
                                    <div className="flex justify-between text-xs text-muted-foreground">
                                        <span>{formatCurrency(goal.current_amount_cents / 100)}</span>
                                        <span>de {formatCurrency(goal.target_amount_cents / 100)}</span>
                                    </div>
                                </div>
                            );
                        })
                    )}

                    {displayGoals.length > 0 && (
                        <div className="pt-2">
                            <Link href={route('dashboard.savings-goals.index')}>
                                <Button variant="outline" size="sm" className="w-full">
                                    Ver todas
                                </Button>
                            </Link>
                        </div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
