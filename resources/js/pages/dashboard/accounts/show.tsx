import AppHeader from '@/components/dashboard/app-header';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { TagBadge } from '@/components/tags/tag-badge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Account } from '@/types/account';
import { Head, router } from '@inertiajs/react';
import { Calendar, CheckCircle2, CreditCard, Edit, FolderOpen, Repeat, Tag, TrendingUp, XCircle } from 'lucide-react';

interface ShowAccountProps {
    account: Account;
}

export default function ShowAccount({ account }: ShowAccountProps) {
    const handleEdit = () => {
        router.get(route('dashboard.accounts.edit', { account: account.uuid }));
    };

    const handleMarkAsPaid = (transactionId: string) => {
        router.patch(
            route('dashboard.transactions.mark-as-paid', { transaction: transactionId }),
            {},
            {
                preserveScroll: true,
            }
        );
    };

    const handleMarkAsUnpaid = (transactionId: string) => {
        router.patch(
            route('dashboard.transactions.mark-as-unpaid', { transaction: transactionId }),
            {},
            {
                preserveScroll: true,
            }
        );
    };

    const statusVariant = account.status === 'active' ? 'default' : account.status === 'completed' ? 'secondary' : 'destructive';

    const progress = account.transactions_count
        ? Math.round(((account.paid_transactions_count || 0) / account.transactions_count) * 100)
        : 0;

    return (
        <DashboardLayout title="Detalhes da Conta">
            <Head title={`Conta: ${account.name}`} />

            <div className="space-y-6">
                {/* Header */}
                <AppHeader
                    title={account.name}
                    description={account.description || 'Detalhes da conta'}
                    routeBack={route('dashboard.accounts.index')}
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
                                {account.status_label}
                            </Badge>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Valor Total</CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL',
                                }).format(account.total_amount)}
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
                                    <div className="h-full bg-primary transition-all" style={{ width: `${progress}%` }} />
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    {account.paid_transactions_count || 0} de {account.transactions_count || 0} pagas
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Informações Detalhadas */}
                <Card>
                    <CardHeader>
                        <CardTitle>Informações da Conta</CardTitle>
                        <CardDescription>Detalhes completos do compromisso financeiro</CardDescription>
                    </CardHeader>
                    <CardContent className="grid gap-6">
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Carteira */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-primary/10 p-2">
                                    <CreditCard className="h-5 w-5 text-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Carteira</p>
                                    <p className="font-medium">{account.wallet?.name}</p>
                                    <p className="text-xs text-muted-foreground">{account.wallet?.type_label}</p>
                                </div>
                            </div>

                            {/* Categoria */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-primary/10 p-2">
                                    <FolderOpen className="h-5 w-5 text-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Categoria</p>
                                    <p className="font-medium">{account.category?.name}</p>
                                </div>
                            </div>

                            {/* Tags */}
                            {account.tags && account.tags.length > 0 && (
                                <div className="flex items-start gap-3">
                                    <div className="rounded-lg bg-primary/10 p-2">
                                        <Tag className="h-5 w-5 text-primary" />
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-sm text-muted-foreground">Tags</p>
                                        <div className="flex flex-wrap gap-1">
                                            {account.tags.map((tag) => (
                                                <TagBadge key={tag.id} name={tag.name} color={tag.color} />
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Tipo de Recorrência */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-primary/10 p-2">
                                    <Repeat className="h-5 w-5 text-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Tipo de Recorrência</p>
                                    <p className="font-medium">{account.recurrence_type_label}</p>
                                    {account.installments && <p className="text-xs text-muted-foreground">{account.installments} parcelas</p>}
                                </div>
                            </div>

                            {/* Data de Início */}
                            <div className="flex items-start gap-3">
                                <div className="rounded-lg bg-primary/10 p-2">
                                    <Calendar className="h-5 w-5 text-primary" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm text-muted-foreground">Data de Início</p>
                                    <p className="font-medium">
                                        {new Date(account.start_date + 'T00:00:00').toLocaleDateString('pt-BR', {
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

                {/* Lista de Transações */}
                <Card>
                    <CardHeader>
                        <CardTitle>Transações Geradas</CardTitle>
                        <CardDescription>
                            {account.transactions_count || 0} transação(ões) gerada(s) automaticamente
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {account.transactions && account.transactions.length > 0 ? (
                            <div className="space-y-2">
                                {account.transactions.map((transaction) => (
                                    <div
                                        key={transaction.uuid}
                                        className="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50"
                                    >
                                        <div className="space-y-1">
                                            <div className="flex items-center gap-2">
                                                <p className="font-medium">
                                                    {new Date(transaction.due_date + 'T00:00:00').toLocaleDateString('pt-BR')}
                                                </p>
                                                {transaction.installment_label && (
                                                    <Badge variant="outline" className="text-xs">
                                                        {transaction.installment_label}
                                                    </Badge>
                                                )}
                                            </div>
                                            {transaction.paid_at && (
                                                <p className="text-xs text-muted-foreground">
                                                    Pago em {new Date(transaction.paid_at + 'T00:00:00').toLocaleDateString('pt-BR')}
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex items-center gap-3">
                                            <p className="text-lg font-semibold">
                                                {new Intl.NumberFormat('pt-BR', {
                                                    style: 'currency',
                                                    currency: 'BRL',
                                                }).format(transaction.amount)}
                                            </p>
                                            <Badge
                                                variant={
                                                    transaction.status === 'paid'
                                                        ? 'default'
                                                        : transaction.status === 'overdue'
                                                          ? 'destructive'
                                                          : 'secondary'
                                                }
                                            >
                                                {transaction.status_label}
                                            </Badge>

                                            {/* Action Buttons */}
                                            {transaction.status !== 'paid' ? (
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    className="gap-2"
                                                    onClick={() => handleMarkAsPaid(transaction.uuid)}
                                                >
                                                    <CheckCircle2 className="h-4 w-4" />
                                                    Pagar
                                                </Button>
                                            ) : (
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    className="gap-2"
                                                    onClick={() => handleMarkAsUnpaid(transaction.uuid)}
                                                >
                                                    <XCircle className="h-4 w-4" />
                                                    Desfazer
                                                </Button>
                                            )}
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
