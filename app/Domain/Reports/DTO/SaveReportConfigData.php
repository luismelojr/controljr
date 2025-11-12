<?php

namespace App\Domain\Reports\DTO;

use Illuminate\Http\Request;

readonly class SaveReportConfigData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public GenerateReportData $reportConfig,
        public bool $isFavorite = false,
        public bool $isTemplate = false,
    ) {}

    /**
     * Create from Request
     */
    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            reportConfig: GenerateReportData::fromRequest($request, $userId),
            isFavorite: $request->boolean('is_favorite', false),
            isTemplate: $request->boolean('is_template', false),
        );
    }

    /**
     * Convert to array for SavedReport model
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'report_type' => $this->reportConfig->reportType->value,
            'filters' => $this->reportConfig->filters->toArray(),
            'visualization' => [
                'type' => $this->reportConfig->visualizationType->value,
            ],
            'is_favorite' => $this->isFavorite,
            'is_template' => $this->isTemplate,
            'user_id' => $this->reportConfig->userId,
        ];
    }
}
