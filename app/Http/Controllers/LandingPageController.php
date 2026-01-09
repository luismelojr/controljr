<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Inertia\Inertia;
use Inertia\Response;

class LandingPageController extends Controller
{
    public function __invoke(): Response
    {
        $plans = SubscriptionPlan::active()->orderBy('price_cents')->get();

        return Inertia::render('landing-page', [
            'plans' => SubscriptionPlanResource::collection($plans),
        ]);
    }
}
