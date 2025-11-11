import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Head, router } from '@inertiajs/react';
import { BellIcon, CheckIcon } from 'lucide-react';

interface Notification {
    uuid: string;
    title: string;
    message: string;
    type: 'info' | 'warning' | 'danger' | 'success';
    type_label: string;
    is_read: boolean;
    created_at: string;
    created_at_human: string;
    data?: Record<string, any>;
}

interface NotificationsProps {
    notifications: {
        data: Notification[];
        links: any[];
        meta: any;
    };
}

export default function NotificationsIndex({ notifications }: NotificationsProps) {
    const getVariantByType = (type: string) => {
        switch (type) {
            case 'danger':
                return 'destructive';
            case 'warning':
                return 'default';
            case 'success':
                return 'default';
            case 'info':
            default:
                return 'secondary';
        }
    };

    const markAsRead = (uuid: string) => {
        router.post(route('dashboard.notifications.read', { notification: uuid }), {}, {
            preserveScroll: true,
        });
    };

    const markAllAsRead = () => {
        router.post(route('dashboard.notifications.read-all'), {}, {
            preserveScroll: true,
        });
    };

    const unreadCount = notifications.data.filter((n) => !n.is_read).length;

    return (
        <DashboardLayout title="Notificações">
            <Head title="Notificações" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Notificações</h1>
                        <p className="text-muted-foreground">
                            Acompanhe seus alertas e notificações
                        </p>
                    </div>
                    {unreadCount > 0 && (
                        <Button onClick={markAllAsRead} variant="outline">
                            <CheckIcon className="mr-2 h-4 w-4" />
                            Marcar Todas como Lidas
                        </Button>
                    )}
                </div>

                {/* Lista de Notificações */}
                {notifications.data.length === 0 ? (
                    <div className="rounded-lg border bg-card p-12 text-center">
                        <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                            <BellIcon className="h-10 w-10 text-muted-foreground" />
                        </div>
                        <h2 className="mt-4 text-xl font-semibold">Nenhuma notificação</h2>
                        <p className="mt-2 text-muted-foreground">
                            Você não tem notificações no momento.
                        </p>
                    </div>
                ) : (
                    <div className="space-y-3">
                        {notifications.data.map((notification) => (
                            <Card
                                key={notification.uuid}
                                className={`cursor-pointer transition-colors ${
                                    !notification.is_read ? 'bg-accent/50 hover:bg-accent/70' : 'hover:bg-accent/30'
                                }`}
                                onClick={() => !notification.is_read && markAsRead(notification.uuid)}
                            >
                                <CardContent className="p-4">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <div className="flex items-center gap-2 mb-1">
                                                <h3 className="font-semibold">{notification.title}</h3>
                                                <Badge variant={getVariantByType(notification.type)}>
                                                    {notification.type_label}
                                                </Badge>
                                            </div>
                                            <p className="text-sm text-muted-foreground">
                                                {notification.message}
                                            </p>
                                            <p className="text-xs text-muted-foreground mt-2">
                                                {notification.created_at_human}
                                            </p>

                                            {/* Dados extras se disponíveis */}
                                            {notification.data && Object.keys(notification.data).length > 0 && (
                                                <div className="mt-3 rounded-md bg-muted/50 p-3 text-xs">
                                                    {notification.data.usage_percent && (
                                                        <p>
                                                            <strong>Uso:</strong> {notification.data.usage_percent}%
                                                        </p>
                                                    )}
                                                    {notification.data.days_before && (
                                                        <p>
                                                            <strong>Dias restantes:</strong> {notification.data.days_before}
                                                        </p>
                                                    )}
                                                    {notification.data.amount && (
                                                        <p>
                                                            <strong>Valor:</strong> R$ {notification.data.amount}
                                                        </p>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                        {!notification.is_read && (
                                            <div
                                                className="ml-4 h-2 w-2 rounded-full bg-blue-500"
                                                title="Não lida"
                                            />
                                        )}
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}

                {/* Paginação (se houver) */}
                {notifications.links && notifications.links.length > 3 && (
                    <div className="flex justify-center gap-2">
                        {notifications.links.map((link: any, index: number) => (
                            <Button
                                key={index}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url)}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
