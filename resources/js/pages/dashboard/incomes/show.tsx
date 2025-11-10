import AppHeader from '@/components/dashboard/app-header';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Income } from '@/types/income';
import { Head, router } from '@inertiajs/react';
import { Calendar, Edit, FolderOpen, Repeat, TrendingUp } from 'lucide-react';

interface ShowIncomeProps {
    income: Income;
}

export default function ShowIncome({ income }: ShowIncomeProps) {
    const handleEdit = () => {
        router.get(route('dashboard.incomes.edit', { income: income.uuid }));
    };

    const statusVariant = income.status === 'active' ? 'default' : income.status === 'completed' ? 'secondary' : 'destructive';

    const progress = income.transactions_count
        ? Math.round(((income.received_transactions_count || 0) / income.transactions_count) * 100)
        : 0;

    return (
        <DashboardLayout title="Detalhes da Receita">
            <Head title={`Receita: ${income.name}`} />

            <div className="space-y-6">
                {/* Header */}
                <AppHeader
                    title={income.name}
                    description={income.notes || 'Detalhes da receita'}
                    routeBack={route('dashboard.incomes.index')}
                    actions={[
                        {
                            label: 'Editar',
                            onClick: handleEdit,
                            icon: <Edit className="h-4 w-4" />,
                            variant: 'outline',
                        },
                    ]}
                />

                {/* Status e Progresso */}
                <div className="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Badge variant={statusVariant} className="text-base">
                                {income.status_label}
                            </Badge>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Valor Total</CardTitle>
                            <TrendingUp className="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">
                                {new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL',
                                }).format(income.total_amount)}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Progresso</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <p className="text-2xl font-bold">{progress}%</p>
                                <div className="h-2 overflow-hidden rounded-full bg-secondary">
                                    <div className="h-full bg-green-600 transition-all" style={{ width: `${progress}%` }} />
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    {income.received_transactions_count || 0} de {income.transactions_count || 0} recebidas
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Informações Detalhadas */}
                <Card>
                    <CardHeader>
                        <CardTitle>Informações da Receita</CardTitle>
                        <CardDescription>Detalhes completos da fonte de renda</CardDescription>
                    </CardHeader>
                    <CardContent className="grid gap-6">
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Categoria */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-green-600/10 p-2">
                                    <FolderOpen className="h-5 w-5 text-green-600" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Categoria</p>
                                    <p className="font-medium">{income.category?.name}</p>
                                </div>
                            </div>

                            {/* Tipo de Recorrência */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-green-600/10 p-2">
                                    <Repeat className="h-5 w-5 text-green-600" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Tipo de Recorrência</p>
                                    <p className="font-medium">{income.recurrence_type_label}</p>
                                    {income.installments && <p className="text-xs text-muted-foreground">{income.installments} parcelas</p>}
                                </div>
                            </div>

                            {/* Data de Início */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-green-600/10 p-2">
                                    <Calendar className="h-5 w-5 text-green-600" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Data de Início</p>
                                    <p className="font-medium">
                                        {new Date(income.start_date + 'T00:00:00').toLocaleDateString('pt-BR', {
                                            day: '2-digit',
                                            month: 'long',
                                            year: 'numeric',
                                        })}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Lista de Transações de Receita */}
                <Card>
                    <CardHeader>
                        <CardTitle>Transações de Recebimento</CardTitle>
                        <CardDescription>
                            {income.transactions_count || 0} transação(ões) gerada(s) automaticamente
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {income.incomeTransactions && income.incomeTransactions.length > 0 ? (
                            <div className="space-y-2">
                                {income.incomeTransactions.map((transaction) => (
                                    <div
                                        key={transaction.uuid}
                                        className="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50"
                                    >
                                        <div className="space-y-1">
                                            <div className="flex items-center gap-2">
                                                <p className="font-medium">
                                                    {new Date(transaction.expected_date + 'T00:00:00').toLocaleDateString('pt-BR')}
                                                </p>
                                                {transaction.installment_label && (
                                                    <Badge variant="outline" className="text-xs">
                                                        {transaction.installment_label}
                                                    </Badge>
                                                )}
                                            </div>
                                            {transaction.received_at && (
                                                <p className="text-xs text-muted-foreground">
                                                    Recebido em {new Date(transaction.received_at + 'T00:00:00').toLocaleDateString('pt-BR')}
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex items-center gap-4">
                                            <p className="text-lg font-semibold text-green-600">
                                                {new Intl.NumberFormat('pt-BR', {
                                                    style: 'currency',
                                                    currency: 'BRL',
                                                }).format(transaction.amount)}
                                            </p>
                                            <Badge
                                                variant={
                                                    transaction.status === 'received'
                                                        ? 'default'
                                                        : transaction.status === 'overdue'
                                                          ? 'destructive'
                                                          : 'secondary'
                                                }
                                            >
                                                {transaction.status_label}
                                            </Badge>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="py-8 text-center text-muted-foreground">Nenhuma transação gerada</div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </DashboardLayout>
    );
}
