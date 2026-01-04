<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\DTO\CreatePaymentData;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function __construct(
        protected AsaasService $asaasService
    ) {
    }

    /**
     * Create a payment for a subscription
     */
    public function createSubscriptionPayment(Subscription $subscription, string $paymentMethod): Payment
    {
        $data = CreatePaymentData::from([
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'amount_cents' => $subscription->plan->price_cents,
            'payment_method' => $paymentMethod,
            'description' => "Assinatura {$subscription->plan->name} - MeloSys",
        ]);

        return $this->createPayment($data);
    }

    /**
     * Create a payment
     */
    public function createPayment(CreatePaymentData $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data->user_id);

            // 1. Criar/obter customer no Asaas
            $customerId = $this->getOrCreateCustomer($user);

            // 2. Criar pagamento no Asaas
            $asaasPayment = $this->asaasService->createPayment($customerId, [
                'payment_method' => $data->payment_method,
                'amount' => $data->getAmountInReais(),
                'due_date' => $data->due_date,
                'description' => $data->description,
                'external_reference' => $user->uuid,
            ]);

            // 3. Criar registro de pagamento no banco
            $payment = Payment::create([
                'user_id' => $data->user_id,
                'subscription_id' => $data->subscription_id,
                'amount_cents' => $data->amount_cents,
                'status' => $this->mapAsaasStatus($asaasPayment['status']),
                'payment_method' => $data->payment_method,
                'payment_gateway' => 'asaas',
                'external_payment_id' => $asaasPayment['id'],
                'invoice_url' => $asaasPayment['invoiceUrl'] ?? null,
                'due_date' => $asaasPayment['dueDate'] ?? null,
            ]);

            // 4. Buscar dados específicos do método de pagamento
            $this->fetchPaymentMethodData($payment);

            return $payment->fresh();
        });
    }

    /**
     * Get or create customer in Asaas
     */
    protected function getOrCreateCustomer(User $user): string
    {
        // Verifica se usuário já tem customer_id armazenado
        if ($user->currentSubscription?->external_customer_id) {
            return $user->currentSubscription->external_customer_id;
        }

        // Cria novo customer no Asaas
        $customer = $this->asaasService->createCustomer($user);

        // Armazena customer_id na subscription se existir
        if ($user->currentSubscription) {
            $user->currentSubscription->update([
                'external_customer_id' => $customer['id'],
            ]);
        }

        return $customer['id'];
    }

    /**
     * Fetch payment method specific data (PIX QR Code, Boleto barcode, etc.)
     */
    protected function fetchPaymentMethodData(Payment $payment): void
    {
        try {
            if ($payment->isPix()) {
                $pixData = $this->asaasService->getPixQrCode($payment->external_payment_id);

                $payment->update([
                    'pix_qr_code' => $pixData['encodedImage'] ?? null,
                    'pix_copy_paste' => $pixData['payload'] ?? null,
                ]);
            } elseif ($payment->isBoleto()) {
                $paymentData = $this->asaasService->getPayment($payment->external_payment_id);

                $payment->update([
                    'boleto_barcode' => $paymentData['bankSlipUrl'] ?? null,
                    'invoice_url' => $paymentData['invoiceUrl'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment method data', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process credit card payment
     */
    public function processCreditCardPayment(Payment $payment, array $creditCardData): Payment
    {
        try {
            // No Asaas, o pagamento com cartão é criado com os dados do cartão
            // Aqui você implementaria a lógica específica para processar cartão
            // Por enquanto, vamos apenas marcar como confirmado

            $payment->markAsConfirmed();

            return $payment->fresh();
        } catch (\Exception $e) {
            Log::error('Credit card payment failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Cancel a payment
     */
    public function cancelPayment(Payment $payment): bool
    {
        try {
            $this->asaasService->cancelPayment($payment->external_payment_id);

            $payment->update(['status' => 'cancelled']);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cancel payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment): bool
    {
        try {
            $this->asaasService->refundPayment($payment->external_payment_id);

            $payment->update(['status' => 'refunded']);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to refund payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Payment $payment): Payment
    {
        try {
            $asaasPayment = $this->asaasService->getPayment($payment->external_payment_id);

            $payment->update([
                'status' => $this->mapAsaasStatus($asaasPayment['status']),
            ]);

            if ($asaasPayment['status'] === 'CONFIRMED') {
                $payment->markAsConfirmed();
            } elseif ($asaasPayment['status'] === 'RECEIVED') {
                $payment->markAsReceived();
            }

            return $payment->fresh();
        } catch (\Exception $e) {
            Log::error('Failed to check payment status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Map Asaas status to our internal status
     */
    protected function mapAsaasStatus(string $asaasStatus): string
    {
        return match ($asaasStatus) {
            'PENDING' => 'pending',
            'CONFIRMED' => 'confirmed',
            'RECEIVED' => 'received',
            'OVERDUE' => 'overdue',
            'REFUNDED' => 'refunded',
            default => 'pending',
        };
    }
}
