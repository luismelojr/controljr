import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Head, router } from '@inertiajs/react';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { ArrowLeft, Edit, Play, Star, Trash2 } from 'lucide-react';
import { SavedReportResource } from '@/types/reports';
import { Badge } from '@/components/ui/badge';
import { useState } from 'react';

interface ShowReportProps {
    report: SavedReportResource;
}

export default function ShowReport({ report }: ShowReportProps) {
    const [isRunning, setIsRunning] = useState(false);
    const [showDeleteDialog, setShowDeleteDialog] = useState(false);

    /**
     * Go back to reports list
     */
    const handleBack = () => {
        router.get(route('dashboard.reports.index'));
    };

    /**
     * Run the saved report
     */
    const handleRun = () => {
        setIsRunning(true);
        router.get(route('dashboard.reports.run', report.uuid), {}, {
            onFinish: () => setIsRunning(false),
        });
    };

    /**
     * Edit report configuration
     */
    const handleEdit = () => {
        router.get(route('dashboard.reports.builder'), {
            prefill: report.uuid,
        });
    };

    /**
     * Toggle favorite status
     */
    const handleToggleFavorite = () => {
        router.post(route('dashboard.reports.toggleFavorite', report.uuid));
    };

    /**
     * Open delete confirmation dialog
     */
    const handleDelete = () => {
        setShowDeleteDialog(true);
    };

    /**
     * Confirm delete action
     */
    const confirmDelete = () => {
        router.delete(route('dashboard.reports.destroy', report.uuid), {
            onSuccess: () => {
                setShowDeleteDialog(false);
            },
        });
    };

    /**
     * Format period type to Portuguese
     */
    const formatPeriodType = (periodType: string | null) => {
        if (!periodType) return 'N/A';

        const map: Record<string, string> = {
            last_month: 'Último mês',
            last_3_months: 'Últimos 3 meses',
            last_6_months: 'Últimos 6 meses',
            last_year: 'Último ano',
            current_month: 'Mês atual',
            current_year: 'Ano atual',
            custom: 'Personalizado',
        };

        return map[periodType] || periodType;
    };

    return (
        <DashboardLayout title="Detalhes do Relatório">
            <Head title={report.name} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Button variant="ghost" size="icon" onClick={handleBack}>
                            <ArrowLeft className="h-5 w-5" />
                        </Button>
                        <div>
                            <div className="flex items-center gap-3">
                                <h1 className="text-3xl font-bold">{report.name}</h1>
                                {report.is_favorite && (
                                    <Star className="h-5 w-5 fill-yellow-400 text-yellow-400" />
                                )}
                            </div>
                            <p className="text-sm text-muted-foreground mt-1">
                                Criado em {format(new Date(report.created_at), "dd 'de' MMMM 'de' yyyy", { locale: ptBR })}
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <Button variant="outline" size="sm" onClick={handleToggleFavorite}>
                            <Star className={`mr-2 h-4 w-4 ${report.is_favorite ? 'fill-yellow-400 text-yellow-400' : ''}`} />
                            {report.is_favorite ? 'Remover Favorito' : 'Adicionar aos Favoritos'}
                        </Button>
                        <Button variant="outline" size="sm" onClick={handleEdit}>
                            <Edit className="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                        <Button variant="destructive" size="sm" onClick={handleDelete}>
                            <Trash2 className="mr-2 h-4 w-4" />
                            Deletar
                        </Button>
                        <Button onClick={handleRun} disabled={isRunning}>
                            <Play className="mr-2 h-4 w-4" />
                            {isRunning ? 'Executando...' : 'Executar Relatório'}
                        </Button>
                    </div>
                </div>

                {/* Report Information */}
                <div className="grid gap-6 md:grid-cols-2">
                    {/* Basic Info Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Informações Básicas</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {report.description && (
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground mb-1">Descrição</h3>
                                    <p className="text-sm">{report.description}</p>
                                </div>
                            )}

                            <div>
                                <h3 className="text-sm font-medium text-muted-foreground mb-1">Tipo de Relatório</h3>
                                <Badge variant="secondary">{report.report_type_label}</Badge>
                            </div>

                            <div>
                                <h3 className="text-sm font-medium text-muted-foreground mb-1">Tipo de Visualização</h3>
                                <Badge variant="secondary">{report.visualization_type}</Badge>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Statistics Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Estatísticas</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <h3 className="text-sm font-medium text-muted-foreground mb-1">Executado</h3>
                                <p className="text-2xl font-bold">{report.run_count} vezes</p>
                            </div>

                            {report.last_run_at && (
                                <div>
                                    <h3 className="text-sm font-medium text-muted-foreground mb-1">Última Execução</h3>
                                    <p className="text-sm">
                                        {format(new Date(report.last_run_at), "dd 'de' MMMM 'de' yyyy 'às' HH:mm", { locale: ptBR })}
                                    </p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Filters Card */}
                <Card>
                    <CardHeader>
                        <CardTitle>Filtros Configurados</CardTitle>
                        <CardDescription>Filtros aplicados quando este relatório é executado</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <dl className="grid gap-3 text-sm">
                            {report.config.period_type && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Período:</dt>
                                    <dd className="font-medium">{formatPeriodType(report.config.period_type)}</dd>
                                </div>
                            )}

                            {report.config.start_date && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Data Inicial:</dt>
                                    <dd className="font-medium">{format(new Date(report.config.start_date), 'dd/MM/yyyy')}</dd>
                                </div>
                            )}

                            {report.config.end_date && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Data Final:</dt>
                                    <dd className="font-medium">{format(new Date(report.config.end_date), 'dd/MM/yyyy')}</dd>
                                </div>
                            )}

                            {report.config.category_ids && report.config.category_ids.length > 0 && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Categorias:</dt>
                                    <dd className="font-medium">{report.config.category_ids.length} selecionada(s)</dd>
                                </div>
                            )}

                            {report.config.wallet_ids && report.config.wallet_ids.length > 0 && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Carteiras:</dt>
                                    <dd className="font-medium">{report.config.wallet_ids.length} selecionada(s)</dd>
                                </div>
                            )}

                            {report.config.status && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Status:</dt>
                                    <dd className="font-medium capitalize">{report.config.status}</dd>
                                </div>
                            )}

                            {report.config.min_amount !== null && report.config.min_amount !== undefined && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Valor Mínimo:</dt>
                                    <dd className="font-medium">
                                        {new Intl.NumberFormat('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL',
                                        }).format(report.config.min_amount)}
                                    </dd>
                                </div>
                            )}

                            {report.config.max_amount !== null && report.config.max_amount !== undefined && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Valor Máximo:</dt>
                                    <dd className="font-medium">
                                        {new Intl.NumberFormat('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL',
                                        }).format(report.config.max_amount)}
                                    </dd>
                                </div>
                            )}

                            {report.config.limit && (
                                <div className="flex justify-between">
                                    <dt className="text-muted-foreground">Limite:</dt>
                                    <dd className="font-medium">Top {report.config.limit}</dd>
                                </div>
                            )}

                            {!report.config.period_type &&
                                !report.config.start_date &&
                                !report.config.end_date &&
                                (!report.config.category_ids || report.config.category_ids.length === 0) &&
                                (!report.config.wallet_ids || report.config.wallet_ids.length === 0) &&
                                !report.config.status &&
                                !report.config.limit && (
                                    <div className="text-center text-muted-foreground py-4">Nenhum filtro configurado</div>
                                )}
                        </dl>
                    </CardContent>
                </Card>
            </div>

            {/* Delete Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={showDeleteDialog}
                onOpenChange={setShowDeleteDialog}
                onConfirm={confirmDelete}
                title="Excluir relatório?"
                description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente o relatório salvo"
                itemName={report.name}
                confirmText="Excluir"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
