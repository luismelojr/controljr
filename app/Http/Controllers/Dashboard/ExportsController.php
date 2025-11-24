<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Exports\Services\ExportService;
use App\Domain\Exports\DTO\ExportTransactionsData;
use App\Domain\Exports\DTO\ExportIncomesData;
use App\Domain\Exports\DTO\ExportAccountsData;
use App\Domain\Exports\DTO\ExportFiltersData;
use App\Http\Controllers\Controller;
use App\Facades\Toast;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportsController extends Controller
{
    public function __construct(
        protected ExportService $exportService
    ) {}

    /**
     * Exporta transações
     */
    public function transactions(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:excel,csv',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'category_ids' => 'nullable|array',
            'wallet_ids' => 'nullable|array',
            'status' => 'nullable|string',
        ]);

        try {
            $data = ExportTransactionsData::from([
                'format' => $validated['format'],
                'filters' => ExportFiltersData::from([
                    'user_id' => $request->user()->id,
                    'start_date' => isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                    'end_date' => isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null,
                    'category_ids' => $validated['category_ids'] ?? null,
                    'wallet_ids' => $validated['wallet_ids'] ?? null,
                    'status' => $validated['status'] ?? null,
                ]),
            ]);

            $filePath = $this->exportService->exportTransactions($data);

            Toast::success('Transações exportadas com sucesso!');

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Toast::error('Erro ao exportar: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Exporta receitas
     */
    public function incomes(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:excel,csv',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'category_ids' => 'nullable|array',
            'wallet_ids' => 'nullable|array',
        ]);

        try {
            $data = ExportIncomesData::from([
                'format' => $validated['format'],
                'filters' => ExportFiltersData::from([
                    'user_id' => $request->user()->id,
                    'start_date' => isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                    'end_date' => isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null,
                    'category_ids' => $validated['category_ids'] ?? null,
                    'wallet_ids' => $validated['wallet_ids'] ?? null,
                ]),
            ]);

            $filePath = $this->exportService->exportIncomes($data);

            Toast::success('Receitas exportadas com sucesso!');

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Toast::error('Erro ao exportar: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Exporta contas
     */
    public function accounts(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:excel,csv',
            'status' => 'nullable|string',
        ]);

        try {
            $data = ExportAccountsData::from([
                'user_id' => $request->user()->id,
                'format' => $validated['format'],
                'status' => $validated['status'] ?? null,
            ]);

            $filePath = $this->exportService->exportAccounts($data);

            Toast::success('Contas exportadas com sucesso!');

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Toast::error('Erro ao exportar: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Exporta orçamentos
     */
    public function budgets(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:excel,csv',
        ]);

        try {
            $filePath = $this->exportService->exportBudgets(
                $request->user()->id,
                $validated['format']
            );

            Toast::success('Orçamentos exportados com sucesso!');

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Toast::error('Erro ao exportar: ' . $e->getMessage());
            return back();
        }
    }
}
