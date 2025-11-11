<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Alerts\DTO\CreateAlertData;
use App\Domain\Alerts\DTO\UpdateAlertData;
use App\Domain\Alerts\Services\AlertService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alert\StoreAlertRequest;
use App\Http\Requests\Alert\UpdateAlertRequest;
use App\Http\Resources\AlertResource;
use App\Models\Alert;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AlertsController extends Controller
{
    public function __construct(
        private AlertService $alertService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $alerts = $this->alertService->getUserAlerts(auth()->id());

        return Inertia::render('dashboard/alerts/index', [
            'alerts' => AlertResource::collection($alerts)->resolve(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlertRequest $request): RedirectResponse
    {
        $data = CreateAlertData::fromRequest($request);

        $this->alertService->create($data);

        Toast::create('Alerta criado com sucesso!')
            ->title('Operação Realizada')
            ->success()
            ->flash();

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlertRequest $request, Alert $alert): RedirectResponse
    {
        // Ensure user owns the alert
        if ($alert->user_id !== auth()->id()) {
            abort(403);
        }

        $data = UpdateAlertData::fromRequest($request);

        $this->alertService->update($alert, $data);

        Toast::create('Alerta atualizado com sucesso!')
            ->title('Operação Realizada')
            ->success()
            ->flash();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alert $alert): RedirectResponse
    {
        // Ensure user owns the alert
        if ($alert->user_id !== auth()->id()) {
            abort(403);
        }

        $this->alertService->delete($alert);

        Toast::create('Alerta excluído com sucesso!')
            ->title('Operação Realizada')
            ->success()
            ->flash();

        return redirect()->back();
    }

    /**
     * Toggle the alert status.
     */
    public function toggleStatus(Alert $alert): RedirectResponse
    {
        // Ensure user owns the alert
        if ($alert->user_id !== auth()->id()) {
            abort(403);
        }

        $this->alertService->toggleStatus($alert);

        $status = $alert->fresh()->is_active ? 'ativado' : 'desativado';

        Toast::create("Alerta {$status} com sucesso!")
            ->title('Operação Realizada')
            ->success()
            ->flash();

        return redirect()->back();
    }
}
