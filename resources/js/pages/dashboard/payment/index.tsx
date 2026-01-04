import { router } from '@inertiajs/react';
import { CreditCard, QrCode, Receipt, Eye, X } from 'lucide-react';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { PaymentIndexPageProps } from '@/types/payment';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';

export default function PaymentIndex({ payments }: PaymentIndexPageProps) {
    const statusColors = {
        pending: 'bg-yellow-500',
        confirmed: 'bg-green-500',
        received: 'bg-green-600',
        overdue: 'bg-red-500',
        refunded: 'bg-purple-500',
        cancelled: 'bg-gray-500',
    };

    const statusLabels = {
        pending: 'Pendente',
        confirmed: 'Confirmado',
        received: 'Recebido',
        overdue: 'Vencido',
        refunded: 'Reembolsado',
        cancelled: 'Cancelado',
    };

    const paymentMethodIcons = {
        pix: QrCode,
        boleto: Receipt,
        credit_card: CreditCard,
    };

    const paymentMethodLabels = {
        pix: 'PIX',
        boleto: 'Boleto',
        credit_card: 'Cartão',
    };

    const handleCancelPayment = (paymentUuid: string) => {
        if (confirm('Deseja realmente cancelar este pagamento?')) {
            router.delete(`/dashboard/payment/${paymentUuid}/cancel`);
        }
    };

    return (
        <DashboardLayout title={'Meus Pagamentos - Asaas'}>
            <div className="container mx-auto py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold">Histórico de Pagamentos</h1>
                    <p className="text-muted-foreground mt-2">
                        Visualize todos os seus pagamentos
                    </p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Pagamentos</CardTitle>
                        <CardDescription>
                            Total de {payments.total} pagamento(s)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {payments.data.length === 0 ? (
                            <div className="text-center py-12">
                                <CreditCard className="h-12 w-12 mx-auto mb-4 text-muted-foreground" />
                                <p className="text-muted-foreground">
                                    Nenhum pagamento encontrado
                                </p>
                            </div>
                        ) : (
                            <div className="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Data</TableHead>
                                            <TableHead>Plano</TableHead>
                                            <TableHead>Método</TableHead>
                                            <TableHead>Valor</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead className="text-right">Ações</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {payments.data.map((payment) => {
                                            const Icon =
                                                paymentMethodIcons[
                                                    payment.payment_method
                                                ];
                                            return (
                                                <TableRow key={payment.uuid}>
                                                    <TableCell>
                                                        {format(
                                                            new Date(payment.created_at),
                                                            'dd/MM/yyyy',
                                                            { locale: ptBR }
                                                        )}
                                                    </TableCell>
                                                    <TableCell>
                                                        {payment.subscription
                                                            ? payment.subscription.plan
                                                                  .name
                                                            : '-'}
                                                    </TableCell>
                                                    <TableCell>
                                                        <div className="flex items-center gap-2">
                                                            <Icon className="h-4 w-4" />
                                                            <span>
                                                                {
                                                                    paymentMethodLabels[
                                                                        payment
                                                                            .payment_method
                                                                    ]
                                                                }
                                                            </span>
                                                        </div>
                                                    </TableCell>
                                                    <TableCell className="font-medium">
                                                        {payment.amount_formatted}
                                                    </TableCell>
                                                    <TableCell>
                                                        <Badge
                                                            className={
                                                                statusColors[payment.status]
                                                            }
                                                        >
                                                            {statusLabels[payment.status]}
                                                        </Badge>
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        <div className="flex justify-end gap-2">
                                                            {payment.status === 'pending' && (
                                                                <>
                                                                    <Button
                                                                        variant="outline"
                                                                        size="sm"
                                                                        onClick={() =>
                                                                            router.visit(
                                                                                `/dashboard/payment/${payment.uuid}`
                                                                            )
                                                                        }
                                                                    >
                                                                        <Eye className="h-4 w-4 mr-1" />
                                                                        Ver
                                                                    </Button>
                                                                    <Button
                                                                        variant="outline"
                                                                        size="sm"
                                                                        onClick={() =>
                                                                            handleCancelPayment(
                                                                                payment.uuid
                                                                            )
                                                                        }
                                                                    >
                                                                        <X className="h-4 w-4 mr-1" />
                                                                        Cancelar
                                                                    </Button>
                                                                </>
                                                            )}
                                                            {(payment.status === 'confirmed' ||
                                                                payment.status ===
                                                                    'received') && (
                                                                <Button
                                                                    variant="outline"
                                                                    size="sm"
                                                                    onClick={() =>
                                                                        router.visit(
                                                                            `/dashboard/payment/${payment.uuid}/success`
                                                                        )
                                                                    }
                                                                >
                                                                    <Eye className="h-4 w-4 mr-1" />
                                                                    Ver Detalhes
                                                                </Button>
                                                            )}
                                                        </div>
                                                    </TableCell>
                                                </TableRow>
                                            );
                                        })}
                                    </TableBody>
                                </Table>
                            </div>
                        )}

                        {/* Pagination */}
                        {payments.last_page > 1 && (
                            <div className="flex justify-center gap-2 mt-6">
                                <Button
                                    variant="outline"
                                    disabled={payments.current_page === 1}
                                    onClick={() =>
                                        router.visit(
                                            `/dashboard/payment?page=${payments.current_page - 1}`
                                        )
                                    }
                                >
                                    Anterior
                                </Button>
                                <span className="flex items-center px-4">
                                    Página {payments.current_page} de {payments.last_page}
                                </span>
                                <Button
                                    variant="outline"
                                    disabled={
                                        payments.current_page === payments.last_page
                                    }
                                    onClick={() =>
                                        router.visit(
                                            `/dashboard/payment?page=${payments.current_page + 1}`
                                        )
                                    }
                                >
                                    Próxima
                                </Button>
                            </div>
                        )}
                    </CardContent>
                </Card>

                <div className="mt-6">
                    <Button
                        variant="outline"
                        onClick={() => router.visit('/dashboard/subscription')}
                    >
                        Voltar para Assinatura
                    </Button>
                </div>
            </div>
        </DashboardLayout>
    );
}
