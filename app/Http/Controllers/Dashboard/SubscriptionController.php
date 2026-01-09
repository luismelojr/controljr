<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Subscriptions\Services\SubscriptionService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Display subscription dashboard
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $currentSubscription = $this->subscriptionService->getActiveSubscription($user);
        $history = $this->subscriptionService->getHistory($user);

        return Inertia::render('dashboard/subscription/index', [
            'currentSubscription' => $currentSubscription ? new SubscriptionResource($currentSubscription) : null,
            'subscriptionHistory' => SubscriptionResource::collection($history),
        ]);
    }

    /**
     * Display available plans
     */
    public function plans(Request $request): Response
    {
        $plans = SubscriptionPlan::active()->orderBy('price_cents')->get();
        $currentSubscription = $this->subscriptionService->getActiveSubscription($request->user());

        return Inertia::render('dashboard/subscription/plans', [
            'plans' => SubscriptionPlanResource::collection($plans),
            'currentPlan' => $currentSubscription?->plan?->slug ?? 'free',
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request, string $planSlug): RedirectResponse
    {
        try {
            $user = $request->user();
            $plan = SubscriptionPlan::active()->bySlug($planSlug)->firstOrFail();

            // Check for existing pending subscription for THIS plan
            $pendingSubscription = $user->subscriptions()
                ->where('status', 'pending')
                ->where('subscription_plan_id', $plan->id)
                ->latest()
                ->first();

            if ($pendingSubscription) {
                 Toast::info('Continuando assinatura pendente...');
                 return redirect()->route('dashboard.payment.choose-method');
            }

            // Create subscription
            $subscription = $this->subscriptionService->create($user, $planSlug);

            // If free plan, activate immediately and redirect to dashboard
            if ($plan->isFree()) {
                Toast::success('Bem-vindo ao plano gratuito!');

                return redirect()->route('dashboard.subscription.index');
            }

            // For paid plans, redirect to payment method selection
            Toast::info('Escolha o método de pagamento para continuar');

            return redirect()->route('dashboard.payment.choose-method');
        } catch (\Exception $e) {
            Toast::error('Erro ao criar assinatura: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $subscription = $this->subscriptionService->getActiveSubscription($user);

            if (! $subscription) {
                Toast::error('Você não possui uma assinatura ativa');

                return back();
            }

            $this->subscriptionService->cancel($subscription, false);

            Toast::success('Assinatura cancelada. Você ainda tem acesso até o fim do período atual.');

            return redirect()->route('dashboard.subscription.index');
        } catch (\Exception $e) {
            Toast::error('Erro ao cancelar assinatura: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Resume cancelled subscription
     */
    public function resume(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $subscription = $user->currentSubscription;

            if (! $subscription || ! $subscription->onGracePeriod()) {
                Toast::error('Assinatura não pode ser retomada');

                return back();
            }

            $this->subscriptionService->resume($subscription);

            Toast::success('Assinatura retomada com sucesso!');

            return redirect()->route('dashboard.subscription.index');
        } catch (\Exception $e) {
            Toast::error('Erro ao retomar assinatura: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Upgrade to a new plan
     */
    public function upgrade(Request $request, string $planSlug): RedirectResponse
    {
        try {
            $user = $request->user();

            $newSubscription = $this->subscriptionService->upgrade($user, $planSlug);

            Toast::success('Plano atualizado com sucesso! Redirecionando para pagamento...');

            return redirect()->route('dashboard.payment.choose-method');
        } catch (\Exception $e) {
            Toast::error('Erro ao atualizar plano: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Downgrade to a new plan
     */
    public function downgrade(Request $request, string $planSlug): RedirectResponse
    {
        try {
            $user = $request->user();

            $this->subscriptionService->downgrade($user, $planSlug);

            Toast::success('Downgrade agendado. O novo plano será ativado no final do período atual.');

            return redirect()->route('dashboard.subscription.index');
        } catch (\Exception $e) {
            Toast::error('Erro ao fazer downgrade: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Preview plan change
     */
    public function previewChange(Request $request, string $planSlug): Response
    {
        $user = $request->user();
        $newPlan = SubscriptionPlan::active()->bySlug($planSlug)->firstOrFail();
        $currentSubscription = $this->subscriptionService->getActiveSubscription($user);

        $isUpgrade = $newPlan->price_cents > ($currentSubscription?->plan->price_cents ?? 0);

        return Inertia::render('dashboard/subscription/preview-change', [
            'newPlan' => new SubscriptionPlanResource($newPlan),
            'currentPlan' => $currentSubscription?->plan ? new SubscriptionPlanResource($currentSubscription->plan) : null,
            'isUpgrade' => $isUpgrade,
        ]);
    }
}
