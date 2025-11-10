import AppHeader from '@/components/dashboard/app-header';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Transaction, TransactionSummary } from '@/types/transaction';
import { Head, router } from '@inertiajs/react';
import { AlertCircle, Check, CheckCircle2, Clock, TrendingDown, TrendingUp, X } from 'lucide-react';
import { useState } from 'react';

interface TransactionsMonthProps {
    transactions: Transaction[];
    summary: TransactionSummary;
    year: number;
    month: number;
    month_name: string;
}

export default function TransactionsMonth({ transactions, summary, year, month, month_name }: TransactionsMonthProps) {
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

    const handlePay = (uuid: string) => {
        setConfirmDialog({
            open: true,
            transactionUuid: uuid,
            action: 'pay',
            message: 'Deseja marcar esta transação como paga?',
        });
    };

    const handleUnpay = (uuid: string) => {
        setConfirmDialog({
            open: true,
            transactionUuid: uuid,
            action: 'unpay',
            message: 'Deseja marcar esta transação como não paga?',
        });
    };

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

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    return (
        <DashboardLayout title={`Transações - ${month_name}/${year}`}>
            <Head title={`Transações - ${month_name}/${year}`} />

            <div className="space-y-6">
                {/* Header */}
                <AppHeader
                    title={`${month_name} de ${year}`}
                    description="Resumo financeiro do mês"
                    routeBack={route('dashboard.transactions.index')}
                />

                {/* Cards de Resumo */}
                <div className="grid gap-4 md:grid-cols-4">
                    {/* Total Esperado */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Esperado</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(summary.total_expected)}</div>
                            <p className="text-xs text-muted-foreground">{summary.transactions_count} transações</p>
                        </CardContent>
                    </Card>

                    {/* Total Pago */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Pago</CardTitle>
                            <CheckCircle2 className="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">{formatCurrency(summary.total_spent)}</div>
                            <p className="text-xs text-muted-foreground">{summary.paid_count} pagas</p>
                        </CardContent>
                    </Card>

                    {/* Total Pendente */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Pendente</CardTitle>
                            <Clock className="h-4 w-4 text-yellow-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-yellow-600">{formatCurrency(summary.total_pending)}</div>
                            <p className="text-xs text-muted-foreground">{summary.pending_count} pendentes</p>
                        </CardContent>
                    </Card>

                    {/* Total Atrasado */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Atrasado</CardTitle>
                            <AlertCircle className="h-4 w-4 text-destructive" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-destructive">{formatCurrency(summary.total_overdue)}</div>
                            <p className="text-xs text-muted-foreground">{summary.overdue_count} atrasadas</p>
                        </CardContent>
                    </Card>
                </div>

                {/* Lista de Transações */}
                <Card>
                    <CardHeader>
                        <CardTitle>Transações do Mês</CardTitle>
                        <CardDescription>Todas as transações previstas para este período</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {transactions.length > 0 ? (
                            <div className="space-y-2">
                                {transactions.map((transaction) => (
                                    <div
                                        key={transaction.uuid}
                                        className="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50"
                                    >
                                        <div className="flex-1 space-y-1">
                                            <div className="flex items-center gap-2">
                                                <p className="font-medium">{transaction.account?.name}</p>
                                                {transaction.installment_label && (
                                                    <Badge variant="outline" className="text-xs">
                                                        {transaction.installment_label}
                                                    </Badge>
                                                )}
                                            </div>
                                            <div className="flex items-center gap-4 text-sm text-muted-foreground">
                                                <span>{transaction.wallet?.name}</span>
                                                <span>•</span>
                                                <span>{transaction.category?.name}</span>
                                                <span>•</span>
                                                <span>Vence em {new Date(transaction.due_date + 'T00:00:00').toLocaleDateString('pt-BR')}</span>
                                            </div>
                                            {transaction.paid_at && (
                                                <p className="text-xs text-green-600">
                                                    ✓ Pago em {new Date(transaction.paid_at + 'T00:00:00').toLocaleDateString('pt-BR')}
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex items-center gap-4">
                                            <div className="text-right">
                                                <p className="text-lg font-semibold">{formatCurrency(transaction.amount)}</p>
                                                <Badge
                                                    variant={
                                                        transaction.status === 'paid'
                                                            ? 'default'
                                                            : transaction.status === 'overdue'
                                                              ? 'destructive'
                                                              : 'secondary'
                                                    }
                                                    className="mt-1"
                                                >
                                                    {transaction.status_label}
                                                </Badge>
                                            </div>

                                            <div className="flex gap-2">
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
                                                    <Button
                                                        variant="ghost"
                                                        size="icon-sm"
                                                        onClick={() => handlePay(transaction.uuid)}
                                                        title="Marcar como paga"
                                                    >
                                                        <Check className="h-4 w-4 text-green-600" />
                                                    </Button>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="py-12 text-center text-muted-foreground">Nenhuma transação prevista para este mês</div>
                        )}
                    </CardContent>
                </Card>
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
