<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    public function download(Payment $payment)
    {
        // Check authorization: User owns payment OR is admin
        if ($payment->user_id !== auth()->id() && ! auth()->user()->is_admin) {
            abort(403);
        }

        $pdf = Pdf::loadView('invoices.default', compact('payment'));
        
        return $pdf->download('fatura-' . $payment->uuid . '.pdf');
    }
}
