<?php

namespace App\Domain\Payments\DTO;

use Spatie\LaravelData\Data;

class WebhookEventData extends Data
{
    public function __construct(
        public string $event, // PAYMENT_CREATED, PAYMENT_CONFIRMED, etc.
        public array $payment, // Dados do pagamento do Asaas
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
}
