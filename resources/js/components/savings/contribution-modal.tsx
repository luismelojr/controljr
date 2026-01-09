import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import TextMoney from '@/components/ui/text-money';
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

        // Garantir que amount seja um número válido
        const submitData = {
            amount: data.amount ? parseFloat(data.amount.toString()) : 0,
        };

        post(route('dashboard.savings-goals.contribute', { savings_goal: goal.uuid }), {
            data: submitData,
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
                        <TextMoney
                            label="Valor"
                            id="amount"
                            value={data.amount}
                            onValueChange={(value) => setData('amount', value || '')}
                            placeholder="R$ 0,00"
                            error={errors.amount}
                            required
                            autoFocus
                        />
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
