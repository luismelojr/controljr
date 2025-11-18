import DashboardLayout from '@/components/layouts/dashboard-layout';
import CustomToast from '@/components/ui/custom-toast';
import { BalanceCard } from '@/components/dashboard/balance-card';
import { StatsCard } from '@/components/dashboard/stats-card';
import { AccountsEnding } from '@/components/dashboard/accounts-ending';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { PageProps } from '@/types';
import { usePage } from '@inertiajs/react';
import { AlertCircle, Bell, CreditCard, CheckCircle2, Clock } from 'lucide-react';
import { router } from '@inertiajs/react';

interface DashboardData {
    total_balance: number;
    balance_percentage_change: number;
    monthly_expenses: number;
    expenses_percentage_change: number;
    monthly_income: number;
    income_percentage_change: number;
    upcoming_transactions: Array<{
        id: string;
        name: string;
        category: string;
        due_date: string;
        due_date_raw: string;
        amount: number;
        status: string;
        installment_info: string | null;
    }>;
    wallets_summary: Array<{
        id: string;
        name: string;
        type: string;
        balance: number;
        card_limit?: number;
        card_limit_used?: number;
        usage_percentage?: number;
    }>;
    accounts_ending_this_month: Array<{
        uuid: string;
        name: string;
        category: string;
        wallet: string;
        total_amount: number;
        installments: number;
        installment_info: string;
        due_date: string;
    }>;
    unread_notifications_count: number;
    unread_notifications: Array<{
        id: string;
        title: string;
        message: string;
        type: string;
        created_at: string;
    }>;
}

