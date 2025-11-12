<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Reports\DTO\GenerateReportData;
use App\Domain\Reports\DTO\SaveReportConfigData;
use App\Domain\Reports\Services\ReportService;
use App\Enums\ExportFormatEnum;
use App\Enums\ReportTypeEnum;
use App\Enums\VisualizationTypeEnum;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\GenerateReportRequest;
use App\Http\Requests\Reports\SaveReportRequest;
use App\Http\Resources\SavedReportResource;
use App\Models\SavedReport;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    /**
     * Display list of saved reports
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $userReports = $this->reportService->getUserReports($userId);
        $favorites = $this->reportService->getUserFavorites($userId);
        $templates = $this->reportService->getTemplates();

        return Inertia::render('dashboard/reports/index', [
            'savedReports' => SavedReportResource::collection($userReports),
            'favorites' => SavedReportResource::collection($favorites),
            'templates' => SavedReportResource::collection($templates),
        ]);
    }

    /**
     * Show report builder wizard
     */
    public function builder(Request $request)
    {
        $categories = $request->user()->categories()->where('status', true)->orWhere('is_default', true)->get();
        $wallets = $request->user()->wallets()->where('status', true)->get();

        return Inertia::render('dashboard/reports/builder', [
            'report_types' => $this->getReportTypes(),
            'visualization_types' => $this->getVisualizationTypes(),
            'categories' => $categories,
            'wallets' => $wallets,
        ]);
    }

    /**
     * Generate report (preview)
     */
    public function generate(GenerateReportRequest $request)
    {
        $userId = $request->user()->id;
        $reportData = GenerateReportData::fromRequest($request, $userId);

        try {
            $result = $this->reportService->generateReport($reportData);

            // Render the view page with the generated report
            return Inertia::render('dashboard/reports/view', [
                'report' => [
                    'report_type' => $reportData->reportType->value,
                    'filters' => $reportData->filters,
                    'data' => $result['data'],
                    'summary' => $result['summary'],
                    'generated_at' => now()->toISOString(),
                ],
                'config' => [
                    'report_type' => $reportData->reportType->value,
                    'visualization_type' => $reportData->visualizationType->value,
                    'filters' => $reportData->filters,
                ],
                'reportTypes' => $this->getReportTypes(),
                'visualizationTypes' => $this->getVisualizationTypes(),
            ]);
        } catch (\Exception $e) {
            Toast::error('Erro ao gerar relatório: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Save report configuration
     */
    public function store(SaveReportRequest $request)
    {
        $userId = $request->user()->id;
        $configData = SaveReportConfigData::fromRequest($request, $userId);

        try {
            $savedReport = $this->reportService->saveReportConfig($configData);

            Toast::success('Relatório salvo com sucesso!');

            return redirect()->route('dashboard.reports.show', $savedReport->uuid);
        } catch (\Exception $e) {
            Toast::error('Erro ao salvar relatório: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Show saved report
     */
    public function show(Request $request, SavedReport $report)
    {
        // Verify ownership
        if ($report->user_id !== $request->user()->id && !$report->is_template) {
            abort(403, 'Você não tem permissão para acessar este relatório.');
        }

        return Inertia::render('dashboard/reports/show', [
            'report' => new SavedReportResource($report),
        ]);
    }

    /**
     * Run saved report
     */
    public function run(Request $request, SavedReport $report)
    {
        // Verify ownership
        if ($report->user_id !== $request->user()->id && !$report->is_template) {
            abort(403, 'Você não tem permissão para executar este relatório.');
        }

        try {
            $result = $this->reportService->runSavedReport($report);

            // Render the view page with the generated report
            return Inertia::render('dashboard/reports/view', [
                'report' => [
                    'report_type' => $report->report_type,
                    'filters' => $report->filters,
                    'data' => $result['data'],
                    'summary' => $result['summary'],
                    'generated_at' => $result['metadata']['generated_at'],
                ],
                'config' => [
                    'report_type' => $report->report_type,
                    'visualization_type' => $report->visualization['type'],
                    'filters' => $report->filters,
                ],
                'savedReport' => new SavedReportResource($report),
            ]);
        } catch (\Exception $e) {
            Toast::error('Erro ao executar relatório: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Update saved report
     */
    public function update(Request $request, SavedReport $report)
    {
        // Verify ownership
        if ($report->user_id !== $request->user()->id) {
            abort(403, 'Você não tem permissão para atualizar este relatório.');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_favorite' => ['sometimes', 'boolean'],
        ]);

        try {
            $updated = $this->reportService->updateSavedReport($report, $validated);

            Toast::success('Relatório atualizado com sucesso!');

            return back();
        } catch (\Exception $e) {
            Toast::error('Erro ao atualizar relatório: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Delete saved report
     */
    public function destroy(Request $request, SavedReport $report)
    {
        // Verify ownership
        if ($report->user_id !== $request->user()->id) {
            abort(403, 'Você não tem permissão para deletar este relatório.');
        }

        try {
            $this->reportService->deleteSavedReport($report);

            Toast::success('Relatório deletado com sucesso!');

            return redirect()->route('dashboard.reports.index');
        } catch (\Exception $e) {
            Toast::error('Erro ao deletar relatório: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Request $request, SavedReport $report)
    {
        // Verify ownership
        if ($report->user_id !== $request->user()->id) {
            abort(403);
        }

        try {
            $updated = $this->reportService->toggleFavorite($report);

            $message = $updated->is_favorite
                ? 'Relatório marcado como favorito!'
                : 'Relatório desmarcado como favorito!';

            Toast::success($message);

            return back();
        } catch (\Exception $e) {
            Toast::error('Erro ao atualizar favorito.');
            return back();
        }
    }

    /**
     * Export report
     */
    public function export(Request $request, SavedReport $report)
    {
        // Verify ownership
        if ($report->user_id !== $request->user()->id && !$report->is_template) {
            abort(403);
        }

        $format = $request->input('format', 'pdf');

        try {
            $exportFormat = ExportFormatEnum::from($format);
        } catch (\ValueError $e) {
            Toast::error('Formato de exportação inválido.');
            return back();
        }

        try {
            // Generate report data
            $result = $this->reportService->runSavedReport($report);

            // Get config from saved report
            $reportConfig = GenerateReportData::fromSavedReport($report);

            // Export
            $downloadUrl = $this->reportService->exportReport(
                $result,
                $reportConfig,
                $exportFormat
            );

            // Return file download
            return redirect($downloadUrl);
        } catch (\Exception $e) {
            Toast::error('Erro ao exportar relatório: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Get report types for frontend
     */
    private function getReportTypes(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
                'description' => $case->description(),
                'icon' => $case->icon(),
            ];
        }, ReportTypeEnum::cases());
    }

    /**
     * Get visualization types for frontend
     */
    private function getVisualizationTypes(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
                'icon' => $case->icon(),
            ];
        }, VisualizationTypeEnum::cases());
    }
}
