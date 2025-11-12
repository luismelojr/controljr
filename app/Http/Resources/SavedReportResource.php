<?php

namespace App\Http\Resources;

use App\Enums\ReportTypeEnum;
use App\Enums\VisualizationTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reportType = ReportTypeEnum::from($this->report_type);
        $visualizationType = VisualizationTypeEnum::from($this->visualization['type'] ?? 'table');

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'report_type' => $this->report_type,
            'report_type_label' => $reportType->label(),
            'report_type_description' => $reportType->description(),
            'report_type_icon' => $reportType->icon(),
            'config' => $this->filters,
            'visualization' => [
                'type' => $visualizationType->value,
                'label' => $visualizationType->label(),
                'icon' => $visualizationType->icon(),
            ],
            'visualization_type' => $visualizationType->value,
            'is_template' => $this->is_template,
            'is_favorite' => $this->is_favorite,
            'last_run_at' => $this->last_run_at?->toISOString(),
            'last_run_at_human' => $this->last_run_at?->diffForHumans(),
            'run_count' => $this->run_count,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
