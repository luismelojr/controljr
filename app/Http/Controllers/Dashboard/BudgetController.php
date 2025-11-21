<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Budgets\DTO\CreateBudgetData;
use App\Domain\Budgets\DTO\UpdateBudgetData;
use App\Domain\Budgets\Services\BudgetService;
use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService
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
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|date',
            'recurrence' => 'in:monthly,once',
        ]);

        $data = new CreateBudgetData(
            category_id: $validated['category_id'],
            amount: $validated['amount'],
            period: $validated['period'],
            recurrence: $validated['recurrence'] ?? 'monthly',
        );

        $this->budgetService->create($request->user(), $data);

        return redirect()->back()->with('success', 'Orçamento criado com sucesso!');
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'amount' => 'numeric|min:0.01',
            'recurrence' => 'in:monthly,once',
            'status' => 'boolean',
        ]);

        $data = new UpdateBudgetData(
            amount: $validated['amount'] ?? null,
            recurrence: $validated['recurrence'] ?? null,
            status: $validated['status'] ?? null,
        );

        $this->budgetService->update($budget, $data);

        return redirect()->back()->with('success', 'Orçamento atualizado com sucesso!');
    }

    public function destroy(Budget $budget)
    {
        $this->budgetService->delete($budget);

        return redirect()->back()->with('success', 'Orçamento removido com sucesso!');
    }
}
