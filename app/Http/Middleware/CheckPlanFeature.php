<?php

namespace App\Http\Middleware;

use App\Facades\Toast;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     *
     * Check if user's plan allows access to a specific feature
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        // If no user is authenticated, let auth middleware handle it
        if (! $user) {
            return $next($request);
        }

        // Get user's plan limits
        $planLimits = $user->getPlanLimits();

        // Check if feature exists in plan limits
        if (! isset($planLimits[$feature])) {
            // Feature not defined in plan, allow access (fail open)
            return $next($request);
        }

        $featureLimit = $planLimits[$feature];

        // If feature is disabled (value is false or 0)
        if ($featureLimit === false || $featureLimit === 0) {
            Toast::error('Este recurso não está disponível no seu plano atual')
                ->persistent();

            return redirect()->route('dashboard.subscription.plans');
        }

        // Feature is available, continue
        return $next($request);
    }

    /**
     * Check if user has reached feature limit
     *
     * This can be used in controllers to check count limits
     */
    public static function hasReachedLimit(Request $request, string $feature, int $currentCount): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        $planLimits = $user->getPlanLimits();

        if (! isset($planLimits[$feature])) {
            return false;
        }

        $limit = $planLimits[$feature];

        // -1 means unlimited
        if ($limit === -1) {
            return false;
        }

        // Check if current count has reached limit
        return $currentCount >= $limit;
    }
}
