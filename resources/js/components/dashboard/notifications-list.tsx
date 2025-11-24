import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertCircle, Bell } from 'lucide-react';
import { router } from '@inertiajs/react';

interface Notification {
    id: string;
    title: string;
    message: string;
    type: string;
    created_at: string;
}

interface NotificationsListProps {
    notifications: Notification[];
    unreadCount: number;
}

export function NotificationsList({ notifications, unreadCount }: NotificationsListProps) {
    if (unreadCount === 0) return null;

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center justify-between">
                    <span className="flex items-center gap-2">
                        <Bell className="h-5 w-5" />
                        Alertas
                    </span>
                    <Badge variant="destructive">{unreadCount}</Badge>
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div className="space-y-3">
                    {notifications.map((notification) => (
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
    );
}
