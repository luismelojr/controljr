<?php

namespace App\Http\Controllers;

use App\Domain\Reconciliation\Services\ReconciliationService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReconciliationController extends Controller
{
    public function __construct(
        protected ReconciliationService $reconciliationService
    ) {}

    public function index()
    {
        return Inertia::render('Reconciliation/Index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:ofx,txt,xml', // OFX often has varied mime types
        ]);

        $results = $this->reconciliationService->processFile($request->user(), $request->file('file'));

        return response()->json($results);
    }

    public function reconcile(Request $request, Transaction $transaction)
    {
        $request->validate([
            'external_id' => 'required|string',
        ]);

        $this->reconciliationService->reconcile($transaction, $request->external_id);

        return redirect()->back()->with('success', 'Transaction reconciled successfully.');
    }
    
    public function store(Request $request)
    {
        // Used when user chooses "Create New" from the reconciliation list
        // This would typically reuse TransactionController logic or call TransactionService
        // For now, we'll leave it as a TODO or simple redirect
        return redirect()->back();
    }
}

