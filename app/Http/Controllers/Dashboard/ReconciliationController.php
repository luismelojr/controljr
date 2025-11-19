<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Reconciliation\Services\ReconciliationService;
use App\Http\Controllers\Controller;
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
        // Get user categories for the create dialog
        $categories = auth()->user()->categories()
            ->where('status', true)
            ->orWhere('is_default', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get user wallets for the create dialog
        $wallets = auth()->user()->wallets()
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return Inertia::render('dashboard/reconciliation/index', [
            'categories' => $categories,
            'wallets' => $wallets,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file', // Removed strict mime check as OFX files often have varying mime types
        ]);

        try {
            $results = $this->reconciliationService->processFile($request->user(), $request->file('file'));
            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('OFX Import Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process file: ' . $e->getMessage()], 422);
        }
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

