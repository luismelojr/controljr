import { DataTable, DataTableHeader, DataTablePagination } from '@/components/datatable';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { IncomeTransaction } from '@/types/income-transaction';
import { ColumnDef, PaginatedResponse } from '@/types/datatable';
import { Head, router } from '@inertiajs/react';
import { Check, Eye, X } from 'lucide-react';
import { useState } from 'react';

interface IncomeTransactionsIndexProps {
    incomeTransactions: PaginatedResponse<IncomeTransaction>;
    filters?: {
        filter?: Record<string, any>;
        sort?: string;
    };
}

export default function IncomeTransactionsIndex({ incomeTransactions, filters }: IncomeTransactionsIndexProps) {
    const [confirmDialog, setConfirmDialog] = useState<{
        open: boolean;
        incomeTransactionUuid: string | null;
        action: 'receive' | 'unreceive';
        message: string;
    }>({
        open: false,
        incomeTransactionUuid: null,
        action: 'receive',
        message: '',
    });

    /**
     * Column definitions for DataTable
     */
    const columns: ColumnDef<IncomeTransaction>[] = [
        {
            key: 'expected_date',
            label: 'Data Esperada',
            sortable: true,
            render: (incomeTransaction) => (
                <div className="space-y-1">
                    <p className="font-medium">{new Date(incomeTransaction.expected_date + 'T00:00:00').toLocaleDateString('pt-BR')}</p>
                    {incomeTransaction.received_at && (
                        <p className="text-xs text-muted-foreground">
                            Recebido em {new Date(incomeTransaction.received_at + 'T00:00:00').toLocaleDateString('pt-BR')}
                        </p>
                    )}
                </div>
            ),
        },
        {
            key: 'income',
            label: 'Receita',
            render: (incomeTransaction) => (
                <div className="space-y-1">
                    <p className="font-medium">{incomeTransaction.income?.name}</p>
                    {incomeTransaction.installment_label && (
                        <Badge variant="outline" className="text-xs">
                            {incomeTransaction.installment_label}
                        </Badge>
                    )}
                </div>
            ),
        },
        {
            key: 'month_reference',
            label: 'Mês Referência',
            render: (incomeTransaction) => (
                <p className="text-sm">{incomeTransaction.month_reference_formatted}</p>
            ),
        },
        {
            key: 'category',
            label: 'Categoria',
            render: (incomeTransaction) => (
                <Badge variant="outline" className="font-normal">
                    {incomeTransaction.category?.name}
                </Badge>
            ),
        },
        {
            key: 'amount',
            label: 'Valor',
            sortable: true,
            render: (incomeTransaction) => (
                <p className="font-semibold text-green-600">
                    {new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    }).format(incomeTransaction.amount)}
                </p>
            ),
        },
        {
            key: 'status',
            label: 'Status',
            render: (incomeTransaction) => {
                const variant =
                    incomeTransaction.status === 'received' ? 'default' : incomeTransaction.status === 'overdue' ? 'destructive' : 'secondary';

                return <Badge variant={variant}>{incomeTransaction.status_label}</Badge>;
            },
        },
        {
            key: 'actions',
            label: 'Ações',
            className: 'text-right',
            render: (incomeTransaction) => (
                <div className="flex items-center justify-end gap-2">
                    <Button variant="ghost" size="icon-sm" onClick={() => handleShow(incomeTransaction.uuid)} title="Ver detalhes">
                        <Eye className="h-4 w-4" />
                    </Button>
                    {incomeTransaction.is_received ? (
                        <Button
                            variant="ghost"
                            size="icon-sm"
                            onClick={() => handleUnreceive(incomeTransaction.uuid)}
                            title="Marcar como não recebida"
                        >
                            <X className="h-4 w-4 text-destructive" />
                        </Button>
                    ) : (
                        <Button variant="ghost" size="icon-sm" onClick={() => handleReceive(incomeTransaction.uuid)} title="Marcar como recebida">
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
        router.get(route('dashboard.income-transactions.show', { incomeTransaction: uuid }));
    };

    /**
     * Mark income transaction as received
     */
    const handleReceive = (uuid: string) => {
        setConfirmDialog({
            open: true,
            incomeTransactionUuid: uuid,
            action: 'receive',
            message: 'Deseja marcar esta receita como recebida?',
        });
    };

    /**
     * Mark income transaction as not received
     */
    const handleUnreceive = (uuid: string) => {
        setConfirmDialog({
            open: true,
            incomeTransactionUuid: uuid,
            action: 'unreceive',
            message: 'Deseja marcar esta receita como não recebida?',
        });
    };

    /**
     * Confirm action
     */
    const confirmAction = () => {
        if (!confirmDialog.incomeTransactionUuid) return;

        const routeName =
            confirmDialog.action === 'receive' ? 'dashboard.income-transactions.mark-as-received' : 'dashboard.income-transactions.mark-as-not-received';

        router.patch(
            route(routeName, { incomeTransaction: confirmDialog.incomeTransactionUuid }),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    setConfirmDialog({ open: false, incomeTransactionUuid: null, action: 'receive', message: '' });
                },
            },
        );
    };

    return (
        <DashboardLayout title="Transações de Receita">
            <Head title="Transações de Receita" />

            <div className="space-y-6">
                {/* Header */}
                <DataTableHeader
                    title="Transações de Receita"
                    description="Visualize e gerencie todas as suas transações de receita"
                    actions={[]}
                />

                {/* DataTable */}
                <DataTable data={incomeTransactions.data} columns={columns} activeSort={{ key: '', direction: null }} currentFilters={{}} />

                {/* Pagination */}
                {incomeTransactions.meta && incomeTransactions.links && <DataTablePagination meta={incomeTransactions.meta} links={incomeTransactions.links} />}
            </div>

            {/* Confirm Dialog */}
            <ConfirmDeleteDialog
                open={confirmDialog.open}
                onOpenChange={(open) => setConfirmDialog((prev) => ({ ...prev, open }))}
                onConfirm={confirmAction}
                title={confirmDialog.action === 'receive' ? 'Marcar como recebida?' : 'Marcar como não recebida?'}
                description={confirmDialog.message}
                confirmText="Confirmar"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
