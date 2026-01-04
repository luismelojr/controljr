import { router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { CheckCircle, Clock, Copy, CreditCard, QrCode, Receipt, X } from 'lucide-react';
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
import type { PaymentPageProps } from '@/types/payment';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';

export default function ShowPayment({ payment }: PaymentPageProps) {
    const [copied, setCopied] = useState(false);
    const [isChecking, setIsChecking] = useState(false);

    const copyToClipboard = async (text: string) => {
        try {
            await navigator.clipboard.writeText(text);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    };

    const checkPaymentStatus = async () => {
        setIsChecking(true);
        try {
            const response = await fetch(
                `/dashboard/payment/${payment.uuid}/check-status`
            );
            const data = await response.json();

            if (data.is_confirmed || data.is_received) {
                router.visit(`/dashboard/payment/${payment.uuid}/success`);
            }
        } catch (error) {
            console.error('Error checking payment status:', error);
        } finally {
            setIsChecking(false);
        }
    };

    // Auto-check payment status every 10 seconds for PIX and Boleto
    useEffect(() => {
        if (payment.payment_method === 'pix' || payment.payment_method === 'boleto') {
            const interval = setInterval(checkPaymentStatus, 10000);
            return () => clearInterval(interval);
        }
    }, [payment.uuid]);

    const statusColors = {
        pending: 'bg-yellow-500',
        confirmed: 'bg-green-500',
        received: 'bg-green-600',
        overdue: 'bg-red-500',
        cancelled: 'bg-gray-500',
    };

    const statusLabels = {
        pending: 'Pendente',
        confirmed: 'Confirmado',
        received: 'Recebido',
        overdue: 'Vencido',
        cancelled: 'Cancelado',
    };

    return (
        <DashboardLayout title={'Detalhes do Pagamento - Asaas'}>
            <div className="container max-w-3xl mx-auto py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold">Detalhes do Pagamento</h1>
                    <p className="text-muted-foreground mt-2">
                        Complete o pagamento para ativar sua assinatura
                    </p>
                </div>

                {/* Status Card */}
                <Card className="mb-6">
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div>
                                <CardTitle>Status do Pagamento</CardTitle>
                                <CardDescription>
                                    Plano {payment.subscription?.plan.name}
                                </CardDescription>
                            </div>
                            <Badge className={statusColors[payment.status]}>
                                {statusLabels[payment.status]}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-muted-foreground">Valor</p>
                                <p className="text-xl font-bold">
                                    {payment.amount_formatted}
                                </p>
                            </div>
                            {payment.due_date && (
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Vencimento
                                    </p>
                                    <p className="text-xl font-bold">
                                        {format(
                                            new Date(payment.due_date),
                                            "dd 'de' MMMM",
                                            { locale: ptBR }
                                        )}
                                    </p>
                                </div>
                            )}
                        </div>
                    </CardContent>
                </Card>

                {/* PIX Payment */}
                {payment.payment_method === 'pix' && payment.pix_qr_code && (
                    <Card className="mb-6">
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <QrCode className="h-5 w-5" />
                                <CardTitle>Pague com PIX</CardTitle>
                            </div>
                            <CardDescription>
                                Escaneie o QR Code abaixo ou copie o código PIX
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="flex flex-col items-center space-y-6">
                                {/* QR Code Image */}
                                <div className="bg-white p-4 rounded-lg border">
                                    <img
                                        src={`data:image/png;base64,${payment.pix_qr_code}`}
                                        alt="QR Code PIX"
                                        className="w-64 h-64"
                                    />
                                </div>

                                {/* Copy PIX Code */}
                                {payment.pix_copy_paste && (
                                    <div className="w-full">
                                        <div className="flex items-center gap-2 p-4 bg-muted rounded-lg">
                                            <code className="flex-1 text-xs overflow-hidden text-ellipsis">
                                                {payment.pix_copy_paste}
                                            </code>
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                onClick={() =>
                                                    copyToClipboard(payment.pix_copy_paste!)
                                                }
                                            >
                                                {copied ? (
                                                    <CheckCircle className="h-4 w-4" />
                                                ) : (
                                                    <Copy className="h-4 w-4" />
                                                )}
                                            </Button>
                                        </div>
                                    </div>
                                )}

                                <div className="text-center text-sm text-muted-foreground">
                                    <Clock className="inline h-4 w-4 mr-1" />
                                    Aguardando confirmação do pagamento...
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Boleto Payment */}
                {payment.payment_method === 'boleto' && (
                    <Card className="mb-6">
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <Receipt className="h-5 w-5" />
                                <CardTitle>Boleto Bancário</CardTitle>
                            </div>
                            <CardDescription>
                                Pague o boleto até a data de vencimento
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {payment.boleto_barcode && (
                                <div>
                                    <p className="text-sm font-medium mb-2">
                                        Código de barras
                                    </p>
                                    <div className="flex items-center gap-2 p-4 bg-muted rounded-lg">
                                        <code className="flex-1 text-sm">
                                            {payment.boleto_barcode}
                                        </code>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            onClick={() =>
                                                copyToClipboard(payment.boleto_barcode!)
                                            }
                                        >
                                            {copied ? (
                                                <CheckCircle className="h-4 w-4" />
                                            ) : (
                                                <Copy className="h-4 w-4" />
                                            )}
                                        </Button>
                                    </div>
                                </div>
                            )}

                            {payment.invoice_url && (
                                <Button
                                    className="w-full"
                                    onClick={() =>
                                        window.open(payment.invoice_url!, '_blank')
                                    }
                                >
                                    Visualizar Boleto
                                </Button>
                            )}

                            <div className="text-center text-sm text-muted-foreground">
                                <Clock className="inline h-4 w-4 mr-1" />
                                Aguardando confirmação do pagamento...
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Credit Card Payment */}
                {payment.payment_method === 'credit_card' && (
                    <Card className="mb-6">
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <CreditCard className="h-5 w-5" />
                                <CardTitle>Pagamento com Cartão de Crédito</CardTitle>
                            </div>
                            <CardDescription>
                                Complete o pagamento inserindo os dados do cartão
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="bg-muted rounded-lg p-6 space-y-4">
                                <div className="flex items-start gap-3">
                                    <CheckCircle className="h-5 w-5 text-green-600 mt-0.5 shrink-0" />
                                    <div className="space-y-1">
                                        <p className="font-medium">Ambiente seguro</p>
                                        <p className="text-sm text-muted-foreground">
                                            Você será redirecionado para a página segura do Asaas para inserir os dados do seu cartão
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <CheckCircle className="h-5 w-5 text-green-600 mt-0.5 shrink-0" />
                                    <div className="space-y-1">
                                        <p className="font-medium">Aprovação imediata</p>
                                        <p className="text-sm text-muted-foreground">
                                            Seu pagamento será processado instantaneamente
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {payment.invoice_url && (
                                <Button
                                    className="w-full"
                                    size="lg"
                                    onClick={() =>
                                        window.open(payment.invoice_url!, '_blank')
                                    }
                                >
                                    <CreditCard className="h-4 w-4 mr-2" />
                                    Ir para Pagamento Seguro
                                </Button>
                            )}

                            <div className="text-center text-sm text-muted-foreground">
                                <Clock className="inline h-4 w-4 mr-1" />
                                Aguardando pagamento...
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Action Buttons */}
                <div className="flex gap-4">
                    <Button
                        variant="outline"
                        onClick={() => router.visit('/dashboard/subscription')}
                        className="flex-1"
                    >
                        Voltar
                    </Button>
                    <Button
                        onClick={checkPaymentStatus}
                        disabled={isChecking}
                        className="flex-1"
                    >
                        {isChecking ? 'Verificando...' : 'Verificar Pagamento'}
                    </Button>
                </div>
            </div>
        </DashboardLayout>
    );
}
