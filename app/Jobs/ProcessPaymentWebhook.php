<?php

namespace App\Jobs;

use App\Domain\Payments\DTO\WebhookEventData;
use App\Domain\Payments\Services\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 120, 300]; // 1min, 2min, 5min

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WebhookEventData $webhookData
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(WebhookService $webhookService): void
    {
        try {
            $webhookService->processWebhook($this->webhookData);

            Log::info('Webhook processed successfully', [
                'event' => $this->webhookData->event,
                'payment_id' => $this->webhookData->getExternalPaymentId(),
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook job failed', [
                'event' => $this->webhookData->event,
                'payment_id' => $this->webhookData->getExternalPaymentId(),
                'error' => $e->getMessage(),
            ]);

            throw $e; // Will trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Webhook job failed after all retries', [
            'event' => $this->webhookData->event,
            'payment_id' => $this->webhookData->getExternalPaymentId(),
            'error' => $exception->getMessage(),
        ]);
    }
}
