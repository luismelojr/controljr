<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Transactions\Services\TransactionService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\MarkAsPaidRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use App\Enums\AccountStatusEnum;
use App\Enums\RecurrenceTypeEnum;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        // Get user categories for filter dropdown
        $categories = auth()->user()->categories()
            ->where('status', true)
            ->orWhere('is_default', true)
            ->orderBy('name')
            ->get(['id', 'uuid', 'name']);

        // Get user wallets for filter dropdown
        $wallets = auth()->user()->wallets()
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'uuid', 'name', 'type']);

        return Inertia::render('dashboard/transactions/index', [
            'transactions' => TransactionResource::collection($transactions),
            'categories' => $categories,
            'wallets' => $wallets,
            'filters' => request()->only(['filter', 'sort']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Move to a dedicated Request class
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'paid_at' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'wallet_id' => 'required|exists:wallets,id',
            'status' => 'required|in:pending,paid,overdue',
            'is_reconciled' => 'boolean',
            'external_id' => 'nullable|string',
            'account_id' => 'nullable|exists:accounts,id', // Optional for one-off transactions from reconciliation
        ]);

        // For reconciliation, if no account_id provided, create a one-time account
        $accountId = $validated['account_id'] ?? null;
        
        if (!$accountId) {
            // Create a one-time account for this reconciliation transaction
            $account = Account::create([
                'user_id' => auth()->id(),
                'wallet_id' => $validated['wallet_id'],
                'category_id' => $validated['category_id'],
                'name' => 'Conciliação Bancária - ' . now()->format('d/m/Y H:i'),
                'description' => 'Transação criada via conciliação bancária',
                'total_amount' => $validated['amount'],
                'recurrence_type' => RecurrenceTypeEnum::ONE_TIME,
                'start_date' => $validated['due_date'],
                'status' => AccountStatusEnum::COMPLETED, // Since it's already paid
            ]);
            $accountId = $account->id;
        }

        // Create transaction
        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'account_id' => $accountId,
            'amount' => $validated['amount'],
            'due_date' => $validated['due_date'],
            'paid_at' => $validated['paid_at'] ?? ($validated['status'] === 'paid' ? $validated['due_date'] : null),
            'category_id' => $validated['category_id'],
            'wallet_id' => $validated['wallet_id'],
            'status' => $validated['status'],
            'is_reconciled' => $validated['is_reconciled'] ?? false,
            'external_id' => $validated['external_id'] ?? null,
        ]);
        
        Toast::success('Transação criada com sucesso!');
        return back();
    }

    /**
     * Display transactions for a specific month
     */
    public function month(int $year, int $month): Response
    {

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
