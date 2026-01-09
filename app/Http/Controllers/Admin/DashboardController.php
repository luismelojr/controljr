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

        // Revenue Chart Data (Last 30 days)
        $endDate = now();
        $startDate = now()->subDays(29);
        
        $revenueData = Payment::where('status', 'received')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(amount_cents) as total_cents')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => (float) ($item->total_cents / 100)];
            });

        // Fill in missing days with 0
        $chartData = [];
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $chartData[] = [
                'date' => \Carbon\Carbon::parse($date)->format('d/m'),
                'value' => $revenueData[$date] ?? 0,
            ];
        }

        return Inertia::render('admin/dashboard/index', [
            'metrics' => [
                'total_users' => $totalUsers,
                'active_subscriptions' => $activeSubscriptions,
                'mrr' => $mrr,
            ],
            'recent_payments' => $recentPayments,
            'revenue_chart' => $chartData,
        ]);
    }
}
