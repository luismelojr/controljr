<?php

namespace App\Http\Controllers;

use App\Domain\Payments\DTO\WebhookEventData;
use App\Domain\Payments\Services\WebhookService;
use App\Jobs\ProcessPaymentWebhook;
use App\Models\WebhookCall;
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
        $webhookCall = null;
        try {
            // Log incoming webhook
            $webhookCall = WebhookCall::create([
                'type' => $request->input('event'),
                'payload' => $request->all(),
            ]);

            // 1. Verify webhook signature for security
            $signature = $request->header('asaas-signature');
            $payload = $request->getContent();

            if ($signature && ! $this->webhookService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Invalid webhook signature received');
                $webhookCall->update(['exception' => 'Invalid signature']);

                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // 2. Get webhook data
            $event = $request->input('event');
            $paymentData = $request->input('payment', []);
            $subscriptionData = $request->input('subscription', []);

            Log::info('Webhook received from Asaas', [
                'event' => $event,
                'payment_id' => $paymentData['id'] ?? null,
                'subscription_id' => $subscriptionData['id'] ?? null,
            ]);

            // 3. Dispatch job to process webhook asynchronously
            ProcessPaymentWebhook::dispatch(
                WebhookEventData::from([
                    'event' => $event,
                    'payment' => $paymentData,
                    'subscription' => $subscriptionData,
                ]),
                $webhookCall
            );

            // 4. Return 200 immediately to Asaas
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($webhookCall) {
                $webhookCall->update(['exception' => $e->getMessage()]);
            }

            // Return 200 even on error to avoid Asaas retrying indefinitely
            return response()->json(['success' => false, 'error' => 'Internal error'], 200);
        }
    }

    /**
     * Health check endpoint for webhooks
     * Use this to verify webhook configuration
     */
    public function healthCheck()
    {
        $webhookToken = config('asaas.webhook_token');
        $apiKey = config('asaas.api_key');
        $environment = config('asaas.environment');

        return response()->json([
            'status' => 'ok',
            'webhook_endpoint' => url('/webhook/asaas'),
            'configuration' => [
                'webhook_token_configured' => ! empty($webhookToken),
                'api_key_configured' => ! empty($apiKey),
                'environment' => $environment,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Test webhook endpoint
     * Simulates a webhook call without signature validation
     */
    public function test(Request $request)
    {
        if (! app()->environment('local', 'development')) {
            return response()->json(['error' => 'Test endpoint only available in development'], 403);
        }

        $event = $request->input('event', 'PAYMENT_RECEIVED');
        $paymentData = $request->input('payment', [
            'id' => 'test_payment_'.uniqid(),
            'status' => 'RECEIVED',
            'value' => 100.00,
        ]);

        Log::info('TEST Webhook received', [
            'event' => $event,
            'payment' => $paymentData,
        ]);

        // Process immediately (not via queue) for testing
        $result = $this->webhookService->processWebhook(
            WebhookEventData::from([
                'event' => $event,
                'payment' => $paymentData,
            ])
        );

        return response()->json([
            'success' => $result,
            'event' => $event,
            'message' => $result ? 'Webhook processed successfully' : 'Webhook processing failed',
        ]);
    }
}
