import AppHeader from '@/components/dashboard/app-header';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { IncomeTransaction } from '@/types/income-transaction';
import { Head, router } from '@inertiajs/react';
import { Calendar, Check, FileText, FolderOpen, Hash, X } from 'lucide-react';
import { useState } from 'react';

interface ShowIncomeTransactionProps {
    incomeTransaction: IncomeTransaction;
}

export default function ShowIncomeTransaction({ incomeTransaction }: ShowIncomeTransactionProps) {
    const [confirmDialog, setConfirmDialog] = useState<{
        open: boolean;
        action: 'receive' | 'unreceive';
        message: string;
    }>({
        open: false,
        action: 'receive',
        message: '',
    });

    const handleReceive = () => {
        setConfirmDialog({
            open: true,
            action: 'receive',
            message: 'Deseja marcar esta receita como recebida?',
        });
    };

    const handleUnreceive = () => {
        setConfirmDialog({
            open: true,
            action: 'unreceive',
            message: 'Deseja marcar esta receita como não recebida?',
        });
    };

    const confirmAction = () => {
        const routeName =
            confirmDialog.action === 'receive' ? 'dashboard.income-transactions.mark-as-received' : 'dashboard.income-transactions.mark-as-not-received';

        router.patch(
            route(routeName, { incomeTransaction: incomeTransaction.uuid }),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    setConfirmDialog({ open: false, action: 'receive', message: '' });
                },
            },
        );
    };

    const statusVariant = incomeTransaction.status === 'received' ? 'default' : incomeTransaction.status === 'overdue' ? 'destructive' : 'secondary';

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    return (
        <DashboardLayout title="Detalhes da Receita">
            <Head title="Detalhes da Receita" />

            <div className="space-y-6">
                {/* Header */}
                <AppHeader
                    title={incomeTransaction.income?.name || 'Receita'}
                    description="Detalhes da transação de receita"
                    routeBack={route('dashboard.income-transactions.index')}
                    actions={[
                        incomeTransaction.is_received
                            ? {
                                  label: 'Marcar como não recebida',
                                  onClick: handleUnreceive,
                                  icon: <X className="h-4 w-4" />,
                                  variant: 'destructive',
                              }
                            : {
                                  label: 'Marcar como recebida',
                                  onClick: handleReceive,
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
                            <div className="text-2xl font-bold text-green-600">{formatCurrency(incomeTransaction.amount)}</div>
                            {incomeTransaction.installment_label && (
                                <Badge variant="outline" className="mt-2">
                                    Parcela {incomeTransaction.installment_label}
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
                                {incomeTransaction.status_label}
                            </Badge>
                            {incomeTransaction.received_at && (
                                <p className="mt-2 text-xs text-muted-foreground">
                                    Recebido em {new Date(incomeTransaction.received_at + 'T00:00:00').toLocaleDateString('pt-BR')}
                                </p>
                            )}
                        </CardContent>
                    </Card>

                    {/* Data Esperada */}
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Data Esperada</CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-lg font-bold">
                                {new Date(incomeTransaction.expected_date + 'T00:00:00').toLocaleDateString('pt-BR', {
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
                        <CardDescription>Todos os detalhes desta receita</CardDescription>
                    </CardHeader>
                    <CardContent className="grid gap-6">
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Receita Vinculada */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-green-500/10 p-2">
                                    <FileText className="h-5 w-5 text-green-600" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Receita Vinculada</p>
                                    <p className="font-medium">{incomeTransaction.income?.name}</p>
                                    <p className="text-xs text-muted-foreground">{incomeTransaction.income?.recurrence_type_label}</p>
                                </div>
                            </div>

                            {/* Mês de Referência */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-green-500/10 p-2">
                                    <Calendar className="h-5 w-5 text-green-600" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Mês de Referência</p>
                                    <p className="font-medium">{incomeTransaction.month_reference_formatted}</p>
                                </div>
                            </div>

                            {/* Categoria */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-green-500/10 p-2">
                                    <FolderOpen className="h-5 w-5 text-green-600" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Categoria</p>
                                    <p className="font-medium">{incomeTransaction.category?.name}</p>
                                </div>
                            </div>

                            {/* Parcelas (se aplicável) */}
                            {incomeTransaction.installment_number && incomeTransaction.total_installments && (
                                <div className="flex items-start gap-3">
                                    <div className="rounded-lg bg-green-500/10 p-2">
                                        <Hash className="h-5 w-5 text-green-600" />
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-sm text-muted-foreground">Parcelamento</p>
                                        <p className="font-medium">
                                            Parcela {incomeTransaction.installment_number} de {incomeTransaction.total_installments}
                                        </p>
                                        <p className="text-xs text-muted-foreground">{incomeTransaction.installment_label}</p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </CardContent>
                </Card>

                {/* Botão para ver receita completa */}
                {incomeTransaction.income && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Ver Receita Completa</CardTitle>
                            <CardDescription>Visualize todas as transações desta receita</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button
                                variant="outline"
                                onClick={() => router.get(route('dashboard.incomes.show', { income: incomeTransaction.income!.uuid }))}
                            >
                                Ver todas as transações de "{incomeTransaction.income.name}"
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
                title={confirmDialog.action === 'receive' ? 'Marcar como recebida?' : 'Marcar como não recebida?'}
                description={confirmDialog.message}
                confirmText="Confirmar"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
