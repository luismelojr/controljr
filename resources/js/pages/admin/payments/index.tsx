import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head } from '@inertiajs/react';

interface Payment {
    uuid: string;
    user_name: string;
    amount_formatted: string;
    status: string;
    billing_type: string;
    date: string;
}

interface Props {
    payments: {
        data: Payment[];
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}

const statusMap: Record<string, { label: string; variant: 'default' | 'success' | 'destructive' | 'warning' | 'secondary' }> = {
    PENDING: { label: 'Pendente', variant: 'warning' },
    CONFIRMED: { label: 'Confirmado', variant: 'success' },
    RECEIVED: { label: 'Recebido', variant: 'success' },
    OVERDUE: { label: 'Atrasado', variant: 'destructive' },
    REFUNDED: { label: 'Reembolsado', variant: 'secondary' },
    CANCELLED: { label: 'Cancelado', variant: 'destructive' },
};

export default function AdminPayments({ payments }: Props) {
    return (
        <DashboardLayout title="Pagamentos" subtitle="Histórico de Pagamentos">
            <Head title="Histórico de Pagamentos" />

            <div className="flex h-full flex-1 flex-col gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Histórico de Transações</CardTitle>
                        <CardDescription>Registro de todas as cobranças realizadas.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Data</TableHead>
                                    <TableHead>Usuário</TableHead>
                                    <TableHead>Valor</TableHead>
                                    <TableHead>Método</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {payments.data.map((payment) => (
                                    <TableRow key={payment.uuid}>
                                        <TableCell>{payment.date}</TableCell>
                                        <TableCell className="font-medium">{payment.user_name}</TableCell>
                                        <TableCell>{payment.amount_formatted}</TableCell>
                                        <TableCell>{payment.billing_type}</TableCell>
                                        <TableCell>
                                            <Badge variant={(statusMap[payment.status]?.variant as any) || 'outline'}>
                                                {statusMap[payment.status]?.label || payment.status}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>

                        {/* Pagination Controls */}
                        <div className="mt-4 flex items-center justify-end gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => (window.location.href = payments.prev_page_url || '#')}
                                disabled={!payments.prev_page_url}
                            >
                                Anterior
                            </Button>
                            <span className="text-sm text-muted-foreground">
                                Página {payments.current_page} de {payments.last_page}
                            </span>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => (window.location.href = payments.next_page_url || '#')}
                                disabled={!payments.next_page_url}
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
