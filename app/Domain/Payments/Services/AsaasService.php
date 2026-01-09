<?php

namespace App\Domain\Payments\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('asaas.api_key');
        $this->apiUrl = config('asaas.api_url');
    }

    /**
     * Create or get customer in Asaas
     */
    public function createCustomer(User $user): array
    {
        // ✅ Use real user CPF (required for paid subscriptions)
        // For sandbox testing, you can use: 24971563792
        if (empty($user->cpf)) {
            throw new \Exception('CPF é obrigatório para processar pagamentos. Por favor, atualize seu perfil com um CPF válido.');
        }

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'cpfCnpj' => $user->cpf,
            'externalReference' => $user->uuid,
        ];

        // Adicionar telefone se disponível
        if ($user->phone) {
            $phoneNumbers = $this->extractNumbers($user->phone);
            $payload['phone'] = $phoneNumbers;
            $payload['mobilePhone'] = $phoneNumbers;
        }

        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/customers", $payload);

        if ($response->failed()) {
            $errorData = $response->json();

            Log::error('Asaas create customer failed', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'response' => $errorData,
            ]);

            // Extract error message from Asaas response
            $errorMessage = 'Erro desconhecido';

            if (isset($errorData['errors']) && is_array($errorData['errors'])) {
                // Handle array of errors
                $errors = collect($errorData['errors'])->pluck('description')->join(', ');
                $errorMessage = $errors ?: $errorMessage;
            } elseif (isset($errorData['message'])) {
                $errorMessage = $errorData['message'];
            }

            throw new \Exception('Falha ao criar cliente no Asaas: ' . $errorMessage);
        }

        return $response->json();
    }

    /**
     * Create a payment (charge) in Asaas
     */
    public function createPayment(string $customerId, array $data): array
    {
        $payload = [
            'customer' => $customerId,
            'billingType' => $this->mapPaymentMethod($data['payment_method']),
            'value' => $data['amount'], // Em reais, não centavos
            'dueDate' => $data['due_date'] ?? now()->addDays(3)->format('Y-m-d'),
            'description' => $data['description'] ?? 'Assinatura MeloSys',
            'externalReference' => $data['external_reference'] ?? null,
        ];

        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/payments", $payload);

        if ($response->failed()) {
            Log::error('Asaas create payment failed', [
                'payload' => $payload,
                'response' => $response->json(),
            ]);

            throw new \Exception('Falha ao criar cobrança: ' . $response->json('message', 'Erro desconhecido'));
        }

        return $response->json();
    }

    /**
     * Get payment details
     */
    public function getPayment(string $paymentId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->get("{$this->apiUrl}/payments/{$paymentId}");

        if ($response->failed()) {
            throw new \Exception('Falha ao buscar cobrança');
        }

        return $response->json();
    }

    /**
     * Get PIX QR Code
     */
    public function getPixQrCode(string $paymentId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->get("{$this->apiUrl}/payments/{$paymentId}/pixQrCode");

        if ($response->failed()) {
            throw new \Exception('Falha ao gerar QR Code PIX');
        }

        return $response->json();
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(string $paymentId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->delete("{$this->apiUrl}/payments/{$paymentId}");

        if ($response->failed()) {
            throw new \Exception('Falha ao cancelar cobrança');
        }

        return $response->json();
    }

    /**
     * Refund payment
     */
    public function refundPayment(string $paymentId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->post("{$this->apiUrl}/payments/{$paymentId}/refund");

        if ($response->failed()) {
            throw new \Exception('Falha ao reembolsar pagamento');
        }

        return $response->json();
    }

    /**
     * Create subscription in Asaas
     */
    public function createSubscription(string $customerId, array $data): array
    {
        $payload = [
            'customer' => $customerId,
            'billingType' => $this->mapPaymentMethod($data['payment_method']),
            'value' => $data['amount'], // Em reais
            'cycle' => 'MONTHLY',
            'description' => $data['description'] ?? 'Assinatura MeloSys',
            'externalReference' => $data['external_reference'] ?? null,
        ];

        if (isset($data['nextDueDate'])) {
            $payload['nextDueDate'] = $data['nextDueDate'];
        }

        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/subscriptions", $payload);

        if ($response->failed()) {
            Log::error('Asaas create subscription failed', [
                'payload' => $payload,
                'response' => $response->json(),
            ]);

            throw new \Exception('Falha ao criar assinatura recorrente: ' . $response->json('message', 'Erro desconhecido'));
        }

        return $response->json();
    }

    /**
     * Get payments from a subscription
     */
    public function getSubscriptionPayments(string $subscriptionId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->get("{$this->apiUrl}/subscriptions/{$subscriptionId}/payments");

        if ($response->failed()) {
            throw new \Exception('Falha ao buscar pagamentos da assinatura');
        }

        return $response->json('data', []);
    }

    /**
     * Cancel subscription in Asaas
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
        ])->delete("{$this->apiUrl}/subscriptions/{$subscriptionId}");

        if ($response->failed()) {
            throw new \Exception('Falha ao cancelar assinatura recorrente');
        }

        return $response->json();
    }

    /**
     * Map internal payment method to Asaas billing type
     */
    protected function mapPaymentMethod(string $method): string
    {
        return match ($method) {
            'pix' => 'PIX',
            'boleto' => 'BOLETO',
            'credit_card' => 'CREDIT_CARD',
            default => 'UNDEFINED',
        };
    }

    /**
     * Extract only numbers from string
     */
    protected function extractNumbers(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
