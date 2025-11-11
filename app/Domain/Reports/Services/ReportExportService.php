<?php

namespace App\Domain\Reports\Services;

use App\Enums\ExportFormatEnum;
use App\Enums\ReportTypeEnum;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;

class ReportExportService
{
    /**
     * Export report data to specified format
     */
    public function export(
        array $reportData,
        ReportTypeEnum $reportType,
        ExportFormatEnum $format,
        string $userId
    ): string {
        $filename = $this->generateFilename($reportType, $format, $userId);

        return match ($format) {
            ExportFormatEnum::PDF => $this->exportToPdf($reportData, $reportType, $filename),
            ExportFormatEnum::EXCEL => $this->exportToExcel($reportData, $reportType, $filename),
            ExportFormatEnum::CSV => $this->exportToCsv($reportData, $reportType, $filename),
        };
    }

    /**
     * Export to PDF
     */
    private function exportToPdf(array $reportData, ReportTypeEnum $reportType, string $filename): string
    {
        $html = view('reports.pdf.template', [
            'reportType' => $reportType,
            'data' => $reportData['data'] ?? [],
            'summary' => $reportData['summary'] ?? [],
            'title' => $reportType->label(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->render();

        $path = 'reports/' . $filename;

        Pdf::view('reports.pdf.template', [
            'reportType' => $reportType,
            'data' => $reportData['data'] ?? [],
            'summary' => $reportData['summary'] ?? [],
            'title' => $reportType->label(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])
        ->format('a4')
        ->save(storage_path('app/public/' . $path));

        return $path;
    }

    /**
     * Export to Excel
     */
    private function exportToExcel(array $reportData, ReportTypeEnum $reportType, string $filename): string
    {
        $path = 'reports/' . $filename;

        Excel::store(
            new \App\Exports\ReportExport($reportData, $reportType),
            $path,
            'public'
        );

        return $path;
    }

    /**
     * Export to CSV
     */
    private function exportToCsv(array $reportData, ReportTypeEnum $reportType, string $filename): string
    {
        $path = 'reports/' . $filename;

        Excel::store(
            new \App\Exports\ReportExport($reportData, $reportType),
            $path,
            'public',
            \Maatwebsite\Excel\Excel::CSV
        );

        return $path;
    }

    /**
     * Generate unique filename
     */
    private function generateFilename(
        ReportTypeEnum $reportType,
        ExportFormatEnum $format,
        string $userId
    ): string {
        $timestamp = now()->format('Y-m-d_His');
        $reportSlug = str_replace('_', '-', $reportType->value);
        $extension = $format->extension();

        return "{$reportSlug}_{$userId}_{$timestamp}.{$extension}";
    }

    /**
     * Get download URL for exported file
     */
    public function getDownloadUrl(string $path): string
    {
        return Storage::url($path);
    }

    /**
     * Delete exported file
     */
    public function deleteExport(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Clean old exports (older than 24 hours)
     */
    public function cleanOldExports(): int
    {
        $files = Storage::disk('public')->files('reports');
        $deletedCount = 0;
        $expirationTime = now()->subDay()->timestamp;

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);

            if ($lastModified < $expirationTime) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
