<?php

namespace App\Domain\Payments\DTO;

use Spatie\LaravelData\Data;

class WebhookEventData extends Data
{
    public function __construct(
        public string $event, // PAYMENT_CREATED, SUBSCRIPTION_CREATED, etc.
        public ?array $payment = null, // Dados do pagamento do Asaas (para eventos PAYMENT_*)
        public ?array $subscription = null, // Dados da assinatura do Asaas (para eventos SUBSCRIPTION_*)
    ) {
    }

    public function getExternalPaymentId(): ?string
    {
        return $this->payment['id'] ?? null;
    }

    public function getStatus(): ?string
    {
        return $this->payment['status'] ?? null;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->payment['billingType'] ?? null;
    }

    public function getInvoiceUrl(): ?string
    {
        return $this->payment['invoiceUrl'] ?? null;
    }

    public function getExternalReference(): ?string
    {
        return $this->payment['externalReference'] ?? null;
    }

    public function getExternalSubscriptionId(): ?string
    {
        return $this->subscription['id'] ?? null;
    }
}
