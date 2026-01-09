<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Budgets\DTO\CreateBudgetData;
use App\Domain\Budgets\DTO\UpdateBudgetData;
use App\Domain\Budgets\Services\BudgetService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Domain\Tags\Services\TagService;

class BudgetController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService,
        protected TagService $tagService,
    ) {}

    public function index(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : now();
        
        $budgets = $this->budgetService->getBudgetsStatus($request->user(), $date);
        
        $categories = Category::where('user_id', $request->user()->id)
            ->where('status', true)
            ->orWhere('is_default', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('dashboard/budgets/index', [
            'budgets' => $budgets,
            'categories' => $categories,
            'currentDate' => $date->format('Y-m-d'),
            'tags' => $this->tagService->getUserTags($request->user()),
        ]);
    }

    public function store(StoreBudgetRequest $request)
    {
        $currentCount = $request->user()->budgets()->count();

        if (\App\Http\Middleware\CheckPlanFeature::hasReachedLimit($request, 'max_budgets', $currentCount)) {
            \App\Facades\Toast::error('Você atingiu o limite de orçamentos do seu plano.')
                ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                ->persistent();

            return back();
        }

        $validated = $request->validated();

        $data = new CreateBudgetData(
            category_id: $validated['category_id'],
            amount: $validated['amount'],
            period: $validated['period'],
            recurrence: $validated['recurrence'] ?? 'monthly',
        );

        $budget = $this->budgetService->create($request->user(), $data);

        if ($request->has('tags')) {
            $this->tagService->syncTags($budget, $request->input('tags'), $request->user());
        }

        return redirect()->back()->with('success', 'Orçamento criado com sucesso!');
    }

    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $validated = $request->validated();

        $data = new UpdateBudgetData(
            amount: $validated['amount'] ?? null,
            recurrence: $validated['recurrence'] ?? null,
            status: $validated['status'] ?? null,
        );

        $this->budgetService->update($budget, $data);

        if ($request->has('tags')) {
            $this->tagService->syncTags($budget, $request->input('tags'), $request->user());
        }

        return redirect()->back()->with('success', 'Orçamento atualizado com sucesso!');
    }

    public function destroy(Budget $budget)
    {
        $this->budgetService->delete($budget);

        return redirect()->back()->with('success', 'Orçamento removido com sucesso!');
    }
}
