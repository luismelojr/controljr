<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subscription;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->paginate(15)
            ->through(function ($sub) {
                return [
                    'id' => $sub->id,
                    'user_name' => $sub->user->name,
                    'user_email' => $sub->user->email,
                    'plan_name' => $sub->plan->name,
                    'status' => $sub->status,
                    'status_label' => $sub->status_label,
                    'status_color' => $sub->status_color,
                    'started_at' => $sub->started_at,
                    'ends_at' => $sub->ends_at,
                ];
            });

        return Inertia::render('admin/subscriptions/index', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
