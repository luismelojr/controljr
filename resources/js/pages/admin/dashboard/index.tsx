import { AdminRecentPayments } from '@/components/admin/admin-recent-payments';
import { AdminRevenueChart } from '@/components/admin/admin-revenue-chart';
import { AdminStatsCard } from '@/components/admin/admin-stats-card';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Head } from '@inertiajs/react';
import { CreditCard, DollarSign, Users } from 'lucide-react';

interface Props {
    metrics: {
        total_users: number;
        active_subscriptions: number;
        mrr: number;
    };
    recent_payments: {
        uuid: string;
        user_name: string;
        amount_formatted: string;
        status: string;
        date: string;
    }[];
    revenue_chart: {
        date: string;
        value: number;
    }[];
}

export default function AdminDashboard({ metrics, recent_payments, revenue_chart }: Props) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    return (
        <DashboardLayout title="Admin Dashboard" subtitle="Visão geral do sistema">
            <Head title="Admin Dashboard" />

            <div className="flex h-full flex-1 flex-col gap-8">
                {/* Metrics Row */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <AdminStatsCard
                        title="Total de Usuários"
                        value={metrics.total_users}
                        icon={Users}
                        description="Usuários cadastrados na plataforma"
                    />
                    <AdminStatsCard
                        title="Assinaturas Ativas"
                        value={metrics.active_subscriptions}
                        icon={CreditCard}
                        description="Assinantes com plano ativo"
                    />
                    <AdminStatsCard
                        title="MRR (Receita Recorrente)"
                        value={formatCurrency(metrics.mrr)}
                        icon={DollarSign}
                        description="Estimativa mensal"
                    />
                </div>

                {/* Main Content Area */}
                <div className="grid gap-4 md:grid-cols-1 lg:grid-cols-7">
                    <AdminRevenueChart data={revenue_chart} className="col-span-4" />
                    <AdminRecentPayments payments={recent_payments} className="col-span-4 lg:col-span-3" />
                </div>
            </div>
        </DashboardLayout>
    );
}
