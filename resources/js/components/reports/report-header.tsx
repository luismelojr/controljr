import { SavedReport, ReportTypeOption } from '@/types/reports';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { Calendar, PlayCircle, Star, Edit } from 'lucide-react';
import { router } from '@inertiajs/react';

interface ReportHeaderProps {
    reportType: ReportTypeOption;
    generatedAt: string;
    savedReport?: SavedReport;
    onEdit?: () => void;
}

export function ReportHeader({
    reportType,
    generatedAt,
    savedReport,
    onEdit,
}: ReportHeaderProps) {
    // Format date
    const formattedDate = format(new Date(generatedAt), "dd 'de' MMMM 'de' yyyy 'às' HH:mm", {
        locale: ptBR,
    });

    // Handle edit button
    const handleEdit = () => {
        if (onEdit) {
            onEdit();
        } else if (savedReport) {
            // Navigate to builder with report pre-filled
            router.get(route('dashboard.reports.builder'), {
                report_id: savedReport.uuid,
            });
        }
    };

    return (
        <Card className="border-2">
            <CardContent className="p-6">
                <div className="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    {/* Left side - Report info */}
                    <div className="flex-1 space-y-3">
                        {/* Title and badges */}
                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="text-2xl font-bold">
                                {savedReport?.name || reportType.label}
                            </h1>
                            {savedReport?.is_favorite && (
                                <Badge variant="default" className="gap-1">
                                    <Star className="h-3 w-3 fill-current" />
                                    Favorito
                                </Badge>
                            )}
                            {savedReport?.is_template && (
                                <Badge variant="secondary">Template</Badge>
                            )}
                        </div>

                        {/* Description */}
                        {savedReport?.description ? (
                            <p className="text-muted-foreground">
                                {savedReport.description}
                            </p>
                        ) : (
                            <p className="text-muted-foreground">
                                {reportType.description}
                            </p>
                        )}

                        {/* Report metadata */}
                        <div className="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                            <div className="flex items-center gap-2">
                                <Calendar className="h-4 w-4" />
                                <span>Gerado em {formattedDate}</span>
                            </div>

                            {savedReport && (
                                <>
                                    <div className="flex items-center gap-2">
                                        <PlayCircle className="h-4 w-4" />
                                        <span>
                                            Executado{' '}
                                            {savedReport.run_count === 1
                                                ? '1 vez'
                                                : `${savedReport.run_count} vezes`}
                                        </span>
                                    </div>

                                    {savedReport.last_run_at_human && (
                                        <div className="text-sm">
                                            Última execução:{' '}
                                            {savedReport.last_run_at_human}
                                        </div>
                                    )}
                                </>
                            )}
                        </div>
                    </div>

                    {/* Right side - Actions */}
                    {savedReport && (
                        <div className="flex items-center gap-2">
                            <Button
                                variant="outline"
                                onClick={handleEdit}
                                className="gap-2"
                            >
                                <Edit className="h-4 w-4" />
                                Editar
                            </Button>
                        </div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
