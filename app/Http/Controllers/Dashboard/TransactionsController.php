<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Transactions\Actions\CreateTransactionAction;
use App\Domain\Transactions\Actions\MarkTransactionAsPaidAction;
use App\Domain\Transactions\Actions\MarkTransactionAsUnpaidAction;
use App\Domain\Transactions\Services\TransactionService;
use App\Exceptions\TransactionException;
use App\Exceptions\WalletException;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\MarkAsPaidRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

use App\Domain\Tags\Services\TagService;

class TransactionsController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly CreateTransactionAction $createTransactionAction,
        private readonly MarkTransactionAsPaidAction $markTransactionAsPaidAction,
        private readonly MarkTransactionAsUnpaidAction $markTransactionAsUnpaidAction,
        private readonly TagService $tagService,
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
        
        // Ensure tags are loaded if service didn't (normally service should or we modify query)
        // Since getAllForUser return paginator, we can't easily chain with unless using `through`?
        // Actually, $transactions->load('tags') works on Collection, but on Paginator?
        // Paginator proxies calls... let's see.
        // Better update TransactionService. But to be safe and avoid context switching, I'll postpone TransactionService update for tags loading to Verification phase or next step.
        // Priority is Reconciliation.
        
        // Get user categories for filter dropdown

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
            
        // Get user tags
        $tags = $this->tagService->getUserTags(auth()->user());

        return Inertia::render('dashboard/transactions/index', [
            'transactions' => TransactionResource::collection($transactions),
            'categories' => $categories,
            'wallets' => $wallets,
            'tags' => $tags,
            'filters' => request()->only(['filter', 'sort']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $currentCount = auth()->user()->transactions()->count();

        if (\App\Http\Middleware\CheckPlanFeature::hasReachedLimit($request, 'max_transactions', $currentCount)) {
            Toast::error('Você atingiu o limite de transações do seu plano.')
                ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                ->persistent();

            return back();
        }

        $transaction = $this->createTransactionAction->execute(
            user: auth()->user(),
            data: $request->validated()
        );

        if ($request->has('tags')) {
            $this->tagService->syncTags($transaction, $request->input('tags'), auth()->user());
        }

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

        $transactions = $this->transactionService->getTransactionsForMonth(
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
            'tags' => $this->tagService->getUserTags(auth()->user()),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): Response
    {
        $this->authorize('view', $transaction);

        $transaction->load(['account', 'wallet', 'category', 'tags', 'attachments']);

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

            $this->markTransactionAsPaidAction->execute($transaction, $paidAt);

            Toast::success('Transação marcada como paga!');

            return back();
        } catch (TransactionException $e) {
            Toast::error('Erro na transação: '.$e->getMessage());

            return back();
        } catch (WalletException $e) {
            Toast::error('Erro na carteira: '.$e->getMessage());

            return back();
        } catch (\Exception $e) {
            Toast::error('Erro inesperado: '.$e->getMessage());

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

            $this->markTransactionAsUnpaidAction->execute($transaction);

            Toast::success('Transação marcada como não paga!');

            return back();
        } catch (TransactionException $e) {
            Toast::error('Erro na transação: '.$e->getMessage());

            return back();
        } catch (WalletException $e) {
            Toast::error('Erro na carteira: '.$e->getMessage());

            return back();
        } catch (\Exception $e) {
            Toast::error('Erro inesperado: '.$e->getMessage());

            return back();
        }
    }
}
