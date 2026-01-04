<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Wallets\DTO\CreateWalletData;
use App\Domain\Wallets\DTO\UpdateWalletData;
use App\Domain\Wallets\Services\WalletService;
use App\Exceptions\WalletException;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\StoreWalletRequest;
use App\Http\Requests\Wallet\UpdateWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WalletsController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Wallet::class);

        $wallets = auth()->user()->wallets;

        return Inertia::render('dashboard/wallets/index', [
            'wallets' => WalletResource::collection($wallets)->resolve(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Wallet::class);

        return Inertia::render('dashboard/wallets/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWalletRequest $request): RedirectResponse
    {
        $this->authorize('create', Wallet::class);

        try {
            $data = CreateWalletData::fromRequest($request);

            $currentCount = $request->user()->wallets()->count();

            if (\App\Http\Middleware\CheckPlanFeature::hasReachedLimit($request, 'max_wallets', $currentCount)) {
                Toast::create('Você atingiu o limite de carteiras do seu plano.')
                    ->error()
                    ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                    ->persistent()
                    ->flash();

                return back();
            }

            $this->walletService->create($data, auth()->user());

            Toast::create('Carteira criada com sucesso!')
                ->title('Operação Realizada')
                ->success()
                ->flash();

            return redirect()->route('dashboard.wallets.index');
        } catch (WalletException $e) {
            Toast::error('Erro ao criar carteira: '.$e->getMessage());

            return back()->withInput();
        } catch (\Exception $e) {
            Toast::error('Erro inesperado: '.$e->getMessage());

            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet): Response
    {
        $this->authorize('update', $wallet);

        return Inertia::render('dashboard/wallets/edit', [
            'wallet' => new WalletResource($wallet),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWalletRequest $request, Wallet $wallet): RedirectResponse
    {
        $this->authorize('update', $wallet);

        try {
            $data = UpdateWalletData::fromRequest($request);

            $this->walletService->update($wallet, $data);

            Toast::create('Carteira atualizada com sucesso!')
                ->title('Operação Realizada')
                ->success()
                ->flash();

            return redirect()->route('dashboard.wallets.index');
        } catch (WalletException $e) {
            Toast::error('Erro ao atualizar carteira: '.$e->getMessage());

            return back()->withInput();
        } catch (\Exception $e) {
            Toast::error('Erro inesperado: '.$e->getMessage());

            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet): RedirectResponse
    {
        $this->authorize('delete', $wallet);

        try {
            $this->walletService->delete($wallet);

            Toast::create('Carteira excluída com sucesso!')
                ->title('Operação Realizada')
                ->success()
                ->flash();

            return redirect()->route('dashboard.wallets.index');
        } catch (WalletException $e) {
            Toast::error('Erro ao excluir carteira: '.$e->getMessage());

            return back();
        } catch (\Exception $e) {
            Toast::error('Erro inesperado: '.$e->getMessage());

            return back();
        }
    }
}
