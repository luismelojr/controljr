<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Incomes\DTO\CreateIncomeData;
use App\Domain\Incomes\DTO\UpdateIncomeData;
use App\Domain\Incomes\Services\IncomeService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Income\StoreIncomeRequest;
use App\Http\Requests\Income\UpdateIncomeRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\IncomeResource;
use App\Models\Category;
use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IncomesController extends Controller
{
    public function __construct(
        private IncomeService $incomeService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Income::class);

        $incomes = Income::query()
            ->where('user_id', auth()->id())
            ->with(['category', 'incomeTransactions'])
            ->latest()
            ->paginate(request()->integer('per_page', 15));

        return Inertia::render('dashboard/incomes/index', [
            'incomes' => IncomeResource::collection($incomes),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Income::class);

        // Get user's categories for the form
        $categories = Category::query()
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('is_default', true);
            })
            ->where('status', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return Inertia::render('dashboard/incomes/create', [
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncomeRequest $request): RedirectResponse
    {
        $this->authorize('create', Income::class);

        try {
            $data = CreateIncomeData::fromRequest($request);

            $this->incomeService->create($data, auth()->user());

            Toast::success('Receita criada com sucesso! As transações foram geradas automaticamente.');

            return redirect()->route('dashboard.incomes.index');
        } catch (\Exception $e) {
            Toast::error('Erro ao criar receita: ' . $e->getMessage());

            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Income $income): Response
    {
        $this->authorize('view', $income);

        $income->load(['category', 'incomeTransactions']);

        return Inertia::render('dashboard/incomes/show', [
            'income' => new IncomeResource($income),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income): Response
    {
        $this->authorize('update', $income);

        return Inertia::render('dashboard/incomes/edit', [
            'income' => new IncomeResource($income),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncomeRequest $request, Income $income): RedirectResponse
    {
        $this->authorize('update', $income);

        try {
            $data = UpdateIncomeData::fromRequest($request);

            $this->incomeService->update($income, $data);

            Toast::success('Receita atualizada com sucesso!');

            return redirect()->route('dashboard.incomes.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income): RedirectResponse
    {
        $this->authorize('delete', $income);

        try {
            $this->incomeService->delete($income);

            Toast::success('Receita excluída com sucesso!');

            return redirect()->route('dashboard.incomes.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }

    /**
     * Toggle income status
     */
    public function updateStatus(Income $income): RedirectResponse
    {
        $this->authorize('update', $income);

        try {
            $this->incomeService->toggleStatus($income);

            Toast::success('Status da receita atualizado com sucesso!');

            return redirect()->route('dashboard.incomes.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }
}
