import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ReportViewProps } from '@/types/reports';
import { Head, router } from '@inertiajs/react';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { ArrowLeft, Download, FileSpreadsheet, FileText, Save, Table as TableIcon } from 'lucide-react';
import { useState } from 'react';
import { SaveReportDialog } from '@/components/reports/save-report-dialog';
import { PieChartView } from '@/components/reports/pie-chart-view';
import { BarChartView } from '@/components/reports/bar-chart-view';
import { LineChartView } from '@/components/reports/line-chart-view';

export default function ReportView({ report, config, savedReport }: ReportViewProps) {
    const [isSaving, setIsSaving] = useState(false);
    const [showSaveDialog, setShowSaveDialog] = useState(false);

    /**
     * Format currency (values come in reais from backend)
     */
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    /**
     * Format number
     */
    const formatNumber = (value: number) => {
        return new Intl.NumberFormat('pt-BR').format(value);
    };

    /**
     * Format percentage
     */
    const formatPercentage = (value: number) => {
        return `${value.toFixed(1)}%`;
    };

    /**
     * Go back to reports list
     */
    const handleBack = () => {
        router.get(route('dashboard.reports.index'));
    };

    /**
     * Export report
     */
    const handleExport = (format: 'pdf' | 'excel' | 'csv') => {
        if (!savedReport) {
            alert('O relatório precisa estar salvo para ser exportado');
            return;
        }

        window.location.href = route('dashboard.reports.export', {
            report: savedReport.uuid,
            format,
        });
    };

    /**
     * Save report configuration
     */
    const handleSave = (name: string, description?: string) => {
        setIsSaving(true);

        router.post(
            route('dashboard.reports.store'),
            {
                name,
                description,
                report_type: config.report_type,
                visualization_type: config.visualization_type,
                ...config.filters,
            },
            {
                onFinish: () => {
                    setIsSaving(false);
                    setShowSaveDialog(false);
                },
            },
        );
    };

    return (
        <DashboardLayout title="Visualizar Relatório">
            <Head title="Visualizar Relatório" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Button variant="ghost" size="icon" onClick={handleBack}>
                            <ArrowLeft className="h-5 w-5" />
                        </Button>
                        <div>
                            <h1 className="text-3xl font-bold">{savedReport?.name || 'Visualizar Relatório'}</h1>
                            <p className="text-sm text-muted-foreground">
                                Gerado em {format(new Date(report.generated_at), "dd 'de' MMMM 'de' yyyy 'às' HH:mm", { locale: ptBR })}
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        {!savedReport && (
                            <Button variant="outline" onClick={() => setShowSaveDialog(true)} disabled={isSaving}>
                                <Save className="mr-2 h-4 w-4" />
                                Salvar Relatório
                            </Button>
                        )}

                        {savedReport && (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="outline">
                                        <Download className="mr-2 h-4 w-4" />
                                        Exportar
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem onClick={() => handleExport('pdf')}>
                                        <FileText className="mr-2 h-4 w-4" />
                                        Exportar como PDF
                                    </DropdownMenuItem>
                                    <DropdownMenuItem onClick={() => handleExport('excel')}>
                                        <FileSpreadsheet className="mr-2 h-4 w-4" />
                                        Exportar como Excel
                                    </DropdownMenuItem>
                                    <DropdownMenuItem onClick={() => handleExport('csv')}>
                                        <TableIcon className="mr-2 h-4 w-4" />
                                        Exportar como CSV
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        )}
                    </div>
                </div>

                {/* Summary Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Total</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">{formatCurrency(report.summary.total)}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Quantidade</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-2xl font-bold">{formatNumber(report.summary.count)}</p>
                        </CardContent>
                    </Card>

                    {report.summary.average !== undefined && (
                        <Card>
                            <CardHeader className="pb-2">
                                <CardDescription>Média</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <p className="text-2xl font-bold">{formatCurrency(report.summary.average)}</p>
                            </CardContent>
                        </Card>
                    )}

                    {report.summary.categories_count !== undefined && (
                        <Card>
                            <CardHeader className="pb-2">
                                <CardDescription>Categorias</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <p className="text-2xl font-bold">{formatNumber(report.summary.categories_count)}</p>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Chart Visualization */}
                <Card>
                    <CardHeader>
                        <CardTitle>Visualização</CardTitle>
                        <CardDescription>Representação gráfica dos dados do relatório</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {config.visualization_type === 'pie' && <PieChartView data={report.data} />}
                        {config.visualization_type === 'bar' && <BarChartView data={report.data} orientation="vertical" />}
                        {config.visualization_type === 'horizontal_bar' && <BarChartView data={report.data} orientation="horizontal" />}
                        {config.visualization_type === 'line' && <LineChartView data={report.data} />}
                        {config.visualization_type === 'table' && (
                            <div className="flex h-64 items-center justify-center">
                                <p className="text-sm text-muted-foreground">Visualização em tabela (veja abaixo)</p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Data Table */}
                <Card>
                    <CardHeader>
                        <CardTitle>Dados Detalhados</CardTitle>
                        <CardDescription>Visualização tabular dos dados do relatório</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {report.data.length === 0 ? (
                            <div className="py-8 text-center text-sm text-muted-foreground">Nenhum dado encontrado para os filtros aplicados</div>
                        ) : (
                            <div className="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Nome</TableHead>
                                            <TableHead className="text-right">Valor</TableHead>
                                            {report.data[0]?.percentage !== undefined && <TableHead className="text-right">Percentual</TableHead>}
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {report.data.map((item, index) => (
                                            <TableRow key={index}>
                                                <TableCell className="font-medium">{item.name}</TableCell>
                                                <TableCell className="text-right">{formatCurrency(item.value)}</TableCell>
                                                {item.percentage !== undefined && (
                                                    <TableCell className="text-right">{formatPercentage(item.percentage)}</TableCell>
                                                )}
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Applied Filters */}
                {Object.keys(report.filters).length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Filtros Aplicados</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <dl className="grid gap-2 text-sm">
                                {report.filters.start_date && (
                                    <div className="flex justify-between">
                                        <dt className="text-muted-foreground">Data Inicial:</dt>
                                        <dd className="font-medium">{format(new Date(report.filters.start_date), 'dd/MM/yyyy')}</dd>
                                    </div>
                                )}
                                {report.filters.end_date && (
                                    <div className="flex justify-between">
                                        <dt className="text-muted-foreground">Data Final:</dt>
                                        <dd className="font-medium">{format(new Date(report.filters.end_date), 'dd/MM/yyyy')}</dd>
                                    </div>
                                )}
                                {report.filters.period_type && (
                                    <div className="flex justify-between">
                                        <dt className="text-muted-foreground">Período:</dt>
                                        <dd className="font-medium capitalize">{report.filters.period_type}</dd>
                                    </div>
                                )}
                                {report.filters.status && (
                                    <div className="flex justify-between">
                                        <dt className="text-muted-foreground">Status:</dt>
                                        <dd className="font-medium capitalize">{report.filters.status}</dd>
                                    </div>
                                )}
                            </dl>
                        </CardContent>
                    </Card>
                )}
            </div>

            {/* Save Report Dialog */}
            <SaveReportDialog
                open={showSaveDialog}
                onOpenChange={setShowSaveDialog}
                onSave={handleSave}
                isSaving={isSaving}
            />
        </DashboardLayout>
    );
}
