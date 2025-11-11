<?php

namespace App\Enums;

enum ExportFormatEnum: string
{
    case PDF = 'pdf';
    case EXCEL = 'excel';
    case CSV = 'csv';

    /**
     * Get the label for the export format
     */
    public function label(): string
    {
        return match ($this) {
            self::PDF => 'PDF',
            self::EXCEL => 'Excel',
            self::CSV => 'CSV',
        };
    }

    /**
     * Get the MIME type for the export format
     */
    public function mimeType(): string
    {
        return match ($this) {
            self::PDF => 'application/pdf',
            self::EXCEL => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::CSV => 'text/csv',
        };
    }

    /**
     * Get the file extension for the export format
     */
    public function extension(): string
    {
        return match ($this) {
            self::PDF => 'pdf',
            self::EXCEL => 'xlsx',
            self::CSV => 'csv',
        };
    }

    /**
     * Get the icon for the export format (lucide-react icon name)
     */
    public function icon(): string
    {
        return match ($this) {
            self::PDF => 'FileText',
            self::EXCEL => 'Sheet',
            self::CSV => 'Table',
        };
    }
}
