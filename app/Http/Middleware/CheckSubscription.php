<?php

namespace App\Http\Middleware;

use App\Facades\Toast;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * Check if user has an active subscription or is on free plan
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$requiredPlans): Response
    {
        $user = $request->user();

        // If no user is authenticated, let auth middleware handle it
        if (! $user) {
            return $next($request);
        }

        // Check if user has any subscription
        if (! $user->currentSubscription) {
            Toast::warning('Você precisa de uma assinatura para acessar este recurso')
                ->persistent();

            return redirect()->route('dashboard.subscription.plans');
        }

        // Check if subscription is active
        if (! $user->currentSubscription->isActive()) {
            Toast::error('Sua assinatura está inativa. Por favor, renove para continuar.')
                ->persistent();

            return redirect()->route('dashboard.subscription.index');
        }

        // If specific plans are required, check if user has one of them
        if (! empty($requiredPlans)) {
            $userPlanSlug = $user->currentSubscription->plan->slug;

            if (! in_array($userPlanSlug, $requiredPlans)) {
                $planNames = implode(', ', array_map(fn ($slug) => ucfirst($slug), $requiredPlans));

                Toast::error("Este recurso está disponível apenas para os planos: {$planNames}")
                    ->persistent();

                return redirect()->route('dashboard.subscription.plans');
            }
        }

        return $next($request);
    }
}
