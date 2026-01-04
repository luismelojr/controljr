<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\SavingsGoals\Services\SavingsGoalService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddContributionRequest;
use App\Http\Requests\StoreSavingsGoalRequest;
use App\Http\Requests\UpdateSavingsGoalRequest;
use App\Models\SavingsGoal;
use App\Http\Middleware\CheckPlanFeature;
use App\Facades\Toast;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SavingsGoalsController extends Controller
{
    public function __construct(protected SavingsGoalService $service) {}

    public function index(Request $request)
    {
        $goals = $this->service->getUserGoals($request->user());

        return Inertia::render('dashboard/savings-goals/index', [
            'goals' => $goals,
        ]);
    }

    public function store(StoreSavingsGoalRequest $request)
    {
        $currentCount = $request->user()->savingsGoals()->count();

        if (CheckPlanFeature::hasReachedLimit($request, 'max_savings_goals', $currentCount)) {
            Toast::create('Você atingiu o limite de metas de economia do seu plano.')
                ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                ->error()
                ->persistent()
                ->flash();

            return back();
        }

        $data = $request->validated();
        
        // Convert amount to cents
        if (isset($data['target_amount'])) {
            $data['target_amount_cents'] = (int) round($data['target_amount'] * 100);
            unset($data['target_amount']);
        }
        
        // Ensure defaults if not provided (though migration has defaults, good to be explicit or leave to DB)
        // Checkboxes often send nothing if unchecked, but we handle that in validation/DB defaults.

        $this->service->create($request->user(), $data);

        Toast::success('Meta de economia criada com sucesso!');

        return back();
    }

    public function update(UpdateSavingsGoalRequest $request, SavingsGoal $savingsGoal)
    {
        $this->authorize('update', $savingsGoal);

        $data = $request->validated();

        if (isset($data['target_amount'])) {
            $data['target_amount_cents'] = (int) round($data['target_amount'] * 100);
            unset($data['target_amount']);
        }

        $this->service->update($savingsGoal, $data);

        Toast::success('Meta de economia atualizada com sucesso!');

        return back();
    }

    public function destroy(SavingsGoal $savingsGoal)
    {
        $this->authorize('delete', $savingsGoal);

        $this->service->delete($savingsGoal);

        Toast::success('Meta de economia excluída com sucesso!');

        return back();
    }

    public function addContribution(AddContributionRequest $request, SavingsGoal $savingsGoal)
    {
        $this->authorize('update', $savingsGoal);

        // Amount comes as BRL float/string
        $amountCents = (int) round($request->validated()['amount'] * 100);

        $this->service->addContribution($savingsGoal, $amountCents);

        Toast::success('Contribuição adicionada com sucesso!');

        return back();
    }
}
