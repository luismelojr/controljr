import DashboardLayout from '@/components/layouts/dashboard-layout';
import { BudgetCard } from '@/components/budgets/budget-card';
import { BudgetForm } from '@/components/budgets/budget-form';
import { Button } from '@/components/ui/button';
import ExportButton from '@/components/ui/export-button';
import { Budget } from '@/types/budget';
import { Head, router } from '@inertiajs/react';
import { Plus, ChevronLeft, ChevronRight } from 'lucide-react';
import { useState } from 'react';
import { format, addMonths, subMonths, parseISO } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';

interface Props {
    budgets: Budget[];
    categories: { id: number; name: string }[];
    currentDate: string;
}

export default function BudgetsIndex({ budgets, categories, currentDate }: Props) {
    const [isFormOpen, setIsFormOpen] = useState(false);
    const [editingBudget, setEditingBudget] = useState<Budget | null>(null);
    const [deleteBudget, setDeleteBudget] = useState<Budget | null>(null);

    const date = parseISO(currentDate);

    const handleEdit = (budget: Budget) => {
        setEditingBudget(budget);
        setIsFormOpen(true);
    };

    const handleDelete = (budget: Budget) => {
        setDeleteBudget(budget);
    };

    const confirmDelete = () => {
        if (deleteBudget) {
            router.delete(route('dashboard.budgets.destroy', deleteBudget.id), {
                onSuccess: () => setDeleteBudget(null),
            });
        }
    };

    const handleMonthChange = (direction: 'prev' | 'next') => {
        const newDate = direction === 'prev' ? subMonths(date, 1) : addMonths(date, 1);
        router.get(route('dashboard.budgets.index'), {
            date: format(newDate, 'yyyy-MM-dd'),
        });
    };

    const handleCreate = () => {
        setEditingBudget(null);
        setIsFormOpen(true);
    };

    return (
        <DashboardLayout title="Orçamentos">
            <Head title="Orçamentos" />

            <div className="flex flex-col space-y-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <h1 className="text-3xl font-bold tracking-tight">Orçamentos</h1>
                        <div className="flex items-center gap-2 bg-muted rounded-md p-1">
                            <Button variant="ghost" size="icon" onClick={() => handleMonthChange('prev')}>
                                <ChevronLeft className="h-4 w-4" />
                            </Button>
                            <span className="min-w-[120px] text-center font-medium capitalize">
                                {format(date, 'MMMM yyyy', { locale: ptBR })}
                            </span>
                            <Button variant="ghost" size="icon" onClick={() => handleMonthChange('next')}>
                                <ChevronRight className="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        <ExportButton entity="budgets" />
                        <Button onClick={handleCreate}>
                            <Plus className="mr-2 h-4 w-4" />
                            Novo Orçamento
                        </Button>
                    </div>
                </div>

                {budgets.length === 0 ? (
                    <div className="flex flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center animate-in fade-in-50">
                        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                            <Plus className="h-6 w-6 text-muted-foreground" />
                        </div>
                        <h3 className="mt-4 text-lg font-semibold">Nenhum orçamento definido</h3>
                        <p className="mb-4 mt-2 text-sm text-muted-foreground">
                            Crie um orçamento para controlar seus gastos nesta categoria.
                        </p>
                        <Button onClick={handleCreate}>Criar Orçamento</Button>
                    </div>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {budgets.map((budget) => (
                            <BudgetCard
                                key={budget.id}
                                budget={budget}
                                onEdit={handleEdit}
                                onDelete={handleDelete}
                            />
                        ))}
                    </div>
                )}

                <BudgetForm
                    open={isFormOpen}
                    onOpenChange={setIsFormOpen}
                    categories={categories}
                    budgetToEdit={editingBudget}
                    currentDate={currentDate}
                />

                <ConfirmDeleteDialog
                    open={!!deleteBudget}
                    onOpenChange={(open) => !open && setDeleteBudget(null)}
                    onConfirm={confirmDelete}
                    title="Excluir Orçamento"
                    description={`Tem certeza que deseja excluir o orçamento de ${deleteBudget?.category}?`}
                />
            </div>
        </DashboardLayout>
    );
}
