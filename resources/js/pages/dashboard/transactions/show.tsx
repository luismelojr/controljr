import AppHeader from '@/components/dashboard/app-header';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Transaction } from '@/types/transaction';
import { Head, router } from '@inertiajs/react';
import { Calendar, Check, CreditCard, FileText, FolderOpen, Hash, X } from 'lucide-react';
import { useState } from 'react';

interface ShowTransactionProps {
    transaction: Transaction;
}

export default function ShowTransaction({ transaction }: ShowTransactionProps) {
    const [confirmDialog, setConfirmDialog] = useState<{
        open: boolean;
        action: 'pay' | 'unpay';
        message: string;
    }>({
        open: false,
        action: 'pay',
        message: '',
    });

    const handlePay = () => {
        setConfirmDialog({
            open: true,
            action: 'pay',
            message: 'Deseja marcar esta transação como paga?',
        });
    };

    const handleUnpay = () => {
        setConfirmDialog({
            open: true,
            action: 'unpay',
            message: 'Deseja marcar esta transação como não paga?',
        });
    };

    const confirmAction = () => {
        const routeName =
            confirmDialog.action === 'pay' ? 'dashboard.transactions.mark-as-paid' : 'dashboard.transactions.mark-as-unpaid';

        router.patch(
            route(routeName, { transaction: transaction.uuid }),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    setConfirmDialog({ open: false, action: 'pay', message: '' });
                },
            },
        );
    };

    const statusVariant = transaction.status === 'paid' ? 'default' : transaction.status === 'overdue' ? 'destructive' : 'secondary';

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    return (
        <DashboardLayout title="Detalhes da Transação">
            <Head title="Detalhes da Transação" />

            <div className="space-y-6">
                {/* Header */}
                <AppHeader
                    title={transaction.account?.name || 'Transação'}
                    description="Detalhes da transação"
                    routeBack={route('dashboard.transactions.index')}
                    actions={[
                        transaction.is_paid
                            ? {
                                  label: 'Marcar como não paga',
                                  onClick: handleUnpay,
                                  icon: <X className="h-4 w-4" />,
                                  variant: 'destructive',
                              }
                            : {
                                  label: 'Marcar como paga',
                                  onClick: handlePay,
                                  icon: <Check className="h-4 w-4" />,
                                  variant: 'default',
                              },
                    ]}
                />

                {/* Cards de Informações Principais */}
                <div className="grid gap-4 md:grid-cols-3">
                    {/* Valor */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Valor</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(transaction.amount)}</div>
                            {transaction.installment_label && (
                                <Badge variant="outline" className="mt-2">
                                    Parcela {transaction.installment_label}
                                </Badge>
                            )}
                        </CardContent>
                    </Card>

                    {/* Status */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Badge variant={statusVariant} className="text-base">
                                {transaction.status_label}
                            </Badge>
                            {transaction.paid_at && (
                                <p className="mt-2 text-xs text-muted-foreground">
                                    Pago em {new Date(transaction.paid_at + 'T00:00:00').toLocaleDateString('pt-BR')}
                                </p>
                            )}
                        </CardContent>
                    </Card>

                    {/* Vencimento */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Vencimento</CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-lg font-bold">
                                {new Date(transaction.due_date + 'T00:00:00').toLocaleDateString('pt-BR', {
                                    day: '2-digit',
                                    month: 'long',
                                    year: 'numeric',
                                })}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Detalhes Completos */}
                <Card>
                    <CardHeader>
                        <CardTitle>Informações Completas</CardTitle>
                        <CardDescription>Todos os detalhes desta transação</CardDescription>
                    </CardHeader>
                    <CardContent className="grid gap-6">
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Conta Vinculada */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-primary/10 p-2">
                                    <FileText className="h-5 w-5 text-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Conta Vinculada</p>
                                    <p className="font-medium">{transaction.account?.name}</p>
                                    <p className="text-xs text-muted-foreground">{transaction.account?.recurrence_type_label}</p>
                                </div>
                            </div>

                            {/* Carteira */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-primary/10 p-2">
                                    <CreditCard className="h-5 w-5 text-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Carteira</p>
                                    <p className="font-medium">{transaction.wallet?.name}</p>
                                    <p className="text-xs text-muted-foreground">{transaction.wallet?.type_label}</p>
                                </div>
                            </div>

                            {/* Categoria */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-primary/10 p-2">
                                    <FolderOpen className="h-5 w-5 text-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Categoria</p>
                                    <p className="font-medium">{transaction.category?.name}</p>
                                </div>
                            </div>

                            {/* Parcelas (se aplicável) */}
                            {transaction.installment_number && transaction.total_installments && (
                                <div className="flex items-start gap-3">
                                    <div className="rounded-lg bg-primary/10 p-2">
                                        <Hash className="h-5 w-5 text-primary" />
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-sm text-muted-foreground">Parcelamento</p>
                                        <p className="font-medium">
                                            Parcela {transaction.installment_number} de {transaction.total_installments}
                                        </p>
                                        <p className="text-xs text-muted-foreground">{transaction.installment_label}</p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </CardContent>
                </Card>

                {/* Botão para ver conta completa */}
                {transaction.account && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Ver Conta Completa</CardTitle>
                            <CardDescription>Visualize todas as transações desta conta</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button
                                variant="outline"
                                onClick={() => router.get(route('dashboard.accounts.show', { account: transaction.account!.uuid }))}
                            >
                                Ver todas as transações de "{transaction.account.name}"
                            </Button>
                        </CardContent>
                    </Card>
                )}
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
