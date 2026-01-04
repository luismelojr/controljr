import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Head, router } from '@inertiajs/react';
import { CreditCard, Calendar, TrendingUp, AlertCircle, CheckCircle } from 'lucide-react';
import { useState } from 'react';
import { format, parseISO } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { Subscription } from '@/types/subscription';

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
                        <p className="text-muted-foreground mt-2">Gerencie sua assinatura e histórico de pagamentos</p>
                    </div>

                    <Button onClick={handleChangePlan}>
                        <TrendingUp className="mr-2 h-4 w-4" />
                        Mudar de Plano
                    </Button>
                </div>

                {/* Current Subscription Card */}
                {currentSubscription ? (
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <div>
                                    <CardTitle className="flex items-center gap-2">
                                        <CreditCard className="h-5 w-5" />
                                        Plano Atual: {currentSubscription.plan.name}
                                    </CardTitle>
                                    <CardDescription className="mt-2">
                                        {currentSubscription.plan.description}
                                    </CardDescription>
                                </div>
                                <Badge variant={currentSubscription.status_color as any}>
                                    {currentSubscription.status_label}
                                </Badge>
                            </div>
                        </CardHeader>

                        <CardContent className="space-y-4">
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="flex items-center gap-3 rounded-lg border p-4">
                                    <Calendar className="h-8 w-8 text-primary" />
                                    <div>
                                        <p className="text-sm text-muted-foreground">Data de Início</p>
                                        <p className="font-semibold">
                                            {format(parseISO(currentSubscription.started_at), "dd 'de' MMMM 'de' yyyy", {
                                                locale: ptBR,
                                            })}
                                        </p>
                                    </div>
                                </div>

                                {currentSubscription.ends_at && (
                                    <div className="flex items-center gap-3 rounded-lg border p-4">
                                        <Calendar className="h-8 w-8 text-primary" />
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                {currentSubscription.on_grace_period ? 'Válido até' : 'Próxima Renovação'}
                                            </p>
                                            <p className="font-semibold">
                                                {format(parseISO(currentSubscription.ends_at), "dd 'de' MMMM 'de' yyyy", {
                                                    locale: ptBR,
                                                })}
                                            </p>
                                            {currentSubscription.days_remaining > 0 && (
                                                <p className="text-xs text-muted-foreground">
                                                    ({currentSubscription.days_remaining} dias restantes)
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Grace Period Warning */}
                            {currentSubscription.on_grace_period && (
                                <div className="flex items-start gap-3 rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-900 dark:bg-orange-950">
                                    <AlertCircle className="h-5 w-5 text-orange-600 dark:text-orange-400" />
                                    <div className="flex-1">
                                        <p className="font-semibold text-orange-900 dark:text-orange-100">
                                            Assinatura Cancelada
                                        </p>
                                        <p className="text-sm text-orange-800 dark:text-orange-200">
                                            Sua assinatura foi cancelada mas você ainda tem acesso até{' '}
                                            {format(parseISO(currentSubscription.ends_at!), "dd 'de' MMMM", {
                                                locale: ptBR,
                                            })}
                                            . Você pode retomar sua assinatura a qualquer momento.
                                        </p>
                                    </div>
                                </div>
                            )}

                            {/* Active Subscription Info */}
                            {currentSubscription.is_active && !currentSubscription.is_cancelled && (
                                <div className="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950">
                                    <CheckCircle className="h-5 w-5 text-green-600 dark:text-green-400" />
                                    <div className="flex-1">
                                        <p className="font-semibold text-green-900 dark:text-green-100">
                                            Assinatura Ativa
                                        </p>
                                        <p className="text-sm text-green-800 dark:text-green-200">
                                            Sua assinatura está ativa e você tem acesso a todos os recursos do plano{' '}
                                            {currentSubscription.plan.name}.
                                        </p>
                                    </div>
                                </div>
                            )}

                            {/* Action Buttons */}
                            <div className="flex gap-2">
                                {currentSubscription.can_resume && (
                                    <Button onClick={() => setShowResumeDialog(true)}>Retomar Assinatura</Button>
                                )}

                                {currentSubscription.can_cancel && (
                                    <Button variant="destructive" onClick={() => setShowCancelDialog(true)}>
                                        Cancelar Assinatura
                                    </Button>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <Card>
                        <CardHeader>
                            <CardTitle>Nenhuma Assinatura Ativa</CardTitle>
                            <CardDescription>Você está no plano gratuito</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="mb-4 text-muted-foreground">
                                Faça upgrade para um plano premium e tenha acesso a recursos avançados!
                            </p>
                            <Button onClick={handleChangePlan}>Ver Planos Disponíveis</Button>
                        </CardContent>
                    </Card>
                )}

                {/* Subscription History */}
                {subscriptionHistory.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Histórico de Assinaturas</CardTitle>
                            <CardDescription>Suas assinaturas anteriores</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {subscriptionHistory.map((subscription) => (
                                    <div
                                        key={subscription.uuid}
                                        className="flex items-center justify-between rounded-lg border p-4"
                                    >
                                        <div>
                                            <p className="font-semibold">{subscription.plan.name}</p>
                                            <p className="text-sm text-muted-foreground">
                                                {format(parseISO(subscription.started_at), "dd/MM/yyyy", {
                                                    locale: ptBR,
                                                })}{' '}
                                                -{' '}
                                                {subscription.ends_at
                                                    ? format(parseISO(subscription.ends_at), "dd/MM/yyyy", {
                                                          locale: ptBR,
                                                      })
                                                    : 'Atual'}
                                            </p>
                                        </div>
                                        <Badge variant={subscription.status_color as any}>
                                            {subscription.status_label}
                                        </Badge>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}
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
