import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Download, FileText, FileSpreadsheet, FileType } from 'lucide-react';
import { useState } from 'react';

interface ExportButtonsProps {
    reportId?: string;
    reportType: string;
    filters?: Record<string, any>;
}

export function ExportButtons({
    reportId,
    reportType,
    filters = {},
}: ExportButtonsProps) {
    const [isExporting, setIsExporting] = useState(false);
    const [exportingFormat, setExportingFormat] = useState<string | null>(null);

    const handleExport = async (format: 'pdf' | 'excel' | 'csv') => {
        setIsExporting(true);
        setExportingFormat(format);

        try {
            // Build query params
            const params = new URLSearchParams({
                report_type: reportType,
                format,
                ...filters,
            });

            // If it's a saved report, use its ID
            if (reportId) {
                params.set('report_id', reportId);
            }

            // Trigger download by redirecting
            window.location.href = route('dashboard.reports.export') + '?' + params.toString();

            // Reset loading state after a delay
            setTimeout(() => {
                setIsExporting(false);
                setExportingFormat(null);
            }, 2000);
        } catch (error) {
            console.error('Error exporting report:', error);
            setIsExporting(false);
            setExportingFormat(null);
        }
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="default"
                    className="gap-2"
                    disabled={isExporting}
                >
                    <Download className="h-4 w-4" />
                    {isExporting
                        ? `Exportando ${exportingFormat?.toUpperCase()}...`
                        : 'Exportar'}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-48">
                <DropdownMenuLabel>Formato de Exportação</DropdownMenuLabel>
                <DropdownMenuSeparator />

                <DropdownMenuItem
                    onClick={() => handleExport('pdf')}
                    disabled={isExporting}
                    className="gap-2 cursor-pointer"
                >
                    <FileText className="h-4 w-4" />
                    <span>Exportar como PDF</span>
                </DropdownMenuItem>

                <DropdownMenuItem
                    onClick={() => handleExport('excel')}
                    disabled={isExporting}
                    className="gap-2 cursor-pointer"
                >
                    <FileSpreadsheet className="h-4 w-4" />
                    <span>Exportar como Excel</span>
                </DropdownMenuItem>

                <DropdownMenuItem
                    onClick={() => handleExport('csv')}
                    disabled={isExporting}
                    className="gap-2 cursor-pointer"
                >
                    <FileType className="h-4 w-4" />
                    <span>Exportar como CSV</span>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
