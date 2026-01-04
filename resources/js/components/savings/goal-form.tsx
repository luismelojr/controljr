import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { SavingsGoal } from '@/types';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
// Assuming we have a category selector or just a select for now if categories are passed.
// For now, let's assume we pass categories as props or just skip category selection to keep it simple if not strictly required by user prompt (though schema has it).
// The plan mentions `category_id`. I'll assume we might pass categories list.

interface GoalFormProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    goal?: SavingsGoal;
}

export function GoalForm({ open, onOpenChange, goal }: GoalFormProps) {
    const isEditing = !!goal;

    const { data, setData, post, patch, processing, errors, reset, clearErrors } = useForm({
        name: goal?.name ?? '',
        description: goal?.description ?? '',
        target_amount: goal ? (goal.target_amount_cents / 100).toString() : '',
        target_date: goal?.target_date ?? '',
        category_id: goal?.category_id?.toString() ?? '',
        icon: goal?.icon ?? '🎯',
        color: goal?.color ?? '#10B981',
        is_active: goal?.is_active ?? true,
    });

    useEffect(() => {
        if (open) {
            reset();
            clearErrors();
            if (goal) {
                setData({
                    name: goal.name,
                    description: goal.description ?? '',
                    target_amount: (goal.target_amount_cents / 100).toFixed(2),
                    target_date: goal.target_date ?? '',
                    category_id: goal.category_id?.toString() ?? '',
                    icon: goal.icon,
                    color: goal.color,
                    is_active: goal.is_active,
                });
            } else {
                setData({
                    name: '',
                    description: '',
                    target_amount: '',
                    target_date: '',
                    category_id: '',
                    icon: '🎯',
                    color: '#10B981',
                    is_active: true,
                });
            }
        }
    }, [open, goal, reset, clearErrors, setData]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isEditing && goal) {
            patch(route('dashboard.savings-goals.update', { savingsGoal: goal.id }), {
                onSuccess: () => onOpenChange(false),
            });
        } else {
            post(route('dashboard.savings-goals.store'), {
                onSuccess: () => onOpenChange(false),
            });
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{isEditing ? 'Editar Meta' : 'Nova Meta de Economia'}</DialogTitle>
                    <DialogDescription>
                        {isEditing ? 'Atualize os detalhes da sua meta.' : 'Defina um objetivo para começar a economizar.'}
                    </DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="grid gap-4 py-4">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Nome</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            placeholder="Ex: Viagem de Férias"
                            required
                        />
                        {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="target_amount">Valor Alvo (R$)</Label>
                        <Input
                            id="target_amount"
                            type="number"
                            step="0.01"
                            value={data.target_amount}
                            onChange={(e) => setData('target_amount', e.target.value)}
                            placeholder="0,00"
                            required
                        />
                        {errors.target_amount && <p className="text-sm text-destructive">{errors.target_amount}</p>}
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="target_date">Data Alvo (Opcional)</Label>
                        <Input id="target_date" type="date" value={data.target_date} onChange={(e) => setData('target_date', e.target.value)} />
                        {errors.target_date && <p className="text-sm text-destructive">{errors.target_date}</p>}
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div className="grid gap-2">
                            <Label htmlFor="icon">Ícone</Label>
                            <Input id="icon" value={data.icon} onChange={(e) => setData('icon', e.target.value)} placeholder="🎯" maxLength={2} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="color">Cor</Label>
                            <div className="flex gap-2">
                                <Input
                                    id="color"
                                    type="color"
                                    value={data.color}
                                    onChange={(e) => setData('color', e.target.value)}
                                    className="h-10 w-12 p-1"
                                />
                                <Input
                                    value={data.color}
                                    onChange={(e) => setData('color', e.target.value)}
                                    placeholder="#000000"
                                    pattern="^#[0-9A-Fa-f]{6}$"
                                />
                            </div>
                        </div>
                    </div>

                    {errors.color && <p className="text-sm text-destructive">{errors.color}</p>}
                    {errors.icon && <p className="text-sm text-destructive">{errors.icon}</p>}

                    <div className="grid gap-2">
                        <Label htmlFor="description">Descrição (Opcional)</Label>
                        <Textarea
                            id="description"
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            placeholder="Detalhes adicionais..."
                        />
                    </div>
                </form>
                <DialogFooter>
                    <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                        Cancelar
                    </Button>
                    <Button type="submit" onClick={handleSubmit} disabled={processing}>
                        {isEditing ? 'Salvar Alterações' : 'Criar Meta'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
