import { AccountsEnding } from '@/components/dashboard/accounts-ending';
import { BalanceCard } from '@/components/dashboard/balance-card';
import { GoalsWidget } from '@/components/dashboard/goals-widget';
import { NotificationsList } from '@/components/dashboard/notifications-list';
import { StatsCard } from '@/components/dashboard/stats-card';
import { UpcomingTransactionsList } from '@/components/dashboard/upcoming-transactions-list';
import { WalletsSummaryList } from '@/components/dashboard/wallets-summary-list';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import CustomToast from '@/components/ui/custom-toast';
import { formatCurrency } from '@/lib/format';
import { PageProps } from '@/types';
import { router, usePage } from '@inertiajs/react';

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
        installment_amount: number;
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
    active_goals: SavingsGoal[];
}

export default function Home() {
    const { auth, ...dashboardData } = usePage<PageProps & DashboardData>().props;

    const firstName = auth.user?.name ? auth.user.name.split(' ')[0] : 'User';

    const handleMarkAsPaid = (transactionId: string) => {
        router.patch(
            route('dashboard.transactions.mark-as-paid', { transaction: transactionId }),
            {},
            {
                preserveScroll: true,
            },
        );
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
                        <UpcomingTransactionsList
                            transactions={dashboardData.upcoming_transactions}
                            onMarkAsPaid={handleMarkAsPaid}
                            formatCurrency={formatCurrency}
                        />
                    </div>

                    {/* Right Column - 1 col */}
                    <div className="space-y-6 lg:col-span-1">
                        {/* Notifications */}
                        <NotificationsList
                            notifications={dashboardData.unread_notifications}
                            unreadCount={dashboardData.unread_notifications_count}
                        />

                        {/* Wallets Summary */}
                        <WalletsSummaryList wallets={dashboardData.wallets_summary} formatCurrency={formatCurrency} />

                        {/* active_goals comes from dashboardData now */}
                        <GoalsWidget goals={dashboardData.active_goals} />
                    </div>
                </div>
            </div>

            <CustomToast />
        </DashboardLayout>
    );
}
