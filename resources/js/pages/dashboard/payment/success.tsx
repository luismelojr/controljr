import { router } from '@inertiajs/react';
import { CheckCircle } from 'lucide-react';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { PaymentPageProps } from '@/types/payment';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';

export default function PaymentSuccess({ payment }: PaymentPageProps) {
    return (
        <DashboardLayout>
            <div className="container max-w-2xl mx-auto py-8">
                <div className="text-center mb-8">
                    <div className="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
                        <CheckCircle className="h-12 w-12 text-green-600" />
                    </div>
                    <h1 className="text-3xl font-bold mb-2">Pagamento Confirmado!</h1>
                    <p className="text-muted-foreground">
                        Sua assinatura foi ativada com sucesso
                    </p>
                </div>

                <Card className="mb-6">
                    <CardHeader>
                        <CardTitle>Detalhes do Pagamento</CardTitle>
                        <CardDescription>
                            Plano {payment.subscription?.plan.name}
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-muted-foreground">Valor Pago</p>
                                <p className="text-xl font-bold">
                                    {payment.amount_formatted}
                                </p>
                            </div>
                            {payment.confirmed_at && (
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Data do Pagamento
                                    </p>
                                    <p className="text-xl font-bold">
                                        {format(
                                            new Date(payment.confirmed_at),
                                            "dd/MM/yyyy 'às' HH:mm",
                                            { locale: ptBR }
                                        )}
                                    </p>
                                </div>
                            )}
                        </div>

                        <div className="border-t pt-4">
                            <div className="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p className="text-muted-foreground">Método de Pagamento</p>
                                    <p className="font-medium">
                                        {payment.payment_method === 'pix'
                                            ? 'PIX'
                                            : payment.payment_method === 'boleto'
                                              ? 'Boleto Bancário'
                                              : 'Cartão de Crédito'}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-muted-foreground">Status</p>
                                    <p className="font-medium text-green-600">
                                        {payment.status === 'confirmed'
                                            ? 'Confirmado'
                                            : 'Recebido'}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {payment.invoice_url && (
                            <div className="border-t pt-4">
                                <Button
                                    variant="outline"
                                    className="w-full"
                                    onClick={() => window.open(payment.invoice_url!, '_blank')}
                                >
                                    Ver Comprovante
                                </Button>
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card className="mb-6 border-green-200 bg-green-50">
                    <CardContent className="pt-6">
                        <h3 className="font-semibold mb-2">Próximos passos</h3>
                        <ul className="space-y-2 text-sm text-muted-foreground">
                            <li className="flex items-start">
                                <span className="mr-2">•</span>
                                <span>
                                    Você agora tem acesso a todos os recursos do plano{' '}
                                    {payment.subscription?.plan.name}
                                </span>
                            </li>
                            <li className="flex items-start">
                                <span className="mr-2">•</span>
                                <span>
                                    Um email de confirmação foi enviado para o seu endereço
                                </span>
                            </li>
                            <li className="flex items-start">
                                <span className="mr-2">•</span>
                                <span>
                                    Você pode gerenciar sua assinatura a qualquer momento
                                </span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <div className="flex gap-4">
                    <Button
                        variant="outline"
                        onClick={() => router.visit('/dashboard')}
                        className="flex-1"
                    >
                        Ir para o Dashboard
                    </Button>
                    <Button
                        onClick={() => router.visit('/dashboard/subscription')}
                        className="flex-1"
                    >
                        Ver Assinatura
                    </Button>
                </div>
            </div>
        </DashboardLayout>
    );
}
