<?php

namespace App\Http\Controllers;

use App\Domain\Payments\DTO\WebhookEventData;
use App\Domain\Payments\Services\WebhookService;
use App\Jobs\ProcessPaymentWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected WebhookService $webhookService
    ) {
    }

    /**
     * Handle Asaas webhook
     */
    public function asaas(Request $request)
    {
        try {
            // 1. Verify webhook signature for security
            $signature = $request->header('asaas-signature');
            $payload = $request->getContent();

            if ($signature && ! $this->webhookService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Invalid webhook signature received');

                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // 2. Get webhook data
            $event = $request->input('event');
            $paymentData = $request->input('payment', []);

            Log::info('Webhook received from Asaas', [
                'event' => $event,
                'payment_id' => $paymentData['id'] ?? null,
            ]);

            // 3. Dispatch job to process webhook asynchronously
            ProcessPaymentWebhook::dispatch(
                WebhookEventData::from([
                    'event' => $event,
                    'payment' => $paymentData,
                ])
            );

            // 4. Return 200 immediately to Asaas
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 even on error to avoid Asaas retrying indefinitely
            return response()->json(['success' => false, 'error' => 'Internal error'], 200);
        }
    }
}
