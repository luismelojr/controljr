import { DataTable, DataTableHeader, DataTablePagination } from '@/components/datatable';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Transaction } from '@/types/transaction';
import { ColumnDef, PaginatedResponse } from '@/types/datatable';
import { Head, router } from '@inertiajs/react';
import { Check, Eye, X } from 'lucide-react';
import { useState } from 'react';

interface TransactionsIndexProps {
    transactions: PaginatedResponse<Transaction>;
    filters?: {
        filter?: Record<string, any>;
        sort?: string;
    };
}

export default function TransactionsIndex({ transactions, filters }: TransactionsIndexProps) {
    const [confirmDialog, setConfirmDialog] = useState<{
        open: boolean;
        transactionUuid: string | null;
        action: 'pay' | 'unpay';
        message: string;
    }>({
        open: false,
        transactionUuid: null,
        action: 'pay',
        message: '',
    });

    /**
     * Column definitions for DataTable
     */
    const columns: ColumnDef<Transaction>[] = [
        {
            key: 'due_date',
            label: 'Vencimento',
            sortable: true,
            render: (transaction) => (
                <div className="space-y-1">
                    <p className="font-medium">{new Date(transaction.due_date + 'T00:00:00').toLocaleDateString('pt-BR')}</p>
                    {transaction.paid_at && (
                        <p className="text-xs text-muted-foreground">
                            Pago em {new Date(transaction.paid_at + 'T00:00:00').toLocaleDateString('pt-BR')}
                        </p>
                    )}
                </div>
            ),
        },
        {
            key: 'account',
            label: 'Conta',
            render: (transaction) => (
                <div className="space-y-1">
                    <p className="font-medium">{transaction.account?.name}</p>
                    {transaction.installment_label && (
                        <Badge variant="outline" className="text-xs">
                            {transaction.installment_label}
                        </Badge>
                    )}
                </div>
            ),
        },
        {
            key: 'wallet',
            label: 'Carteira',
            render: (transaction) => (
                <div className="space-y-0.5">
                    <p className="text-sm">{transaction.wallet?.name}</p>
                    <p className="text-xs text-muted-foreground">{transaction.wallet?.type_label}</p>
                </div>
            ),
        },
        {
            key: 'category',
            label: 'Categoria',
            render: (transaction) => (
                <Badge variant="outline" className="font-normal">
                    {transaction.category?.name}
                </Badge>
            ),
        },
        {
            key: 'amount',
            label: 'Valor',
            sortable: true,
            render: (transaction) => (
                <p className="font-semibold">
                    {new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    }).format(transaction.amount)}
                </p>
            ),
        },
        {
            key: 'status',
            label: 'Status',
            render: (transaction) => {
                const variant =
                    transaction.status === 'paid' ? 'default' : transaction.status === 'overdue' ? 'destructive' : 'secondary';

                return <Badge variant={variant}>{transaction.status_label}</Badge>;
            },
        },
        {
            key: 'actions',
            label: 'Ações',
            className: 'text-right',
            render: (transaction) => (
                <div className="flex items-center justify-end gap-2">
                    <Button variant="ghost" size="icon-sm" onClick={() => handleShow(transaction.uuid)} title="Ver detalhes">
                        <Eye className="h-4 w-4" />
                    </Button>
                    {transaction.is_paid ? (
                        <Button
                            variant="ghost"
                            size="icon-sm"
                            onClick={() => handleUnpay(transaction.uuid)}
                            title="Marcar como não paga"
                        >
                            <X className="h-4 w-4 text-destructive" />
                        </Button>
                    ) : (
                        <Button variant="ghost" size="icon-sm" onClick={() => handlePay(transaction.uuid)} title="Marcar como paga">
                            <Check className="h-4 w-4 text-green-600" />
                        </Button>
                    )}
                </div>
            ),
        },
    ];

    /**
     * Navigate to show page
     */
    const handleShow = (uuid: string) => {
        router.get(route('dashboard.transactions.show', { transaction: uuid }));
    };

    /**
     * Mark transaction as paid
     */
    const handlePay = (uuid: string) => {
        setConfirmDialog({
            open: true,
            transactionUuid: uuid,
            action: 'pay',
            message: 'Deseja marcar esta transação como paga?',
        });
    };

    /**
     * Mark transaction as unpaid
     */
    const handleUnpay = (uuid: string) => {
        setConfirmDialog({
            open: true,
            transactionUuid: uuid,
            action: 'unpay',
            message: 'Deseja marcar esta transação como não paga?',
        });
    };

    /**
     * Confirm action
     */
    const confirmAction = () => {
        if (!confirmDialog.transactionUuid) return;

        const routeName =
            confirmDialog.action === 'pay' ? 'dashboard.transactions.mark-as-paid' : 'dashboard.transactions.mark-as-unpaid';

        router.patch(
            route(routeName, { transaction: confirmDialog.transactionUuid }),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    setConfirmDialog({ open: false, transactionUuid: null, action: 'pay', message: '' });
                },
            },
        );
    };

    return (
        <DashboardLayout title="Transações">
            <Head title="Transações" />

            <div className="space-y-6">
                {/* Header */}
                <DataTableHeader
                    title="Transações"
                    description="Visualize e gerencie todas as suas transações financeiras"
                    actions={[]}
                />

                {/* DataTable */}
                <DataTable data={transactions.data} columns={columns} activeSort={{ key: '', direction: null }} currentFilters={{}} />

                {/* Pagination */}
                {transactions.meta && transactions.links && <DataTablePagination meta={transactions.meta} links={transactions.links} />}
            </div>

            {/* Confirm Dialog */}
            <ConfirmDeleteDialog
                open={confirmDialog.open}
                onOpenChange={(open) => setConfirmDialog((prev) => ({ ...prev, open }))}
                onConfirm={confirmAction}
                title={confirmDialog.action === 'pay' ? 'Marcar como paga?' : 'Marcar como não paga?'}
                description={confirmDialog.message}
                confirmText="Confirmar"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
