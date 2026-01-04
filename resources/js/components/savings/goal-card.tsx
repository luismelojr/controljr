import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { cn, formatCurrency } from '@/lib/utils';
import { SavingsGoal } from '@/types';
import { Pencil, Plus, Trash2 } from 'lucide-react';

interface GoalCardProps {
    goal: SavingsGoal;
    onContribute: (goal: SavingsGoal) => void;
    onEdit: (goal: SavingsGoal) => void;
    onDelete: (goal: SavingsGoal) => void;
}

export function GoalCard({ goal, onContribute, onEdit, onDelete }: GoalCardProps) {
    // Accessors should be available in the goal object via serialization
    // If not, we might need to calculate them here or ensure resource handles it.
    // Assuming resource provides 'progress_percentage', 'remaining_amount', 'days_remaining'
    // or we calculate. Let's calculate for safety if not standard.

    // Actually, Model accessors are `progress_percentage`, `remaining_amount` (snake case in JSON usually)

    // We expect the goal object to have these fields from the resource/controller response.
    // Ideally user defined types match.

    const percentage = goal.progress_percentage ?? 0;
    const isCompleted = percentage >= 100;

    return (
        <Card className={cn('relative overflow-hidden transition-all hover:shadow-md', isCompleted && 'border-green-500/50 bg-green-500/5')}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">
                    <span className="mr-2 text-lg">{goal.icon}</span>
                    {goal.name}
                </CardTitle>
                <div className="flex gap-1">
                    <Button variant="ghost" size="icon" className="h-8 w-8 text-muted-foreground hover:text-primary" onClick={() => onEdit(goal)}>
                        <Pencil className="h-4 w-4" />
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon"
                        className="h-8 w-8 text-muted-foreground hover:text-destructive"
                        onClick={() => onDelete(goal)}
                    >
                        <Trash2 className="h-4 w-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div className="text-2xl font-bold">{formatCurrency(goal.current_amount_cents / 100)}</div>
                <p className="text-xs text-muted-foreground">de {formatCurrency(goal.target_amount_cents / 100)}</p>
                <Progress
                    value={percentage}
                    className="mt-4 h-2"
                    indicatorClassName={goal.color ? `bg-[${goal.color}]` : undefined}
                    style={{ '--progress-background': goal.color } as any}
                />
                <div className="mt-2 flex items-center justify-between text-xs text-muted-foreground">
                    <span>{percentage}% atingido</span>
                    {goal.target_date && <span>{new Date(goal.target_date).toLocaleDateString()}</span>}
                </div>

                {!isCompleted && (
                    <Button className="mt-4 w-full" size="sm" onClick={() => onContribute(goal)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Adicionar
                    </Button>
                )}
            </CardContent>
        </Card>
    );
}
