import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head } from '@inertiajs/react';
import { format, parseISO } from 'date-fns';
import { ptBR } from 'date-fns/locale';

interface Subscription {
    id: number;
    user_name: string;
    user_email: string;
    plan_name: string;
    status: string;
    status_label: string;
    status_color: string;
    started_at: string;
    ends_at: string | null;
}

interface Props {
    subscriptions: {
        data: Subscription[];
        links: any[]; // Simple pagination links for now, or use full Paginator type
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}

export default function AdminSubscriptions({ subscriptions }: Props) {
    return (
        <DashboardLayout title="Assinaturas" subtitle="Gerenciar Assinaturas">
            <Head title="Gerenciar Assinaturas" />

            <div className="flex h-full flex-1 flex-col gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Todas as Assinaturas</CardTitle>
                        <CardDescription>Listagem completa de assinaturas do sistema.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Usuário</TableHead>
                                    <TableHead>Plano</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Início</TableHead>
                                    <TableHead>Fim/Renovação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {subscriptions.data.map((sub) => (
                                    <TableRow key={sub.id}>
                                        <TableCell>
                                            <div className="flex flex-col">
                                                <span className="font-medium">{sub.user_name}</span>
                                                <span className="text-xs text-muted-foreground">{sub.user_email}</span>
                                            </div>
                                        </TableCell>
                                        <TableCell>{sub.plan_name}</TableCell>
                                        <TableCell>
                                            <Badge variant={sub.status_color as any}>{sub.status_label}</Badge>
                                        </TableCell>
                                        <TableCell>{format(parseISO(sub.started_at), 'dd/MM/yyyy', { locale: ptBR })}</TableCell>
                                        <TableCell>{sub.ends_at ? format(parseISO(sub.ends_at), 'dd/MM/yyyy', { locale: ptBR }) : '-'}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>

                        {/* Simple Pagination Controls */}
                        <div className="mt-4 flex items-center justify-end gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => (window.location.href = subscriptions.prev_page_url || '#')}
                                disabled={!subscriptions.prev_page_url}
                            >
                                Anterior
                            </Button>
                            <span className="text-sm text-muted-foreground">
                                Página {subscriptions.current_page} de {subscriptions.last_page}
                            </span>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => (window.location.href = subscriptions.next_page_url || '#')}
                                disabled={!subscriptions.next_page_url}
                            >
                                Próxima
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </DashboardLayout>
    );
}
