<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Accounts\DTO\CreateAccountData;
use App\Domain\Accounts\DTO\UpdateAccountData;
use App\Domain\Accounts\Services\AccountService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\WalletResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AccountsController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Account::class);

        $accounts = Account::query()
            ->where('user_id', auth()->id())
            ->with(['wallet', 'category', 'transactions'])
            ->latest()
            ->paginate(request()->integer('per_page', 15));

        return Inertia::render('dashboard/accounts/index', [
            'accounts' => AccountResource::collection($accounts),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Account::class);

        // Get user's wallets and categories for the form
        $wallets = Wallet::where('user_id', auth()->id())
            ->where('status', true)
            ->get();

        $categories = Category::query()
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('is_default', true);
            })
            ->where('status', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return Inertia::render('dashboard/accounts/create', [
            'wallets' => WalletResource::collection($wallets),
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $this->authorize('create', Account::class);

        try {
            $data = CreateAccountData::fromRequest($request);

            $this->accountService->create($data, auth()->user());

            Toast::success('Conta criada com sucesso! As transações foram geradas automaticamente.');

            return redirect()->route('dashboard.accounts.index');
        } catch (\Exception $e) {
            Toast::error('Erro ao criar conta: ' . $e->getMessage());

            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account): Response
    {
        $this->authorize('view', $account);

        $account->load(['wallet', 'category', 'transactions']);

        return Inertia::render('dashboard/accounts/show', [
            'account' => new AccountResource($account),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account): Response
    {
        $this->authorize('update', $account);

        return Inertia::render('dashboard/accounts/edit', [
            'account' => new AccountResource($account),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->authorize('update', $account);

        try {
            $data = UpdateAccountData::fromRequest($request);

            $this->accountService->update($account, $data);

            Toast::success('Conta atualizada com sucesso!');

            return redirect()->route('dashboard.accounts.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);

        try {
            $this->accountService->delete($account);

            Toast::success('Conta excluída com sucesso!');

            return redirect()->route('dashboard.accounts.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }

    /**
     * Toggle account status
     */
    public function updateStatus(Account $account): RedirectResponse
    {
        $this->authorize('update', $account);

        try {
            $this->accountService->toggleStatus($account);

            Toast::success('Status da conta atualizado com sucesso!');

            return redirect()->route('dashboard.accounts.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }
}
