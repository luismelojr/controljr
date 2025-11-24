import { DataTable, DataTableHeader, DataTablePagination } from '@/components/datatable';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import ExportButton from '@/components/ui/export-button';
import { Button } from '@/components/ui/button';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Account } from '@/types/account';
import { ColumnDef, PaginatedResponse } from '@/types/datatable';
import { Head, router } from '@inertiajs/react';
import { Edit, Eye, Plus, Trash2 } from 'lucide-react';
import { useMemo, useState } from 'react';

interface AccountsIndexProps {
    accounts: PaginatedResponse<Account>;
}

export default function AccountsIndex({ accounts }: AccountsIndexProps) {
    const [deleteDialog, setDeleteDialog] = useState<{
        open: boolean;
        accountUuid: string | null;
        accountName: string;
    }>({
        open: false,
        accountUuid: null,
        accountName: '',
    });

    /**
     * Column definitions for DataTable
     */
    const columns: ColumnDef<Account>[] = [
        {
            key: 'name',
            label: 'Nome',
            sortable: true,
            render: (account) => (
                <div className="space-y-1">
                    <p className="font-medium">{account.name}</p>
                    {account.description && <p className="text-xs text-muted-foreground">{account.description}</p>}
                </div>
            ),
        },
        {
            key: 'wallet',
            label: 'Carteira',
            render: (account) => (
                <div className="space-y-0.5">
                    <p className="text-sm">{account.wallet?.name}</p>
                    <p className="text-xs text-muted-foreground">{account.wallet?.type_label}</p>
                </div>
            ),
        },
        {
            key: 'category',
            label: 'Categoria',
            render: (account) => (
                <Badge variant="outline" className="font-normal">
                    {account.category?.name}
                </Badge>
            ),
        },
        {
            key: 'recurrence_type',
            label: 'Tipo',
            render: (account) => (
                <div className="space-y-0.5">
                    <p className="text-sm">{account.recurrence_type_label}</p>
                    {account.installments && <p className="text-xs text-muted-foreground">{account.installments}x</p>}
                </div>
            ),
        },
        {
            key: 'total_amount',
            label: 'Valor Total',
            sortable: true,
            render: (account) => (
                <p className="font-medium">
                    {new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    }).format(account.total_amount)}
                </p>
            ),
        },
        {
            key: 'status',
            label: 'Status',
            render: (account) => {
                const variant =
                    account.status === 'active' ? 'default' : account.status === 'completed' ? 'secondary' : 'destructive';

                return <Badge variant={variant}>{account.status_label}</Badge>;
            },
        },
        {
            key: 'progress',
            label: 'Progresso',
            render: (account) => {
                if (!account.transactions_count) return <span className="text-xs text-muted-foreground">-</span>;

                const percentage = Math.round(((account.paid_transactions_count || 0) / account.transactions_count) * 100);

                return (
                    <div className="space-y-1">
                        <p className="text-xs">
                            {account.paid_transactions_count || 0}/{account.transactions_count}
                        </p>
                        <div className="h-1.5 w-20 overflow-hidden rounded-full bg-secondary">
                            <div className="h-full bg-primary transition-all" style={{ width: `${percentage}%` }} />
                        </div>
                    </div>
                );
            },
        },
        {
            key: 'actions',
            label: 'Ações',
            className: 'text-right',
            render: (account) => (
                <div className="flex items-center justify-end gap-2">
                    <Button variant="ghost" size="icon-sm" onClick={() => handleShow(account.uuid)} title="Ver detalhes">
                        <Eye className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon-sm" onClick={() => handleEdit(account.uuid)} title="Editar conta">
                        <Edit className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon-sm" onClick={() => handleDelete(account.uuid, account.name)} title="Excluir conta">
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
        router.get(route('dashboard.accounts.create'));
    };

    /**
     * Navigate to show page
     */
    const handleShow = (uuid: string) => {
        router.get(route('dashboard.accounts.show', { account: uuid }));
    };

    /**
     * Navigate to edit page
     */
    const handleEdit = (uuid: string) => {
        router.get(route('dashboard.accounts.edit', { account: uuid }));
    };

    /**
     * Open delete confirmation dialog
     */
    const handleDelete = (uuid: string, name: string) => {
        setDeleteDialog({
            open: true,
            accountUuid: uuid,
            accountName: name,
        });
    };

    /**
     * Confirm delete action
     */
    const confirmDelete = () => {
        if (deleteDialog.accountUuid) {
            router.delete(route('dashboard.accounts.destroy', { account: deleteDialog.accountUuid }), {
                preserveScroll: true,
                onSuccess: () => {
                    setDeleteDialog({ open: false, accountUuid: null, accountName: '' });
                },
            });
        }
    };

    return (
        <DashboardLayout title="Contas">
            <Head title="Contas" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Contas</h1>
                        <p className="text-muted-foreground">Gerencie seus compromissos financeiros e visualize transações futuras</p>
                    </div>
                    <div className="flex items-center gap-2">
                        <ExportButton entity="accounts" />
                        <Button onClick={handleCreate}>
                            <Plus className="mr-2 h-4 w-4" />
                            Nova Conta
                        </Button>
                    </div>
                </div>

                {/* DataTable */}
                <DataTable
                    data={accounts.data}
                    columns={columns}
                    activeSort={{ key: '', direction: null }}
                    currentFilters={{}}
                />

                {/* Pagination */}
                {accounts.meta && accounts.links && <DataTablePagination meta={accounts.meta} links={accounts.links} />}
            </div>

            {/* Delete Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={deleteDialog.open}
                onOpenChange={(open) => setDeleteDialog((prev) => ({ ...prev, open }))}
                onConfirm={confirmDelete}
                title="Excluir conta?"
                description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente a conta e todas as suas transações."
                itemName={deleteDialog.accountName}
                confirmText="Excluir"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
