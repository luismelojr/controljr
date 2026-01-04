import { router, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { CreditCard, QrCode, Receipt } from 'lucide-react';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import CpfModal from '@/components/payment/cpf-modal';
import type { PaymentMethodPageProps } from '@/types/payment';

export default function PaymentMethod({
    subscription,
    paymentMethods,
}: PaymentMethodPageProps) {
    const [selectedMethod, setSelectedMethod] = useState<string>('pix');
    const [isProcessing, setIsProcessing] = useState(false);
    const [showCpfModal, setShowCpfModal] = useState(false);
    const { props } = usePage();

    // Check if CPF modal should be shown
    useEffect(() => {
        if (props.requires_cpf) {
            setShowCpfModal(true);
        }
    }, [props.requires_cpf]);

    const handleSubmit = () => {
        setIsProcessing(true);

        router.post(
            '/dashboard/payment/create',
            {
                payment_method: selectedMethod,
            },
            {
                onFinish: () => setIsProcessing(false),
            }
        );
    };

    const methodIcons = {
        pix: QrCode,
        boleto: Receipt,
        credit_card: CreditCard,
    };

    const methodLabels = {
        pix: 'PIX',
        boleto: 'Boleto Bancário',
        credit_card: 'Cartão de Crédito',
    };

    const methodDescriptions = {
        pix: 'Pagamento instantâneo via QR Code',
        boleto: 'Vencimento em 3 dias úteis',
        credit_card: 'Aprovação imediata',
    };

    return (
        <DashboardLayout>
            <div className="container max-w-2xl mx-auto py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold">Escolha o método de pagamento</h1>
                    <p className="text-muted-foreground mt-2">
                        Complete o pagamento da sua assinatura{' '}
                        {subscription.plan.name}
                    </p>
                </div>

                <Card className="mb-6">
                    <CardHeader>
                        <CardTitle>Resumo da assinatura</CardTitle>
                        <CardDescription>
                            Plano {subscription.plan.name}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex justify-between items-center">
                            <span className="text-lg">Total a pagar</span>
                            <span className="text-2xl font-bold">
                                {subscription.plan.price_formatted}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Selecione o método de pagamento</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <RadioGroup
                            value={selectedMethod}
                            onValueChange={setSelectedMethod}
                            className="space-y-4"
                        >
                            {paymentMethods.pix && (
                                <div className="flex items-center space-x-3 border rounded-lg p-4 cursor-pointer hover:bg-accent">
                                    <RadioGroupItem
                                        value="pix"
                                        id="pix"
                                        className="shrink-0"
                                    />
                                    <Label
                                        htmlFor="pix"
                                        className="flex items-start gap-4 flex-1 cursor-pointer"
                                    >
                                        <div className="mt-1">
                                            <QrCode className="h-6 w-6" />
                                        </div>
                                        <div>
                                            <div className="font-semibold">
                                                {methodLabels.pix}
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                {methodDescriptions.pix}
                                            </div>
                                        </div>
                                    </Label>
                                </div>
                            )}

                            {paymentMethods.boleto && (
                                <div className="flex items-center space-x-3 border rounded-lg p-4 cursor-pointer hover:bg-accent">
                                    <RadioGroupItem
                                        value="boleto"
                                        id="boleto"
                                        className="shrink-0"
                                    />
                                    <Label
                                        htmlFor="boleto"
                                        className="flex items-start gap-4 flex-1 cursor-pointer"
                                    >
                                        <div className="mt-1">
                                            <Receipt className="h-6 w-6" />
                                        </div>
                                        <div>
                                            <div className="font-semibold">
                                                {methodLabels.boleto}
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                {methodDescriptions.boleto}
                                            </div>
                                        </div>
                                    </Label>
                                </div>
                            )}

                            {paymentMethods.credit_card && (
                                <div className="flex items-center space-x-3 border rounded-lg p-4 cursor-pointer hover:bg-accent">
                                    <RadioGroupItem
                                        value="credit_card"
                                        id="credit_card"
                                        className="shrink-0"
                                    />
                                    <Label
                                        htmlFor="credit_card"
                                        className="flex items-start gap-4 flex-1 cursor-pointer"
                                    >
                                        <div className="mt-1">
                                            <CreditCard className="h-6 w-6" />
                                        </div>
                                        <div>
                                            <div className="font-semibold">
                                                {methodLabels.credit_card}
                                            </div>
                                            <div className="text-sm text-muted-foreground">
                                                {methodDescriptions.credit_card}
                                            </div>
                                        </div>
                                    </Label>
                                </div>
                            )}
                        </RadioGroup>

                        <div className="mt-6 flex gap-4">
                            <Button
                                variant="outline"
                                onClick={() => router.visit('/dashboard/subscription')}
                                className="flex-1"
                            >
                                Cancelar
                            </Button>
                            <Button
                                onClick={handleSubmit}
                                disabled={isProcessing}
                                className="flex-1"
                            >
                                {isProcessing ? 'Processando...' : 'Continuar'}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* CPF Modal */}
                <CpfModal open={showCpfModal} onOpenChange={setShowCpfModal} />
            </div>
        </DashboardLayout>
    );
}
