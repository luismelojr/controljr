import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head } from '@inertiajs/react';
import { CreditCard, Download, FileText, QrCode, Receipt } from 'lucide-react';

interface Payment {
    uuid: string;
    user_name: string;
    amount_formatted: string;
    status: string;
    billing_type: 'pix' | 'boleto' | 'credit_card';
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

const methodLabels = {
    pix: 'PIX',
    boleto: 'Boleto Bancário',
    credit_card: 'Cartão de Crédito',
};

const paymentMethodIcons = {
    pix: QrCode,
    boleto: Receipt,
    credit_card: CreditCard,
};

export default function AdminPayments({ payments }: Props) {
    return (
        <DashboardLayout title="Pagamentos" subtitle="Histórico de Pagamentos">
            <Head title="Histórico de Pagamentos" />

            <div className="flex h-full flex-1 flex-col gap-4">
                <div className="flex items-center justify-end">
                    <a href={route('admin.export.payments')} target="_blank" rel="noreferrer">
                        <Button variant="outline" size="sm" className="gap-2">
                            <Download className="h-4 w-4" />
                            Exportar CSV
                        </Button>
                    </a>
                </div>

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
                                    <TableHead className="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {payments.data.map((payment) => {
                                    const Icon = paymentMethodIcons[payment.billing_type];
                                    return (
                                        <TableRow key={payment.uuid}>
                                            <TableCell>{payment.date}</TableCell>
                                            <TableCell className="font-medium">{payment.user_name}</TableCell>
                                            <TableCell>{payment.amount_formatted}</TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <Icon className={'size-4'} />
                                                    <span>{methodLabels[payment.billing_type]}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={(statusMap[payment.status]?.variant as any) || 'outline'}>
                                                    {statusMap[payment.status]?.label || payment.status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <a href={route('payments.invoice', { payment: payment.uuid })} target="_blank" rel="noreferrer">
                                                    <Button variant="ghost" size="icon" title="Baixar Fatura">
                                                        <FileText className="h-4 w-4" />
                                                    </Button>
                                                </a>
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
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
