import { router } from '@inertiajs/react';
import { useState } from 'react';
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
import { formatPriceCent } from '@/lib/format';

export default function PaymentMethod({
    subscription,
    paymentMethods,
    hasCpf,
}: PaymentMethodPageProps) {
    const [selectedMethod, setSelectedMethod] = useState<string>('pix');
    const [isProcessing, setIsProcessing] = useState(false);
    const [showCpfModal, setShowCpfModal] = useState(false);

    const handleSubmit = () => {
        // Check if user has CPF before processing payment (only for paid plans)
        if (!subscription.plan.is_free && !hasCpf) {
            setShowCpfModal(true);
            return;
        }

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

    const handleCpfSaved = () => {
        setShowCpfModal(false);

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
        <DashboardLayout title={subscription.plan.name + ' - Pagamento'}>
            <div className="container mx-auto py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold">Escolha o método de pagamento</h1>
                    <p className="mt-2 text-muted-foreground">Complete o pagamento da sua assinatura {subscription.plan.name}</p>
                </div>

                <Card className="mb-6">
                    <CardHeader>
                        <CardTitle>Resumo da assinatura</CardTitle>
                        <CardDescription>Plano {subscription.plan.name}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="flex items-center justify-between">
                            <span className="text-lg">Total a pagar</span>
                            <span className="text-2xl font-bold">{formatPriceCent(subscription.plan.price_cents)}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Selecione o método de pagamento</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <RadioGroup value={selectedMethod} onValueChange={setSelectedMethod} className="space-y-4">
                            {paymentMethods.pix && (
                                <div className="flex cursor-pointer items-center space-x-3 rounded-lg border p-4 hover:bg-accent">
                                    <RadioGroupItem value="pix" id="pix" className="shrink-0" />
                                    <Label htmlFor="pix" className="flex flex-1 cursor-pointer items-start gap-4">
                                        <div className="mt-1">
                                            <QrCode className="h-6 w-6" />
                                        </div>
                                        <div>
                                            <div className="font-semibold">{methodLabels.pix}</div>
                                            <div className="text-sm text-muted-foreground">{methodDescriptions.pix}</div>
                                        </div>
                                    </Label>
                                </div>
                            )}

                            {paymentMethods.boleto && (
                                <div className="flex cursor-pointer items-center space-x-3 rounded-lg border p-4 hover:bg-accent">
                                    <RadioGroupItem value="boleto" id="boleto" className="shrink-0" />
                                    <Label htmlFor="boleto" className="flex flex-1 cursor-pointer items-start gap-4">
                                        <div className="mt-1">
                                            <Receipt className="h-6 w-6" />
                                        </div>
                                        <div>
                                            <div className="font-semibold">{methodLabels.boleto}</div>
                                            <div className="text-sm text-muted-foreground">{methodDescriptions.boleto}</div>
                                        </div>
                                    </Label>
                                </div>
                            )}

                            {paymentMethods.credit_card && (
                                <div className="flex cursor-pointer items-center space-x-3 rounded-lg border p-4 hover:bg-accent">
                                    <RadioGroupItem value="credit_card" id="credit_card" className="shrink-0" />
                                    <Label htmlFor="credit_card" className="flex flex-1 cursor-pointer items-start gap-4">
                                        <div className="mt-1">
                                            <CreditCard className="h-6 w-6" />
                                        </div>
                                        <div>
                                            <div className="font-semibold">{methodLabels.credit_card}</div>
                                            <div className="text-sm text-muted-foreground">{methodDescriptions.credit_card}</div>
                                        </div>
                                    </Label>
                                </div>
                            )}
                        </RadioGroup>

                        <div className="mt-6 flex gap-4">
                            <Button variant="outline" onClick={() => router.visit('/dashboard/subscription')} className="flex-1">
                                Cancelar
                            </Button>
                            <Button onClick={handleSubmit} disabled={isProcessing} className="flex-1">
                                {isProcessing ? 'Processando...' : 'Continuar'}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* CPF Modal */}
                <CpfModal open={showCpfModal} onOpenChange={setShowCpfModal} onSuccess={handleCpfSaved} />
            </div>
        </DashboardLayout>
    );
}
