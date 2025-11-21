import { Budget } from '@/types/budget';
import { Progress } from '@/components/ui/progress';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Pencil, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

interface BudgetCardProps {
    budget: Budget;
    onEdit: (budget: Budget) => void;
    onDelete: (budget: Budget) => void;
}

export function BudgetCard({ budget, onEdit, onDelete }: BudgetCardProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const getProgressColor = (status: string) => {
        switch (status) {
            case 'red':
                return 'bg-red-500';
            case 'yellow':
                return 'bg-yellow-500';
            default:
                return 'bg-green-500';
        }
    };

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">
                    {budget.category}
                </CardTitle>
                <div className="flex items-center gap-2">
                    <Button variant="ghost" size="icon" className="h-8 w-8" onClick={() => onEdit(budget)}>
                        <Pencil className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon" className="h-8 w-8 text-destructive" onClick={() => onDelete(budget)}>
                        <Trash2 className="h-4 w-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div className="text-2xl font-bold">{formatCurrency(budget.amount)}</div>
                <p className="text-xs text-muted-foreground mb-4">
                    Gasto: {formatCurrency(budget.spent)} ({budget.percentage}%)
                </p>
                <Progress 
                    value={Math.min(budget.percentage, 100)} 
                    className={cn("h-2", getProgressColor(budget.status))}
                    indicatorClassName={getProgressColor(budget.status)}
                />
                <div className="mt-2 flex justify-between text-xs text-muted-foreground">
                    <span>Restante</span>
                    <span className={cn(budget.remaining < 0 && "text-red-500 font-bold")}>
                        {formatCurrency(budget.remaining)}
                    </span>
                </div>
            </CardContent>
        </Card>
    );
}
