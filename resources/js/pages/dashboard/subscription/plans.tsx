import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { SubscriptionPlan } from '@/types/subscription';
import { Head, router } from '@inertiajs/react';
import { Check } from 'lucide-react';

interface Props {
    plans: SubscriptionPlan[];
    currentPlan: string;
}

export default function SubscriptionPlans({ plans, currentPlan }: Props) {
    const handleSelectPlan = (planSlug: string) => {
        if (planSlug === currentPlan) {
            return;
        }

        const selectedPlan = plans.find((p) => p.slug === planSlug);
        const currentPlanObj = plans.find((p) => p.slug === currentPlan);

        // Se não encontrar os planos (erro defensivo), usa fallback para subscribe
        if (!selectedPlan || !currentPlanObj) {
            router.post(route('dashboard.subscription.subscribe', { planSlug }));
            return;
        }

        // Se o plano atual for gratuito, é sempre um 'subscribe' normal
        if (currentPlanObj.is_free) {
            router.post(route('dashboard.subscription.subscribe', { planSlug }));
            return;
        }

        // Se ambos são pagos, verifica se é upgrade ou downgrade
        if (selectedPlan.price_cents > currentPlanObj.price_cents) {
            // Upgrade (Prorated)
            router.post(route('dashboard.subscription.upgrade', { planSlug }));
        } else {
            // Downgrade (Agendado)
            router.post(route('dashboard.subscription.downgrade', { planSlug }));
        }
    };

    const getFeatureLabel = (key: string): string => {
        const labels: Record<string, string> = {
            categories: 'Categorias',
            wallets: 'Carteiras',
            budgets: 'Orçamentos',
            savings_goals: 'Metas de Economia',
            export_per_month: 'Exportações/mês',
            transactions_history_months: 'Histórico de Transações',
            tags: 'Tags Personalizadas',
            attachments: 'Anexos',
            custom_reports: 'Relatórios Customizados',
            ai_predictions: 'Previsões com IA',
            family_members: 'Membros da Família',
        };

        return labels[key] || key;
    };

    const getFeatureValue = (value: number | boolean): string => {
        if (typeof value === 'boolean') {
            return value ? 'Sim' : 'Não';
        }

        if (value === -1) {
            return 'Ilimitado';
        }

        if (value === 0) {
            return 'Não disponível';
        }

        return value.toString();
    };

    return (
        <DashboardLayout title="Planos de Assinatura">
            <Head title="Planos de Assinatura" />

            <div className="flex flex-col space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Escolha seu Plano</h1>
                        <p className="mt-2 text-muted-foreground">Escolha o plano ideal para suas necessidades financeiras</p>
                    </div>
                </div>

                <div className="grid gap-6 md:grid-cols-3">
                    {plans.map((plan) => {
                        const isCurrentPlan = plan.slug === currentPlan;

                        return (
                            <div
                                key={plan.uuid}
                                className={`relative flex flex-col rounded-lg border-2 p-6 shadow-sm transition-all hover:shadow-md ${
                                    isCurrentPlan ? 'border-primary bg-primary/5' : plan.is_premium ? 'border-primary/50' : 'border-border'
                                }`}
                            >
                                {plan.is_premium && (
                                    <Badge className="absolute -top-3 left-1/2 -translate-x-1/2" variant="default">
                                        Mais Popular
                                    </Badge>
                                )}

                                {isCurrentPlan && (
                                    <Badge className="absolute -top-3 right-4" variant="default">
                                        Plano Atual
                                    </Badge>
                                )}

                                <div className="flex-1">
                                    <h3 className="text-2xl font-bold">{plan.name}</h3>
                                    <p className="mt-2 text-sm text-muted-foreground">{plan.description}</p>

                                    <div className="mt-4 flex items-baseline gap-1">
                                        <span className="text-4xl font-bold">{plan.price_formatted}</span>
                                        {!plan.is_free && <span className="text-muted-foreground">/mês</span>}
                                    </div>

                                    <ul className="mt-6 space-y-3">
                                        {Object.entries(plan.features).map(([key, value]) => {
                                            const displayValue = getFeatureValue(value);
                                            const isAvailable = value !== 0 && value !== false;

                                            return (
                                                <li key={key} className={`flex items-start gap-2 ${!isAvailable ? 'text-muted-foreground' : ''}`}>
                                                    <Check
                                                        className={`h-5 w-5 flex-shrink-0 ${isAvailable ? 'text-primary' : 'text-muted-foreground'}`}
                                                    />
                                                    <span className="text-sm">
                                                        <strong>{displayValue}</strong> {getFeatureLabel(key)}
                                                    </span>
                                                </li>
                                            );
                                        })}
                                    </ul>
                                </div>

                                <Button
                                    className="mt-6 w-full"
                                    variant={isCurrentPlan ? 'outline' : plan.is_premium ? 'default' : 'secondary'}
                                    disabled={isCurrentPlan}
                                    onClick={() => handleSelectPlan(plan.slug)}
                                >
                                    {isCurrentPlan ? 'Plano Atual' : plan.is_free ? 'Escolher Gratuito' : 'Assinar Agora'}
                                </Button>
                            </div>
                        );
                    })}
                </div>

                <div className="rounded-lg border bg-muted/50 p-4">
                    <p className="text-sm text-muted-foreground">
                        <strong>Nota:</strong> Você pode cancelar sua assinatura a qualquer momento. Mudanças de plano entram em vigor imediatamente.
                    </p>
                </div>
            </div>
        </DashboardLayout>
    );
}
