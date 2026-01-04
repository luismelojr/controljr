<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::with('user')
            ->latest()
            ->paginate(15)
            ->through(function ($payment) {
                return [
                    'uuid' => $payment->uuid,
                    'user_name' => $payment->user->name,
                    'amount_formatted' => $payment->value_formatted,
                    'status' => $payment->status,
                    'billing_type' => $payment->billing_type,
                    'date' => $payment->created_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('admin/payments/index', [
            'payments' => $payments,
        ]);
    }
}
