import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import TextMoney from '@/components/ui/text-money';
import TextSelect from '@/components/ui/text-select';
import { Budget } from '@/types/budget';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';

interface Category {
    id: number;
    name: string;
}

import { TagInput, TagOption } from '@/components/tags/tag-input';

interface BudgetFormProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    categories: Category[];
    budgetToEdit?: Budget | null;
    currentDate: string;
    tags: TagOption[];
}

export function BudgetForm({ open, onOpenChange, categories, budgetToEdit, currentDate, tags }: BudgetFormProps) {
    const { data, setData, post, put, processing, errors, clearErrors } = useForm({
        category_id: '',
        amount: 0,
        period: currentDate,
        recurrence: 'monthly',
        tags: [],
    });

    useEffect(() => {
        if (budgetToEdit) {
            setData({
                category_id: budgetToEdit.category_id.toString(),
                amount: budgetToEdit.amount,
                period: currentDate,
                recurrence: 'monthly',
                tags: budgetToEdit.tags?.map((t) => ({ name: t.name, color: t.color })) || [],
            });
        } else if (open) {
            setData({
                category_id: '',
                amount: 0,
                period: currentDate,
                recurrence: 'monthly',
                tags: [],
            });
        }
        clearErrors();
    }, [budgetToEdit, open, currentDate]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (budgetToEdit) {
            put(route('dashboard.budgets.update', budgetToEdit.id), {
                onSuccess: () => onOpenChange(false),
            });
        } else {
            post(route('dashboard.budgets.store'), {
                onSuccess: () => onOpenChange(false),
            });
        }
    };

    const categoryOptions = categories.map((cat) => ({
        value: cat.id.toString(),
        label: cat.name,
    }));

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{budgetToEdit ? 'Editar Orçamento' : 'Novo Orçamento'}</DialogTitle>
                    <DialogDescription>Defina um limite de gastos para uma categoria neste mês.</DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4 py-4">
                    <TextSelect
                        id="category_id"
                        label="Categoria"
                        options={categoryOptions}
                        value={data.category_id}
                        onValueChange={(val) => setData('category_id', val)}
                        error={errors.category_id}
                        disabled={!!budgetToEdit}
                        placeholder="Selecione uma categoria"
                    />

                    <TextMoney
                        id="amount"
                        name="amount"
                        label="Limite de Gastos"
                        value={data.amount}
                        onChange={(val) => setData('amount', val)}
                        error={errors.amount}
                    />

                    <div className="space-y-2">
                        <label className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Tags</label>
                        <TagInput value={data.tags} onChange={(newTags) => setData('tags', newTags)} suggestions={tags} />
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {budgetToEdit ? 'Salvar Alterações' : 'Criar Orçamento'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
