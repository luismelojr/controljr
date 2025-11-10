import { DataTable, DataTableHeader, DataTablePagination } from '@/components/datatable';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Income } from '@/types/income';
import { ColumnDef, PaginatedResponse } from '@/types/datatable';
import { Head, router } from '@inertiajs/react';
import { Edit, Eye, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface IncomesIndexProps {
    incomes: PaginatedResponse<Income>;
}

export default function IncomesIndex({ incomes }: IncomesIndexProps) {
    const [deleteDialog, setDeleteDialog] = useState<{
        open: boolean;
        incomeUuid: string | null;
        incomeName: string;
    }>({
        open: false,
        incomeUuid: null,
        incomeName: '',
    });

    /**
     * Column definitions for DataTable
     */
    const columns: ColumnDef<Income>[] = [
        {
            key: 'name',
            label: 'Nome',
            sortable: true,
            render: (income) => (
                <div className="space-y-1">
                    <p className="font-medium">{income.name}</p>
                    {income.notes && <p className="text-xs text-muted-foreground">{income.notes}</p>}
                </div>
            ),
        },
        {
            key: 'category',
            label: 'Categoria',
            render: (income) => (
                <Badge variant="outline" className="font-normal">
                    {income.category?.name}
                </Badge>
            ),
        },
        {
            key: 'recurrence_type',
            label: 'Tipo',
            render: (income) => (
                <div className="space-y-0.5">
                    <p className="text-sm">{income.recurrence_type_label}</p>
                    {income.installments && <p className="text-xs text-muted-foreground">{income.installments}x</p>}
                </div>
            ),
        },
        {
            key: 'total_amount',
            label: 'Valor Total',
            sortable: true,
            render: (income) => (
                <p className="font-medium text-green-600">
                    {new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    }).format(income.total_amount)}
                </p>
            ),
        },
        {
            key: 'status',
            label: 'Status',
            render: (income) => {
                const variant =
                    income.status === 'active' ? 'default' : income.status === 'completed' ? 'secondary' : 'destructive';

                return <Badge variant={variant}>{income.status_label}</Badge>;
            },
        },
        {
            key: 'progress',
            label: 'Progresso',
            render: (income) => {
                if (!income.transactions_count) return <span className="text-xs text-muted-foreground">-</span>;

                const percentage = Math.round(((income.received_transactions_count || 0) / income.transactions_count) * 100);

                return (
                    <div className="space-y-1">
                        <p className="text-xs">
                            {income.received_transactions_count || 0}/{income.transactions_count}
                        </p>
                        <div className="h-1.5 w-20 overflow-hidden rounded-full bg-secondary">
                            <div className="h-full bg-green-600 transition-all" style={{ width: `${percentage}%` }} />
                        </div>
                    </div>
                );
            },
        },
        {
            key: 'actions',
            label: 'Ações',
            className: 'text-right',
            render: (income) => (
                <div className="flex items-center justify-end gap-2">
                    <Button variant="ghost" size="icon-sm" onClick={() => handleShow(income.uuid)} title="Ver detalhes">
                        <Eye className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon-sm" onClick={() => handleEdit(income.uuid)} title="Editar receita">
                        <Edit className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon-sm" onClick={() => handleDelete(income.uuid, income.name)} title="Excluir receita">
                        <Trash2 className="h-4 w-4 text-destructive" />
                    </Button>
                </div>
            ),
        },
    ];

    /**
     * Navigate to create page
     */
    const handleCreate = () => {
        router.get(route('dashboard.incomes.create'));
    };

    /**
     * Navigate to show page
     */
    const handleShow = (uuid: string) => {
        router.get(route('dashboard.incomes.show', { income: uuid }));
    };

    /**
     * Navigate to edit page
     */
    const handleEdit = (uuid: string) => {
        router.get(route('dashboard.incomes.edit', { income: uuid }));
    };

    /**
     * Open delete confirmation dialog
     */
    const handleDelete = (uuid: string, name: string) => {
        setDeleteDialog({
            open: true,
            incomeUuid: uuid,
            incomeName: name,
        });
    };

    /**
     * Confirm delete action
     */
    const confirmDelete = () => {
        if (deleteDialog.incomeUuid) {
            router.delete(route('dashboard.incomes.destroy', { income: deleteDialog.incomeUuid }), {
                preserveScroll: true,
                onSuccess: () => {
                    setDeleteDialog({ open: false, incomeUuid: null, incomeName: '' });
                },
            });
        }
    };

    return (
        <DashboardLayout title="Receitas">
            <Head title="Receitas" />

            <div className="space-y-6">
                {/* Header */}
                <DataTableHeader
                    title="Receitas"
                    description="Gerencie suas fontes de renda e visualize recebimentos futuros"
                    actions={[
                        {
                            label: 'Nova Receita',
                            onClick: handleCreate,
                            icon: <Plus className="h-4 w-4" />,
                            variant: 'default',
                        },
                    ]}
                />

                {/* DataTable */}
                <DataTable
                    data={incomes.data}
                    columns={columns}
                    activeSort={{ key: '', direction: null }}
                    currentFilters={{}}
                />

                {/* Pagination */}
                {incomes.meta && incomes.links && <DataTablePagination meta={incomes.meta} links={incomes.links} />}
            </div>

            {/* Delete Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={deleteDialog.open}
                onOpenChange={(open) => setDeleteDialog((prev) => ({ ...prev, open }))}
                onConfirm={confirmDelete}
                title="Excluir receita?"
                description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente a receita e todas as suas transações."
                itemName={deleteDialog.incomeName}
                confirmText="Excluir"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
