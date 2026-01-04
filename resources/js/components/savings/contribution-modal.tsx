import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { SavingsGoal } from '@/types';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';

interface ContributionModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    goal: SavingsGoal | null;
}

export function ContributionModal({ open, onOpenChange, goal }: ContributionModalProps) {
    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        amount: '',
    });

    useEffect(() => {
        if (open) {
            reset();
            clearErrors();
        }
    }, [open, reset, clearErrors]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (!goal) return;

        post(route('dashboard.savings-goals.contribute', { savingsGoal: goal.id }), {
            onSuccess: () => onOpenChange(false),
        });
    };

    if (!goal) return null;

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Adicionar Contribuição</DialogTitle>
                    <DialogDescription>
                        Adicione um valor para a meta: <strong>{goal.name}</strong>.
                    </DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="grid gap-4 py-4">
                    <div className="grid gap-2">
                        <Label htmlFor="amount">Valor (R$)</Label>
                        <Input
                            id="amount"
                            type="number"
                            step="0.01"
                            value={data.amount}
                            onChange={(e) => setData('amount', e.target.value)}
                            placeholder="0,00"
                            required
                            autoFocus
                        />
                        {errors.amount && <p className="text-sm text-destructive">{errors.amount}</p>}
                    </div>
                </form>
                <DialogFooter>
                    <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                        Cancelar
                    </Button>
                    <Button type="submit" onClick={handleSubmit} disabled={processing}>
                        Adicionar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
