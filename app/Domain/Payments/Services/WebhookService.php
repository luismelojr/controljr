<?php

namespace App\Domain\Payments\Services;

use App\Domain\Payments\DTO\WebhookEventData;
use App\Domain\Subscriptions\Services\SubscriptionService;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {
    }

    /**
     * Process webhook event from Asaas
     */
    public function processWebhook(WebhookEventData $data): bool
    {
        try {
            Log::info('Processing Asaas webhook', [
                'event' => $data->event,
                'payment_id' => $data->getExternalPaymentId(),
            ]);

            return match ($data->event) {
                // Eventos de pagamento único
                'PAYMENT_CREATED' => $this->handlePaymentCreated($data),
                'PAYMENT_UPDATED' => $this->handlePaymentUpdated($data),
                'PAYMENT_CONFIRMED' => $this->handlePaymentConfirmed($data),
                'PAYMENT_RECEIVED' => $this->handlePaymentReceived($data),
                'PAYMENT_OVERDUE' => $this->handlePaymentOverdue($data),
                'PAYMENT_REFUNDED' => $this->handlePaymentRefunded($data),
                'PAYMENT_DELETED' => $this->handlePaymentDeleted($data),

                // Eventos de assinatura recorrente
                'SUBSCRIPTION_CREATED' => $this->handleSubscriptionCreated($data),
                'SUBSCRIPTION_UPDATED' => $this->handleSubscriptionUpdated($data),
                'SUBSCRIPTION_DELETED' => $this->handleSubscriptionDeleted($data),

                default => $this->handleUnknownEvent($data),
            };
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'event' => $data->event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Handle PAYMENT_CREATED event
     */
    protected function handlePaymentCreated(WebhookEventData $data): bool
    {
        // Pagamento já foi criado quando usuário iniciou checkout
        // Apenas log para auditoria
        Log::info('Payment created in Asaas', [
            'external_id' => $data->getExternalPaymentId(),
        ]);

        return true;
    }

    /**
     * Handle PAYMENT_UPDATED event
     */
    protected function handlePaymentUpdated(WebhookEventData $data): bool
    {
        $payment = $this->findPaymentByExternalId($data->getExternalPaymentId());

        if (! $payment) {
            return false;
        }

        $payment->update([
            'status' => $this->mapAsaasStatus($data->getStatus()),
        ]);

        return true;
    }

    /**
     * Handle PAYMENT_CONFIRMED event
     */
    protected function handlePaymentConfirmed(WebhookEventData $data): bool
    {
        $payment = $this->findPaymentByExternalId($data->getExternalPaymentId());

        if (! $payment) {
            return false;
        }

        $payment->markAsConfirmed();

        // Se for pagamento de assinatura, ativar a assinatura
        if ($payment->subscription_id) {
            $this->activateSubscription($payment->subscription);
        }

        Log::info('Payment confirmed', [
            'payment_id' => $payment->id,
            'subscription_id' => $payment->subscription_id,
        ]);

        return true;
    }

    /**
     * Handle PAYMENT_RECEIVED event
     */
    protected function handlePaymentReceived(WebhookEventData $data): bool
    {
        $payment = $this->findPaymentByExternalId($data->getExternalPaymentId());

        if (! $payment) {
            return false;
        }

        $payment->markAsReceived();

        // Se for pagamento de assinatura, ativar a assinatura
        if ($payment->subscription_id) {
            $this->activateSubscription($payment->subscription);
        }

        Log::info('Payment received', [
            'payment_id' => $payment->id,
            'subscription_id' => $payment->subscription_id,
        ]);

        return true;
    }

    /**
     * Handle PAYMENT_OVERDUE event
     */
    protected function handlePaymentOverdue(WebhookEventData $data): bool
    {
        $payment = $this->findPaymentByExternalId($data->getExternalPaymentId());

        if (! $payment) {
            return false;
        }

        $payment->markAsOverdue();

        // Se for pagamento de assinatura, cancelar a assinatura
        if ($payment->subscription_id) {
            $this->subscriptionService->cancel($payment->subscription->user);
        }

        Log::warning('Payment overdue', [
            'payment_id' => $payment->id,
            'subscription_id' => $payment->subscription_id,
        ]);

        return true;
    }

    /**
     * Handle PAYMENT_REFUNDED event
     */
    protected function handlePaymentRefunded(WebhookEventData $data): bool
    {
        $payment = $this->findPaymentByExternalId($data->getExternalPaymentId());

        if (! $payment) {
            return false;
        }

        $payment->update(['status' => 'refunded']);

        Log::info('Payment refunded', [
            'payment_id' => $payment->id,
        ]);

        return true;
    }

    /**
     * Handle PAYMENT_DELETED event
     */
    protected function handlePaymentDeleted(WebhookEventData $data): bool
    {
        $payment = $this->findPaymentByExternalId($data->getExternalPaymentId());

        if (! $payment) {
            return false;
        }

        $payment->update(['status' => 'cancelled']);

        Log::info('Payment deleted', [
            'payment_id' => $payment->id,
        ]);

        return true;
    }

    /**
     * Handle SUBSCRIPTION_CREATED event
     */
    protected function handleSubscriptionCreated(WebhookEventData $data): bool
    {
        // Assinatura recorrente criada no Asaas
        // Já criamos a subscription no nosso banco quando usuário iniciou checkout
        Log::info('Subscription created in Asaas', [
            'subscription_id' => $data->subscription['id'] ?? null,
        ]);

        return true;
    }

    /**
     * Handle SUBSCRIPTION_UPDATED event
     */
    protected function handleSubscriptionUpdated(WebhookEventData $data): bool
    {
        // Assinatura recorrente atualizada (upgrade, downgrade, etc)
        $externalSubscriptionId = $data->subscription['id'] ?? null;

        if (! $externalSubscriptionId) {
            return false;
        }

        $subscription = Subscription::where('external_subscription_id', $externalSubscriptionId)->first();

        if (! $subscription) {
            Log::warning('Subscription not found for webhook', [
                'external_subscription_id' => $externalSubscriptionId,
            ]);

            return false;
        }

        Log::info('Subscription updated in Asaas', [
            'subscription_id' => $subscription->id,
            'external_subscription_id' => $externalSubscriptionId,
        ]);

        return true;
    }

    /**
     * Handle SUBSCRIPTION_DELETED event
     */
    protected function handleSubscriptionDeleted(WebhookEventData $data): bool
    {
        // Assinatura recorrente cancelada no Asaas
        $externalSubscriptionId = $data->subscription['id'] ?? null;

        if (! $externalSubscriptionId) {
            return false;
        }

        $subscription = Subscription::where('external_subscription_id', $externalSubscriptionId)->first();

        if (! $subscription) {
            Log::warning('Subscription not found for deletion webhook', [
                'external_subscription_id' => $externalSubscriptionId,
            ]);

            return false;
        }

        // Cancelar assinatura se ainda não estiver cancelada
        if ($subscription->status !== 'cancelled') {
            $subscription->cancel();

            Log::info('Subscription cancelled via Asaas webhook', [
                'subscription_id' => $subscription->id,
            ]);
        }

        return true;
    }

    /**
     * Handle unknown event
     */
    protected function handleUnknownEvent(WebhookEventData $data): bool
    {
        Log::warning('Unknown webhook event received', [
            'event' => $data->event,
            'payment_data' => $data->payment,
        ]);

        return true;
    }

    /**
     * Find payment by external ID
     */
    protected function findPaymentByExternalId(string $externalId): ?Payment
    {
        $payment = Payment::where('external_payment_id', $externalId)->first();

        if (! $payment) {
            Log::warning('Payment not found for webhook', [
                'external_payment_id' => $externalId,
            ]);
        }

        return $payment;
    }

    /**
     * Activate subscription after successful payment
     */
    protected function activateSubscription(Subscription $subscription): void
    {
        if ($subscription->status !== 'active') {
            // Get user's current active subscription (if any)
            $currentSubscription = $subscription->user->currentSubscription;

            // Activate the new subscription
            $this->subscriptionService->activate($subscription);

            // Cancel old subscription if exists and is different
            if ($currentSubscription && $currentSubscription->id !== $subscription->id) {
                $currentSubscription->cancel();

                Log::info('Old subscription cancelled after upgrade', [
                    'old_subscription_id' => $currentSubscription->id,
                    'new_subscription_id' => $subscription->id,
                ]);
            }

            Log::info('Subscription activated via webhook', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
            ]);
        }
    }

    /**
     * Map Asaas status to our internal status
     */
    protected function mapAsaasStatus(?string $asaasStatus): string
    {
        if (! $asaasStatus) {
            return 'pending';
        }

        return match ($asaasStatus) {
            'PENDING' => 'pending',
            'CONFIRMED' => 'confirmed',
            'RECEIVED' => 'received',
            'OVERDUE' => 'overdue',
            'REFUNDED' => 'refunded',
            default => 'pending',
        };
    }

    /**
     * Verify webhook signature (security)
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $webhookToken = config('asaas.webhook_token');

        if (! $webhookToken) {
            Log::warning('Webhook token not configured');

            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookToken);

        return hash_equals($expectedSignature, $signature);
    }
}
