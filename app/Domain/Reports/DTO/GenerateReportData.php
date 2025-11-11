<?php

namespace App\Domain\Reports\DTO;

use App\Enums\ReportTypeEnum;
use App\Enums\VisualizationTypeEnum;
use App\Models\SavedReport;
use Illuminate\Http\Request;

readonly class GenerateReportData
{
    public function __construct(
        public ReportTypeEnum $reportType,
        public ReportFiltersData $filters,
        public VisualizationTypeEnum $visualizationType,
        public string $userId,
    ) {}

    /**
     * Create from Request
     */
    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            reportType: ReportTypeEnum::from($request->input('report_type')),
            filters: ReportFiltersData::fromRequest($request),
            visualizationType: VisualizationTypeEnum::from($request->input('visualization_type', 'table')),
            userId: $userId,
        );
    }

    /**
     * Create from SavedReport model
     */
    public static function fromSavedReport(SavedReport $savedReport): self
    {
        return new self(
            reportType: ReportTypeEnum::from($savedReport->report_type),
            filters: ReportFiltersData::fromArray($savedReport->filters),
            visualizationType: VisualizationTypeEnum::from($savedReport->visualization['type'] ?? 'table'),
            userId: $savedReport->user_id,
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'report_type' => $this->reportType->value,
            'filters' => $this->filters->toArray(),
            'visualization_type' => $this->visualizationType->value,
            'user_id' => $this->userId,
        ];
    }
}
