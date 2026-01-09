<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function subscriptions(): StreamedResponse
    {
        $filename = 'assinaturas-' . Date::now()->format('Y-m-d-H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Usuário', 'Email', 'Plano', 'Status', 'Data Início', 'Data Fim', 'Pagamento']);

            Subscription::with(['user', 'plan'])->chunk(100, function ($subscriptions) use ($handle) {
                foreach ($subscriptions as $sub) {
                    fputcsv($handle, [
                        $sub->id,
                        $sub->user->name,
                        $sub->user->email,
                        $sub->plan->name,
                        $sub->status,
                        $sub->started_at?->format('d/m/Y H:i'),
                        $sub->ends_at?->format('d/m/Y H:i'),
                        $sub->payment_gateway,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function payments(): StreamedResponse
    {
        $filename = 'pagamentos-' . Date::now()->format('Y-m-d-H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Usuário', 'Email', 'Valor', 'Status', 'Método', 'Data', 'ID Externo']);

            Payment::with(['user'])->chunk(100, function ($payments) use ($handle) {
                foreach ($payments as $payment) {
                    fputcsv($handle, [
                        $payment->uuid,
                        $payment->user->name,
                        $payment->user->email,
                        number_format($payment->amount, 2, ',', '.'),
                        $payment->status,
                        $payment->billing_type,
                        $payment->created_at->format('d/m/Y H:i'),
                        $payment->external_id,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
