<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\IncomeTransactions\Services\IncomeTransactionService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeTransaction\MarkAsReceivedRequest;
use App\Http\Resources\IncomeTransactionResource;
use App\Models\IncomeTransaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IncomeTransactionsController extends Controller
{
    public function __construct(
        private IncomeTransactionService $incomeTransactionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', IncomeTransaction::class);

        $incomeTransactions = $this->incomeTransactionService->getAllForUser(
            user: auth()->user(),
            perPage: request()->integer('per_page', 15),
        );

        return Inertia::render('dashboard/income-transactions/index', [
            'incomeTransactions' => IncomeTransactionResource::collection($incomeTransactions),
            'filters' => request()->only(['filter', 'sort']),
        ]);
    }

    /**
     * Display income transactions for a specific month
     */
    public function month(int $year, int $month): Response
    {
        $this->authorize('viewAny', IncomeTransaction::class);

        $incomeTransactions = $this->incomeTransactionService->getIncomeTransactionsForMonth(
            user: auth()->user(),
            year: $year,
            month: $month,
        );

        $summary = $this->incomeTransactionService->getMonthSummary(
            user: auth()->user(),
            year: $year,
            month: $month,
        );

        return Inertia::render('dashboard/income-transactions/month', [
            'incomeTransactions' => IncomeTransactionResource::collection($incomeTransactions),
            'summary' => $summary,
            'year' => $year,
            'month' => $month,
            'month_name' => Carbon::create($year, $month)->locale('pt_BR')->monthName,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(IncomeTransaction $incomeTransaction): Response
    {
        $this->authorize('view', $incomeTransaction);

        $incomeTransaction->load(['income', 'category']);

        return Inertia::render('dashboard/income-transactions/show', [
            'incomeTransaction' => new IncomeTransactionResource($incomeTransaction),
        ]);
    }

    /**
     * Mark an income transaction as received
     */
    public function markAsReceived(MarkAsReceivedRequest $request, IncomeTransaction $incomeTransaction): RedirectResponse
    {
        $this->authorize('update', $incomeTransaction);

        try {
            $receivedAt = $request->input('received_at')
                ? Carbon::parse($request->input('received_at'))
                : null;

            $this->incomeTransactionService->markAsReceived($incomeTransaction, $receivedAt);

            Toast::success('Receita marcada como recebida!');

            return back();
        } catch (\Exception $e) {
            Toast::error('Erro ao marcar receita como recebida: ' . $e->getMessage());

            return back();
        }
    }

    /**
     * Mark an income transaction as not received (undo receipt)
     */
    public function markAsNotReceived(IncomeTransaction $incomeTransaction): RedirectResponse
    {
        $this->authorize('update', $incomeTransaction);

        try {
            $this->incomeTransactionService->markAsNotReceived($incomeTransaction);

            Toast::success('Receita marcada como não recebida!');

            return back();
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }
}