export default function Home() {
    const { auth, ...dashboardData } = usePage<PageProps & DashboardData>().props;

    const firstName = auth.user?.name ? auth.user.name.split(' ')[0] : 'User';

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const getNotificationVariant = (type: string) => {
        switch (type) {
            case 'danger':
                return 'destructive';
            case 'warning':
                return 'default';
            case 'success':
                return 'default';
            default:
                return 'secondary';
        }
    };

    const handleMarkAsPaid = (transactionId: string) => {
        router.patch(route('dashboard.transactions.mark-as-paid', { transaction: transactionId }), {}, {
            preserveScroll: true,
        });
    };

    return (
        <DashboardLayout title={`Bem-vindo, ${firstName}`} subtitle="👋">
            <div className="space-y-6">
                {/* Top Stats Row */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <BalanceCard
                        balance={dashboardData.total_balance}
                        accountNumber="Saldo Total"
                        percentageChange={dashboardData.balance_percentage_change}
                    />
                    <StatsCard
                        title="Gastos do Mês"
                        value={dashboardData.monthly_expenses}
                        percentageChange={dashboardData.expenses_percentage_change}
                        comparedTo="Comparado ao mês passado"
                        month={new Date().toLocaleDateString('pt-BR', { month: 'long', day: 'numeric' })}
                    />
                    <StatsCard
                        title="Receitas do Mês"
                        value={dashboardData.monthly_income}
                        percentageChange={dashboardData.income_percentage_change}
                        comparedTo="Comparado ao mês passado"
                        month={new Date().toLocaleDateString('pt-BR', { month: 'long', day: 'numeric' })}
                    />
                </div>

                {/* Main Content Grid */}
                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Left Column - 2 cols */}
                    <div className="space-y-6 lg:col-span-2">
                        {/* Accounts Ending This Month */}
                        <AccountsEnding accounts={dashboardData.accounts_ending_this_month} />

                        {/* Upcoming Transactions */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between">
                                    <span className="flex items-center gap-2">
                                        <Clock className="h-5 w-5 text-orange-600" />
                                        Próximas Contas
                                    </span>
                                    <Badge variant="secondary">{dashboardData.upcoming_transactions.length}</Badge>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {dashboardData.upcoming_transactions.length > 0 ? (
                                    <div className="space-y-3">
                                        {dashboardData.upcoming_transactions.map((transaction) => (
                                            <div
                                                key={transaction.id}
                                                className="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50"
                                            >
                                                <div className="flex items-center gap-3 flex-1 min-w-0">
                                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-950 shrink-0">
                                                        <Clock className="h-5 w-5 text-orange-600" />
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="font-medium truncate">{transaction.name}</p>
                                                        <p className="text-sm text-muted-foreground">
                                                            {transaction.category} • Vence em {transaction.due_date}
                                                            {transaction.installment_info && (
                                                                <span className="ml-2">({transaction.installment_info})</span>
                                                            )}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-3 shrink-0">
                                                    <span className="font-semibold text-red-600">
                                                        {formatCurrency(transaction.amount)}
                                                    </span>
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        onClick={() => handleMarkAsPaid(transaction.id)}
                                                    >
                                                        <CheckCircle2 className="mr-2 h-4 w-4" />
                                                        Pagar
                                                    </Button>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-12 text-center">
                                        <Clock className="h-12 w-12 text-muted-foreground/50 mb-3" />
                                        <p className="text-muted-foreground font-medium">Nenhuma conta próxima do vencimento</p>
                                        <p className="text-sm text-muted-foreground mt-1">
                                            Suas próximas contas aparecerão aqui
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Right Column - 1 col */}
                    <div className="space-y-6 lg:col-span-1">
                        {/* Notifications */}
                        {dashboardData.unread_notifications_count > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center justify-between">
                                        <span className="flex items-center gap-2">
                                            <Bell className="h-5 w-5" />
                                            Alertas
                                        </span>
                                        <Badge variant="destructive">{dashboardData.unread_notifications_count}</Badge>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {dashboardData.unread_notifications.map((notification) => (
                                            <div
                                                key={notification.id}
                                                className="rounded-lg border border-l-4 border-l-orange-500 p-3 transition-colors hover:bg-muted/50"
                                            >
                                                <div className="flex items-start gap-2">
                                                    <AlertCircle className="mt-0.5 h-4 w-4 text-orange-500" />
                                                    <div className="flex-1">
                                                        <p className="font-medium text-sm">{notification.title}</p>
                                                        <p className="text-xs text-muted-foreground mt-1">
                                                            {notification.message}
                                                        </p>
                                                        <p className="text-xs text-muted-foreground mt-1">
                                                            {notification.created_at}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                    <Button
                                        variant="link"
                                        className="mt-3 w-full"
                                        onClick={() => router.visit(route('dashboard.notifications.index'))}
                                    >
                                        Ver todas as notificações
                                    </Button>
                                </CardContent>
                            </Card>
                        )}

                        {/* Wallets Summary */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Minhas Carteiras</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {dashboardData.wallets_summary.length > 0 ? (
                                    <div className="space-y-4">
                                        {dashboardData.wallets_summary.map((wallet) => (
                                            <div key={wallet.id} className="space-y-2">
                                                <div className="flex items-center justify-between">
                                                    <div className="flex items-center gap-2">
                                                        <CreditCard className="h-4 w-4 text-muted-foreground" />
                                                        <span className="font-medium text-sm">{wallet.name}</span>
                                                    </div>
                                                    <span className="font-semibold text-sm">
                                                        {formatCurrency(wallet.balance)}
                                                    </span>
                                                </div>

                                                {/* Credit Card Progress Bar */}
                                                {wallet.type === 'card_credit' && wallet.card_limit && wallet.card_limit > 0 && (
                                                    <div className="space-y-1">
                                                        <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                                                            <div
                                                                className={`h-full transition-all ${
                                                                    (wallet.usage_percentage ?? 0) >= 80
                                                                        ? 'bg-red-500'
                                                                        : (wallet.usage_percentage ?? 0) >= 50
                                                                          ? 'bg-orange-500'
                                                                          : 'bg-green-500'
                                                                }`}
                                                                style={{ width: `${wallet.usage_percentage ?? 0}%` }}
                                                            />
                                                        </div>
                                                        <div className="flex items-center justify-between text-xs text-muted-foreground">
                                                            <span>
                                                                {formatCurrency(wallet.card_limit_used ?? 0)} de{' '}
                                                                {formatCurrency(wallet.card_limit)}
                                                            </span>
                                                            <span>{wallet.usage_percentage?.toFixed(1)}%</span>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-center text-sm text-muted-foreground">
                                        Nenhuma carteira cadastrada
                                    </p>
                                )}
                                <Button
                                    variant="outline"
                                    className="mt-4 w-full"
                                    onClick={() => router.visit(route('dashboard.wallets.index'))}
                                >
                                    Gerenciar Carteiras
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <CustomToast />
        </DashboardLayout>
    );
}
