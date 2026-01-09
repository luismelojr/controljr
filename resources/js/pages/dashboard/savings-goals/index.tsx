import { ContributionModal } from '@/components/savings/contribution-modal';
import { GoalCard } from '@/components/savings/goal-card';
import { GoalForm } from '@/components/savings/goal-form';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { PageProps, SavingsGoal } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';

export default function SavingsGoalsIndex({ goals }: { goals: SavingsGoal[] }) {
    const { auth } = usePage<PageProps>().props;
    const [isFormOpen, setIsFormOpen] = useState(false);
    const [isContributionOpen, setIsContributionOpen] = useState(false);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);
    const [selectedGoal, setSelectedGoal] = useState<SavingsGoal | undefined>(undefined);
    const [contributionGoal, setContributionGoal] = useState<SavingsGoal | null>(null);
    const [goalToDelete, setGoalToDelete] = useState<SavingsGoal | null>(null);

    const handleCreate = () => {
        setSelectedGoal(undefined);
        setIsFormOpen(true);
    };

    const handleEdit = (goal: SavingsGoal) => {
        setSelectedGoal(goal);
        setIsFormOpen(true);
    };

    const handleContribute = (goal: SavingsGoal) => {
        setContributionGoal(goal);
        setIsContributionOpen(true);
    };

    const handleDelete = (goal: SavingsGoal) => {
        setGoalToDelete(goal);
        setIsDeleteOpen(true);
    };

    const confirmDelete = () => {
        if (goalToDelete) {
            router.delete(route('dashboard.savings-goals.destroy', { savings_goal: goalToDelete.uuid }));
        }
    };

    const activeGoals = goals.filter((g) => g.is_active);
    const completedGoals = goals.filter((g) => !g.is_active);

    return (
        <DashboardLayout title="Metas de Economia" subtitle="Crie e acompanhe suas metas de economia">
            <Head title="Metas de Economia" />

            <div className="container mx-auto space-y-8 py-12">
                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Suas Metas</h1>
                        <p className="text-muted-foreground">Gerencie seus objetivos financeiros e acompanhe seu progresso.</p>
                    </div>
                    <Button onClick={handleCreate}>
                        <Plus className="mr-2 h-4 w-4" />
                        Nova Meta
                    </Button>
                </div>

                {/* KPI Cards / Summary could go here */}

                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {activeGoals.map((goal) => (
                        <GoalCard key={goal.uuid} goal={goal} onContribute={handleContribute} onEdit={handleEdit} onDelete={handleDelete} />
                    ))}
                    {activeGoals.length === 0 && (
                        <Card className="col-span-full border-dashed p-8 text-center text-muted-foreground">
                            <CardContent>
                                <p>Você não tem metas ativas no momento.</p>
                                <Button variant="link" onClick={handleCreate}>
                                    Criar sua primeira meta
                                </Button>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {completedGoals.length > 0 && (
                    <div className="space-y-4">
                        <h3 className="text-xl font-semibold">Metas Concluídas</h3>
                        <div className="grid gap-6 opacity-75 grayscale transition-all hover:grayscale-0 md:grid-cols-2 lg:grid-cols-3">
                            {completedGoals.map((goal) => (
                                <GoalCard
                                    key={goal.uuid}
                                    goal={goal}
                                    onContribute={() => {}} // Can't contribute to completed
                                    onEdit={handleEdit}
                                    onDelete={handleDelete}
                                />
                            ))}
                        </div>
                    </div>
                )}

                <GoalForm open={isFormOpen} onOpenChange={setIsFormOpen} goal={selectedGoal} />

                <ContributionModal open={isContributionOpen} onOpenChange={setIsContributionOpen} goal={contributionGoal} />

                <ConfirmDeleteDialog
                    open={isDeleteOpen}
                    onOpenChange={setIsDeleteOpen}
                    onConfirm={confirmDelete}
                    title="Excluir Meta"
                    description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente a meta"
                    itemName={goalToDelete?.name}
                    confirmText="Excluir Meta"
                />
            </div>
        </DashboardLayout>
    );
}
