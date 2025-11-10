<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Transactions\Services\TransactionService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\MarkAsPaidRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TransactionsController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = $this->transactionService->getAllForUser(
            user: auth()->user(),
            perPage: request()->integer('per_page', 15),
        );

        return Inertia::render('dashboard/transactions/index', [
            'transactions' => TransactionResource::collection($transactions),
            'filters' => request()->only(['filter', 'sort']),
        ]);
    }

    /**
     * Display transactions for a specific month
     */
    public function month(int $year, int $month): Response
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = $this->transactionService->getTransactionsForMonth(
            user: auth()->user(),
            year: $year,
            month: $month,
        );

        $summary = $this->transactionService->getMonthSummary(
            user: auth()->user(),
            year: $year,
            month: $month,
        );

        return Inertia::render('dashboard/transactions/month', [
            'transactions' => TransactionResource::collection($transactions),
            'summary' => $summary,
            'year' => $year,
            'month' => $month,
            'month_name' => Carbon::create($year, $month)->locale('pt_BR')->monthName,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): Response
    {
        $this->authorize('view', $transaction);

        $transaction->load(['account', 'wallet', 'category']);

        return Inertia::render('dashboard/transactions/show', [
            'transaction' => new TransactionResource($transaction),
        ]);
    }

    /**
     * Mark a transaction as paid
     */
    public function markAsPaid(MarkAsPaidRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        try {
            $paidAt = $request->input('paid_at')
                ? Carbon::parse($request->input('paid_at'))
                : null;

            // Load wallet relationship for credit card limit management
            $transaction->load(['wallet', 'account']);

            $this->transactionService->markAsPaid($transaction, $paidAt);

            Toast::success('Transação marcada como paga!');

            return back();
        } catch (\Exception $e) {
            Toast::error('Erro ao marcar transação como paga: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Mark a transaction as unpaid (undo payment)
     */
    public function markAsUnpaid(Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        try {
            // Load wallet relationship for credit card limit management
            $transaction->load('wallet');

            $this->transactionService->markAsUnpaid($transaction);

            Toast::success('Transação marcada como não paga!');

            return back();
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }
}
