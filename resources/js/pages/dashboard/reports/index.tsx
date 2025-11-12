import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ReportsIndexProps, SavedReport } from '@/types/reports';
import { Head, router } from '@inertiajs/react';
import {
    Activity,
    ArrowDown,
    BarChart,
    Clock,
    Eye,
    FileText,
    MoreVertical,
    PieChart,
    Plus,
    Star,
    StarOff,
    Trash2,
    TrendingDown,
    TrendingUp,
    Wallet,
} from 'lucide-react';
import { useState } from 'react';

export default function ReportsIndex({ savedReports = [], templates = [], favorites = [] }: ReportsIndexProps) {
    const [deleteDialog, setDeleteDialog] = useState<{
        open: boolean;
        reportUuid: string | null;
        reportName: string;
    }>({
        open: false,
        reportUuid: null,
        reportName: '',
    });

    /**
     * Get icon component by name
     */
    const getIcon = (iconName: string) => {
        const icons: Record<string, any> = {
            PieChart,
            Wallet,
            TrendingDown,
            ArrowDown,
            TrendingUp,
            Activity,
            BarChart,
            FileText,
        };
        const Icon = icons[iconName] || FileText;
        return <Icon className="h-8 w-8" />;
    };

    /**
     * Navigate to builder page
     */
    const handleCreate = () => {
        router.get(route('dashboard.reports.builder'));
    };

    /**
     * Run a saved report
     */
    const handleRun = (uuid: string) => {
        router.post(route('dashboard.reports.run', { report: uuid }));
    };

    /**
     * View report details
     */
    const handleView = (uuid: string) => {
        router.get(route('dashboard.reports.show', { report: uuid }));
    };

    /**
     * Toggle favorite status
     */
    const handleToggleFavorite = (uuid: string) => {
        router.post(
            route('dashboard.reports.favorite', { report: uuid }),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    /**
     * Open delete confirmation dialog
     */
    const handleDelete = (uuid: string, name: string) => {
        setDeleteDialog({
            open: true,
            reportUuid: uuid,
            reportName: name,
        });
    };

    /**
     * Confirm delete action
     */
    const confirmDelete = () => {
        if (deleteDialog.reportUuid) {
            router.delete(route('dashboard.reports.destroy', { report: deleteDialog.reportUuid }), {
                preserveScroll: true,
                onSuccess: () => {
                    setDeleteDialog({ open: false, reportUuid: null, reportName: '' });
                },
            });
        }
    };

    /**
     * Render a single report card
     */
    const renderReportCard = (report: SavedReport, showActions: boolean = true) => (
        <Card key={report.uuid} className="group relative overflow-hidden transition-all hover:shadow-lg">
            <CardHeader>
                <div className="flex items-start justify-between">
                    <div className="flex items-center gap-3">
                        <div className="rounded-lg bg-primary/10 p-2 text-primary">{getIcon(report.report_type_icon)}</div>
                        <div className="flex-1">
                            <div className="flex items-center gap-2">
                                <CardTitle className="text-lg">{report.name}</CardTitle>
                                {report.is_favorite && <Star className="h-4 w-4 fill-yellow-500 text-yellow-500" />}
                                {report.is_template && (
                                    <Badge variant="secondary" className="text-xs">
                                        Template
                                    </Badge>
                                )}
                            </div>
                            <CardDescription className="mt-1">{report.report_type_label}</CardDescription>
                        </div>
                    </div>

                    {showActions && !report.is_template && (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="icon-sm" className="opacity-0 group-hover:opacity-100">
                                    <MoreVertical className="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem onClick={() => handleView(report.uuid)}>
                                    <Eye className="mr-2 h-4 w-4" />
                                    Ver Detalhes
                                </DropdownMenuItem>
                                <DropdownMenuItem onClick={() => handleToggleFavorite(report.uuid)}>
                                    {report.is_favorite ? (
                                        <>
                                            <StarOff className="mr-2 h-4 w-4" />
                                            Remover dos Favoritos
                                        </>
                                    ) : (
                                        <>
                                            <Star className="mr-2 h-4 w-4" />
                                            Adicionar aos Favoritos
                                        </>
                                    )}
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem onClick={() => handleDelete(report.uuid, report.name)} className="text-destructive">
                                    <Trash2 className="mr-2 h-4 w-4" />
                                    Excluir
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    )}
                </div>
            </CardHeader>

            <CardContent>
                {report.description && <p className="mb-4 text-sm text-muted-foreground">{report.description}</p>}

                <div className="space-y-2">
                    <div className="flex items-center gap-2 text-xs text-muted-foreground">
                        <BarChart className="h-3 w-3" />
                        <span>{report.visualization.label}</span>
                    </div>

                    {report.last_run_at && (
                        <div className="flex items-center gap-2 text-xs text-muted-foreground">
                            <Clock className="h-3 w-3" />
                            <span>Última execução: {report.last_run_at_human}</span>
                        </div>
                    )}

                    <div className="flex items-center gap-2 text-xs text-muted-foreground">
                        <Eye className="h-3 w-3" />
                        <span>Executado {report.run_count} vez(es)</span>
                    </div>
                </div>

                <Button onClick={() => handleRun(report.uuid)} className="mt-4 w-full" size="sm">
                    Executar Relatório
                </Button>
            </CardContent>
        </Card>
    );

    return (
        <DashboardLayout title="Relatórios">
            <Head title="Relatórios" />
            <div className="space-y-8">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Relatórios</h1>
                        <p className="text-muted-foreground">Analise seus dados financeiros com relatórios personalizados</p>
                    </div>
                    <Button onClick={handleCreate}>
                        <Plus className="mr-2 h-4 w-4" />
                        Criar Novo Relatório
                    </Button>
                </div>

                {/* Favoritos */}
                {favorites.length > 0 && (
                    <section>
                        <div className="mb-4 flex items-center gap-2">
                            <Star className="h-5 w-5 fill-yellow-500 text-yellow-500" />
                            <h2 className="text-xl font-semibold">Favoritos</h2>
                        </div>
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{favorites.map((report) => renderReportCard(report))}</div>
                    </section>
                )}

                {/* Templates */}
                {templates.length > 0 && (
                    <section>
                        <div className="mb-4">
                            <h2 className="text-xl font-semibold">Templates Prontos</h2>
                            <p className="text-sm text-muted-foreground">Relatórios pré-configurados para começar rapidamente</p>
                        </div>
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{templates.map((report) => renderReportCard(report, false))}</div>
                    </section>
                )}

                {/* Meus Relatórios */}
                <section>
                    <div className="mb-4">
                        <h2 className="text-xl font-semibold">Meus Relatórios</h2>
                        <p className="text-sm text-muted-foreground">Relatórios personalizados criados por você</p>
                    </div>

                    {savedReports.length === 0 ? (
                        <Card className="p-12 text-center">
                            <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                                <FileText className="h-10 w-10 text-muted-foreground" />
                            </div>
                            <h3 className="mt-4 text-xl font-semibold">Nenhum relatório criado</h3>
                            <p className="mt-2 text-muted-foreground">
                                Crie seu primeiro relatório personalizado para analisar seus dados financeiros.
                            </p>
                            <Button onClick={handleCreate} className="mt-6">
                                <Plus className="mr-2 h-4 w-4" />
                                Criar Primeiro Relatório
                            </Button>
                        </Card>
                    ) : (
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{savedReports.map((report) => renderReportCard(report))}</div>
                    )}
                </section>
            </div>
            {/* Delete Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={deleteDialog.open}
                onOpenChange={(open) => setDeleteDialog((prev) => ({ ...prev, open }))}
                onConfirm={confirmDelete}
                title="Excluir relatório?"
                description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente o relatório salvo"
                itemName={deleteDialog.reportName}
                confirmText="Excluir"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
