<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Payments\DTO\CreatePaymentData;
use App\Domain\Payments\Services\PaymentGatewayService;
use App\Domain\Subscriptions\Services\SubscriptionService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentGatewayService,
        protected SubscriptionService $subscriptionService
    ) {
    }

    /**
     * Show payment method selection
     */
    public function choosePaymentMethod(Request $request)
    {
        $user = $request->user();

        // Try to get pending subscription first (e.g. upgrades)
        $subscription = $user->subscriptions()
            ->where('status', 'pending')
            ->latest()
            ->first();

        // If no pending, get current active
        if (! $subscription) {
            $subscription = $user->currentSubscription;
        }

        if (! $subscription) {
            Toast::error('Nenhuma assinatura encontrada. Selecione um plano primeiro.');

            return redirect()->route('dashboard.subscription.plans');
        }

        return Inertia::render('dashboard/payment/payment-method', [
            'subscription' => $subscription->load('plan'),
            'paymentMethods' => config('asaas.payment_methods'),
            'hasCpf' => ! empty($user->cpf),
        ]);
    }

    /**
     * Create payment for subscription
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:pix,boleto,credit_card'],
        ]);

        $user = $request->user();

        // Try to get pending subscription first
        $subscription = $user->subscriptions()
            ->where('status', 'pending')
            ->latest()
            ->first();

        // If no pending, get current active
        if (! $subscription) {
            $subscription = $user->currentSubscription;
        }

        if (! $subscription) {
            Toast::error('Nenhuma assinatura encontrada. Selecione um plano primeiro.');

            return redirect()->route('dashboard.subscription.plans');
        }

        // ✅ CRITICAL: For paid plans, require CPF
        // Frontend checks for CPF before submitting, but double-check here for security
        if (! $subscription->plan->isFree() && empty($user->cpf)) {
            Toast::error('Para processar pagamentos, precisamos do seu CPF. Por favor, informe seu CPF para continuar.');

            return redirect()->route('dashboard.payment.choose-method');
        }

        try {
            // Check if it is an upgrade (Active ID != Pending ID)
            $activeSubscription = $user->currentSubscription;
            $isUpgrade = $activeSubscription && $subscription->status === 'pending' && $activeSubscription->id !== $subscription->id;

            if ($isUpgrade) {
                $payment = $this->subscriptionService->processUpgradePayment(
                    $subscription,
                    $validated['payment_method']
                );
            } else {
                $payment = $this->paymentGatewayService->createSubscriptionPayment(
                    $subscription,
                    $validated['payment_method']
                );
            }

            // Para todos os métodos de pagamento, redirecionar para página de pagamento
            // - PIX: Mostra QR Code
            // - Boleto: Mostra código de barras
            // - Cartão de Crédito: Redireciona para página do Asaas
            Toast::success('Pagamento criado com sucesso!');

            return redirect()->route('dashboard.payment.show', $payment->uuid);
        } catch (\Exception $e) {
            Toast::error('Erro ao criar pagamento: '.$e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Show payment details (PIX QR Code, Boleto, etc.)
     */
    public function show(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('dashboard/payment/show', [
            'payment' => $payment->load('subscription.plan'),
        ]);
    }

    /**
     * Show payment success page
     */
    public function success(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('dashboard/payment/success', [
            'payment' => $payment->load('subscription.plan'),
        ]);
    }

    /**
     * List user payments
     */
    public function index(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->with('subscription.plan')
            ->latest()
            ->paginate(15);

        return Inertia::render('dashboard/payment/index', [
            'payments' => $payments,
        ]);
    }

    /**
     * Check payment status
     */
    public function checkStatus(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $updatedPayment = $this->paymentGatewayService->checkPaymentStatus($payment);

            return response()->json([
                'status' => $updatedPayment->status,
                'is_confirmed' => $updatedPayment->isConfirmed(),
                'is_received' => $updatedPayment->isReceived(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao verificar status do pagamento',
            ], 500);
        }
    }

    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $payment->isPending()) {
            Toast::error('Apenas pagamentos pendentes podem ser cancelados');

            return redirect()->back();
        }

        try {
            $this->paymentGatewayService->cancelPayment($payment);

            Toast::success('Pagamento cancelado com sucesso');

            return redirect()->route('dashboard.payment.index');
        } catch (\Exception $e) {
            Toast::error('Erro ao cancelar pagamento: '.$e->getMessage());

            return redirect()->back();
        }
    }
}
