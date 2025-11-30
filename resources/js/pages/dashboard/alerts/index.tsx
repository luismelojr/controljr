import CreateAlertDialog from '@/components/alerts/create-alert-dialog';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Head, router } from '@inertiajs/react';
import { BellIcon, CalendarIcon, CreditCardIcon, PlusIcon, ToggleLeftIcon, ToggleRightIcon, TrashIcon, WalletIcon } from 'lucide-react';
import { useState } from 'react';

interface Alert {
    uuid: string;
    type: string;
    type_label: string;
    trigger_value?: number;
    trigger_days?: number[];
    is_active: boolean;
    notification_channels: string[];
    alertable?: {
        name: string;
    };
}

interface AlertsProps {
    alerts: Alert[];
}

export default function AlertsIndex({ alerts = [] }: AlertsProps) {
    const [showCreateDialog, setShowCreateDialog] = useState(false);
    const [deleteDialog, setDeleteDialog] = useState<{ open: boolean; alertUuid: string | null }>({
        open: false,
        alertUuid: null,
    });

    const getAlertIcon = (type: string) => {
        switch (type) {
            case 'credit_card_usage':
                return <CreditCardIcon className="h-5 w-5" />;
            case 'bill_due_date':
                return <CalendarIcon className="h-5 w-5" />;
            case 'budget_exceeded':
                return <WalletIcon className="h-5 w-5" />;
            default:
                return <BellIcon className="h-5 w-5" />;
        }
    };

    const getAlertDescription = (alert: Alert) => {
        switch (alert.type) {
            case 'credit_card_usage':
                return `Avisar quando usar ${alert.trigger_value}% do limite`;
            case 'bill_due_date':
                return `Avisar ${alert.trigger_days?.join(', ')} dia(s) antes do vencimento`;
            case 'account_balance':
                return `Avisar quando saldo menor que R$ ${alert.trigger_value}`;
            case 'budget_exceeded':
                return `Avisar quando gastar ${alert.trigger_value}% do orçamento`;
            default:
                return 'Alerta personalizado';
        }
    };

    const handleToggleStatus = (uuid: string) => {
        router.patch(
            route('dashboard.alerts.toggle-status', { alert: uuid }),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const handleDelete = (uuid: string) => {
        setDeleteDialog({
            open: true,
            alertUuid: uuid,
        });
    };

    const confirmDelete = () => {
        if (deleteDialog.alertUuid) {
            router.delete(route('dashboard.alerts.destroy', { alert: deleteDialog.alertUuid }));
            setDeleteDialog({ open: false, alertUuid: null });
        }
    };

    return (
        <DashboardLayout title="Alertas">
            <Head title="Alertas" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Alertas</h1>
                        <p className="text-muted-foreground">Gerencie seus alertas e notificações automáticas</p>
                    </div>
                    <Button onClick={() => setShowCreateDialog(true)}>
                        <PlusIcon className="mr-2 h-4 w-4" />
                        Novo Alerta
                    </Button>
                </div>

                {/* Lista de Alertas */}
                {alerts.length === 0 ? (
                    <div className="rounded-lg border bg-card p-12 text-center">
                        <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                            <BellIcon className="h-10 w-10 text-muted-foreground" />
                        </div>
                        <h2 className="mt-4 text-xl font-semibold">Nenhum alerta cadastrado</h2>
                        <p className="mt-2 text-muted-foreground">Crie alertas para ser notificado sobre eventos importantes das suas finanças.</p>
                        <Button onClick={() => setShowCreateDialog(true)} className="mt-6">
                            <PlusIcon className="mr-2 h-4 w-4" />
                            Criar Primeiro Alerta
                        </Button>
                    </div>
                ) : (
                    <div className="grid gap-4">
                        {alerts.map((alert) => (
                            <Card key={alert.uuid}>
                                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <div className="flex items-center space-x-3">
                                        <div className={`${!alert.is_active ? 'opacity-50' : ''}`}>{getAlertIcon(alert.type)}</div>
                                        <div>
                                            <CardTitle className={`text-base ${!alert.is_active ? 'opacity-50' : ''}`}>
                                                {alert.type_label}
                                                {alert.alertable && ` - ${alert.alertable.name}`}
                                            </CardTitle>
                                            <p className={`text-sm text-muted-foreground ${!alert.is_active ? 'opacity-50' : ''}`}>
                                                {getAlertDescription(alert)}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Badge variant={alert.is_active ? 'default' : 'secondary'}>{alert.is_active ? 'Ativo' : 'Inativo'}</Badge>
                                    </div>
                                </CardHeader>
                                <CardContent className="flex items-center justify-between pt-2">
                                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                        <span>Notificações:</span>
                                        {alert.notification_channels.includes('mail') && (
                                            <Badge variant="outline" className="text-xs">
                                                Email
                                            </Badge>
                                        )}
                                        {alert.notification_channels.includes('database') && (
                                            <Badge variant="outline" className="text-xs">
                                                In-App
                                            </Badge>
                                        )}
                                    </div>
                                    <div className="flex gap-2">
                                        <Button variant="outline" size="sm" onClick={() => handleToggleStatus(alert.uuid)}>
                                            {alert.is_active ? (
                                                <>
                                                    <ToggleRightIcon className="mr-2 h-4 w-4" />
                                                    Desativar
                                                </>
                                            ) : (
                                                <>
                                                    <ToggleLeftIcon className="mr-2 h-4 w-4" />
                                                    Ativar
                                                </>
                                            )}
                                        </Button>
                                        <Button variant="destructive" size="sm" onClick={() => handleDelete(alert.uuid)}>
                                            <TrashIcon className="mr-2 h-4 w-4" />
                                            Excluir
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>

            <CreateAlertDialog open={showCreateDialog} onClose={() => setShowCreateDialog(false)} />

            <ConfirmDeleteDialog
                open={deleteDialog.open}
                title="Excluir Alerta"
                description="Tem certeza que deseja excluir este alerta? Esta ação não pode ser desfeita."
                onConfirm={confirmDelete}
                onOpenChange={(open) => setDeleteDialog({ open, alertUuid: open ? deleteDialog.alertUuid : null })}
            />
        </DashboardLayout>
    );
}
