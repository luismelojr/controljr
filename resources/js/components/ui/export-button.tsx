import { useState } from 'react';
import { Download, FileText, FileSpreadsheet, FileType } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import axios from 'axios';
import { toast } from 'sonner';

interface ExportButtonProps {
    /** Tipo de entidade a exportar */
    entity: 'transactions' | 'incomes' | 'accounts' | 'budgets';
    /** Filtros adicionais para passar na exportação */
    filters?: Record<string, any>;
}

export default function ExportButton({
    entity,
    filters = {},
}: ExportButtonProps) {
    const [exporting, setExporting] = useState(false);

    const handleExport = async (format: 'pdf' | 'excel' | 'csv') => {
        setExporting(true);

        try {
            const response = await axios.post(
                route(`dashboard.exports.${entity}`),
                {
                    format,
                    ...filters,
                },
                {
                    responseType: 'blob',
                }
            );

            // Create a blob from the response data
            const blob = new Blob([response.data], {
                type: response.headers['content-type'],
            });

            // Create a link element and trigger the download
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            
            // Try to get filename from content-disposition header
            const contentDisposition = response.headers['content-disposition'];
            let filename = `export-${entity}-${format}.${format === 'excel' ? 'xlsx' : format}`;
            
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
                if (filenameMatch && filenameMatch.length === 2) {
                    filename = filenameMatch[1];
                }
            }
            
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            
            // Cleanup
            link.parentNode?.removeChild(link);
            window.URL.revokeObjectURL(url);
            
        } catch (error: any) {
            console.error('Export error:', error);
            
            // Try to parse the error message from the blob
            if (error.response && error.response.data instanceof Blob) {
                try {
                    const text = await error.response.data.text();
                    const json = JSON.parse(text);
                    toast.error(json.message || 'Erro ao exportar arquivo.');
                } catch (e) {
                    toast.error('Erro ao exportar arquivo.');
                }
            } else {
                toast.error('Erro ao exportar arquivo.');
            }
        } finally {
            setExporting(false);
        }
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="outline"
                    className="gap-2"
                    disabled={exporting}
                >
                    <Download className="h-4 w-4" />
                    {exporting ? 'Exportando...' : 'Exportar'}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem
                    onClick={() => handleExport('excel')}
                    className="cursor-pointer gap-2"
                >
                    <FileSpreadsheet className="h-4 w-4" />
                    <span>Excel</span>
                </DropdownMenuItem>
                <DropdownMenuItem
                    onClick={() => handleExport('csv')}
                    className="cursor-pointer gap-2"
                >
                    <FileType className="h-4 w-4" />
                    <span>CSV</span>
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
