import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Subscription } from '@/types/subscription';
import { Head, router } from '@inertiajs/react';
import { format, parseISO } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { AlertCircle, Calendar, CreditCard, TrendingUp } from 'lucide-react';
import { useState } from 'react';

interface Props {
    currentSubscription: Subscription | null;
    subscriptionHistory: Subscription[];
}

export default function SubscriptionIndex({ currentSubscription, subscriptionHistory }: Props) {
    const [showCancelDialog, setShowCancelDialog] = useState(false);
    const [showResumeDialog, setShowResumeDialog] = useState(false);

    const handleCancel = () => {
        router.delete(route('dashboard.subscription.cancel'), {
            onSuccess: () => setShowCancelDialog(false),
        });
    };

    const handleResume = () => {
        router.post(route('dashboard.subscription.resume'), undefined, {
            onSuccess: () => setShowResumeDialog(false),
        });
    };

    const handleChangePlan = () => {
        router.get(route('dashboard.subscription.plans'));
    };

    return (
        <DashboardLayout title="Minha Assinatura">
            <Head title="Minha Assinatura" />

            <div className="flex flex-col space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Minha Assinatura</h1>
                        <p className="mt-2 text-muted-foreground">Gerencie sua assinatura e histórico de pagamentos</p>
                    </div>

                    <Button onClick={handleChangePlan}>
                        <TrendingUp className="mr-2 h-4 w-4" />
                        Mudar de Plano
                    </Button>
                </div>

                {/* Current Subscription Card */}
                {currentSubscription ? (
                    <Card className="border-primary/20 shadow-sm">
                        <CardHeader className="pb-3">
                            <div className="flex items-center justify-between">
                                <div>
                                    <CardTitle className="flex items-center gap-2 text-xl">
                                        <CreditCard className="h-5 w-5 text-primary" />
                                        {currentSubscription.plan.name}
                                    </CardTitle>
                                    <CardDescription className="mt-1">{currentSubscription.plan.description}</CardDescription>
                                </div>
                                <div className="flex flex-col items-end gap-2">
                                    <Badge variant={currentSubscription.status_color as any} className="px-3 py-1 text-sm">
                                        {currentSubscription.status_label}
                                    </Badge>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent className="space-y-6">
                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div className="flex items-center gap-4 rounded-lg border bg-card p-4 shadow-sm">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                        <Calendar className="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium text-muted-foreground">Início</p>
                                        <p className="font-semibold">
                                            {format(parseISO(currentSubscription.started_at), "dd 'de' MMM, yyyy", {
                                                locale: ptBR,
                                            })}
                                        </p>
                                    </div>
                                </div>

                                {currentSubscription.ends_at && (
                                    <div className="flex items-center gap-4 rounded-lg border bg-card p-4 shadow-sm">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                            <Calendar className="h-5 w-5 text-primary" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-medium text-muted-foreground">
                                                {currentSubscription.on_grace_period ? 'Válido até' : 'Renovação'}
                                            </p>
                                            <p className="font-semibold">
                                                {format(parseISO(currentSubscription.ends_at), "dd 'de' MMM, yyyy", {
                                                    locale: ptBR,
                                                })}
                                            </p>
                                            {currentSubscription.days_remaining > 0 && (
                                                <p className="mt-0.5 text-xs text-muted-foreground">
                                                    (Faltam {currentSubscription.days_remaining} dias)
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                )}

                                <div className="flex items-center gap-4 rounded-lg border bg-card p-4 shadow-sm">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                                        <TrendingUp className="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium text-muted-foreground">Valor</p>
                                        <p className="font-semibold">
                                            {currentSubscription.plan.price_formatted}
                                            <span className="text-sm font-normal text-muted-foreground">/mês</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Status Messages */}
                            {currentSubscription.on_grace_period && (
                                <div className="flex items-start gap-3 rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-900 dark:bg-orange-950">
                                    <AlertCircle className="h-5 w-5 text-orange-600 dark:text-orange-400" />
                                    <div className="flex-1">
                                        <p className="font-semibold text-orange-900 dark:text-orange-100">Assinatura Cancelada</p>
                                        <p className="text-sm text-orange-800 dark:text-orange-200">
                                            Você tem acesso até {format(parseISO(currentSubscription.ends_at!), "dd 'de' MMM", { locale: ptBR })}.
                                            Retome sua assinatura para não perder acesso.
                                        </p>
                                    </div>
                                </div>
                            )}

                            <div className="flex flex-wrap gap-3 pt-2">
                                {currentSubscription.can_resume && (
                                    <Button onClick={() => setShowResumeDialog(true)} className="flex-1 md:flex-none">
                                        Retomar Assinatura
                                    </Button>
                                )}

                                {currentSubscription.can_cancel && (
                                    <Button
                                        variant="outline"
                                        className="flex-1 border-destructive/50 text-destructive hover:bg-destructive/10 md:flex-none"
                                        onClick={() => setShowCancelDialog(true)}
                                    >
                                        Cancelar Assinatura
                                    </Button>
                                )}

                                <Button variant="secondary" onClick={handleChangePlan} className="flex-1 md:flex-none">
                                    Mudar de Plano
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <Card className="border-dashed bg-muted/30">
                        <CardHeader className="text-center">
                            <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                                <TrendingUp className="h-6 w-6 text-primary" />
                            </div>
                            <CardTitle>Nenhuma Assinatura Ativa</CardTitle>
                            <CardDescription>Você está usando o plano gratuito com recursos limitados</CardDescription>
                        </CardHeader>
                        <CardContent className="flex justify-center pb-8">
                            <Button size="lg" onClick={handleChangePlan} className="px-8">
                                Ver Planos Premium
                            </Button>
                        </CardContent>
                    </Card>
                )}

                <div className="grid gap-6 md:grid-cols-2">
                    {/* Actions Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">Pagamentos</CardTitle>
                            <CardDescription>Gerencie suas cobranças</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button variant="outline" className="w-full justify-start" onClick={() => router.get(route('dashboard.payment.index'))}>
                                <CreditCard className="mr-2 h-4 w-4" />
                                Ver Todos os Pagamentos
                            </Button>
                        </CardContent>
                    </Card>

                    {/* History Summary */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">Histórico Recente</CardTitle>
                            <CardDescription>Últimas alterações de assinatura</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {subscriptionHistory.length > 0 ? (
                                <div className="space-y-4">
                                    {subscriptionHistory.slice(0, 3).map((subscription) => (
                                        <div key={subscription.uuid} className="flex items-center justify-between text-sm">
                                            <div>
                                                <p className="font-medium">{subscription.plan.name}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    {format(parseISO(subscription.started_at), 'dd/MM/yyyy', { locale: ptBR })}
                                                </p>
                                            </div>
                                            <Badge variant="outline" className="text-xs">
                                                {subscription.status_label}
                                            </Badge>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground">Nenhum histórico disponível.</p>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Cancel Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={showCancelDialog}
                onOpenChange={setShowCancelDialog}
                onConfirm={handleCancel}
                title="Cancelar Assinatura?"
                description="Você ainda terá acesso aos recursos premium até o final do período atual. Você pode retomar a qualquer momento."
                confirmText="Sim, Cancelar"
                cancelText="Não, Manter Assinatura"
            />

            {/* Resume Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={showResumeDialog}
                onOpenChange={setShowResumeDialog}
                onConfirm={handleResume}
                title="Retomar Assinatura?"
                description="Sua assinatura será reativada imediatamente e você continuará tendo acesso a todos os recursos premium."
                confirmText="Sim, Retomar"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
