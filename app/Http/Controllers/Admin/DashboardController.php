<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalUsers = User::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        
        // Calculate MRR (simple version: sum of active plan prices)
        // Ideally this should be more robust, but for now it works.
        $mrr = Subscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->sum(function ($sub) {
                return $sub->plan->price_cents;
            });

        // Convert cents to float
        $mrr = $mrr / 100;

        $recentPayments = Payment::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'uuid' => $payment->uuid,
                    'user_name' => $payment->user->name,
                    'amount_formatted' => $payment->value_formatted,
                    'status' => $payment->status,
                    'date' => $payment->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('admin/dashboard/index', [
            'metrics' => [
                'total_users' => $totalUsers,
                'active_subscriptions' => $activeSubscriptions,
                'mrr' => $mrr,
            ],
            'recent_payments' => $recentPayments,
        ]);
    }
}
