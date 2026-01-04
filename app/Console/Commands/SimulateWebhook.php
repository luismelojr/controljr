<?php

namespace App\Console\Commands;

use App\Domain\Payments\DTO\WebhookEventData;
use App\Domain\Payments\Services\WebhookService;
use App\Models\Payment;
use Illuminate\Console\Command;

class SimulateWebhook extends Command
{
    protected $signature = 'asaas:simulate-webhook {paymentId} {event=PAYMENT_RECEIVED}';

    protected $description = 'Simulate Asaas webhook for testing';

    public function handle(WebhookService $webhookService): int
    {
        $paymentId = $this->argument('paymentId');
        $event = $this->argument('event');

        $payment = Payment::find($paymentId);

        if (! $payment) {
            $this->error("Payment #{$paymentId} not found");

            return 1;
        }

        $this->info("Simulating webhook: {$event}");
        $this->info("Payment: #{$payment->id} - {$payment->amount_formatted}");

        $webhookData = WebhookEventData::from([
            'event' => $event,
            'payment' => [
                'id' => $payment->external_payment_id,
                'status' => match ($event) {
                    'PAYMENT_CONFIRMED' => 'CONFIRMED',
                    'PAYMENT_RECEIVED' => 'RECEIVED',
                    'PAYMENT_OVERDUE' => 'OVERDUE',
                    default => 'PENDING',
                },
                'value' => $payment->amount,
            ],
        ]);

        $result = $webhookService->processWebhook($webhookData);

        if ($result) {
            $this->info('✅ Webhook processed successfully!');
            $payment->refresh();
            $this->info("Payment status: {$payment->status}");

            if ($payment->subscription) {
                $payment->subscription->refresh();
                $this->info("Subscription status: {$payment->subscription->status}");
            }

            return 0;
        }

        $this->error('❌ Webhook processing failed');

        return 1;
    }
}
