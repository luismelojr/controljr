import { SavedReport } from '@/types/reports';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { PlayCircle, Star, Calendar, MoreVertical, Edit, Trash2 } from 'lucide-react';
import { router } from '@inertiajs/react';
import * as LucideIcons from 'lucide-react';

interface SavedReportCardProps {
    report: SavedReport;
    onDelete?: (uuid: string) => void;
}

export function SavedReportCard({ report, onDelete }: SavedReportCardProps) {
    // Get icon component dynamically
    const getIcon = (iconName: string) => {
        const Icon = (LucideIcons as any)[iconName];
        return Icon ? <Icon className="h-5 w-5" /> : null;
    };

    // Handle run report
    const handleRun = () => {
        router.post(route('dashboard.reports.run', report.uuid));
    };

    // Handle edit
    const handleEdit = () => {
        // Navigate to builder with report pre-filled
        router.get(route('dashboard.reports.builder'), {
            report_id: report.uuid,
        });
    };

    // Handle delete
    const handleDelete = () => {
        if (onDelete) {
            onDelete(report.uuid);
        } else {
            if (confirm('Tem certeza que deseja excluir este relatório?')) {
                router.delete(route('dashboard.reports.destroy', report.uuid));
            }
        }
    };

    return (
        <Card className="group hover:shadow-lg transition-all duration-200 cursor-pointer">
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between gap-2">
                    <div className="flex items-start gap-3 flex-1" onClick={handleRun}>
                        {/* Icon */}
                        <div className="text-primary mt-1">
                            {getIcon(report.report_type_icon)}
                        </div>

                        {/* Title and description */}
                        <div className="flex-1 min-w-0">
                            <div className="flex items-center gap-2 mb-1">
                                <CardTitle className="text-base line-clamp-1">
                                    {report.name}
                                </CardTitle>
                                {report.is_favorite && (
                                    <Star className="h-4 w-4 fill-primary text-primary flex-shrink-0" />
                                )}
                            </div>
                            {report.description && (
                                <CardDescription className="line-clamp-2">
                                    {report.description}
                                </CardDescription>
                            )}
                        </div>
                    </div>

                    {/* Actions dropdown */}
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button
                                variant="ghost"
                                size="icon"
                                className="h-8 w-8 opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                <MoreVertical className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem onClick={handleEdit} className="gap-2">
                                <Edit className="h-4 w-4" />
                                Editar
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                onClick={handleDelete}
                                className="gap-2 text-destructive focus:text-destructive"
                            >
                                <Trash2 className="h-4 w-4" />
                                Excluir
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </CardHeader>

            <CardContent onClick={handleRun}>
                {/* Type badge */}
                <Badge variant="secondary" className="mb-3">
                    {report.report_type_label}
                </Badge>

                {/* Metadata */}
                <div className="space-y-2 text-sm text-muted-foreground">
                    <div className="flex items-center gap-2">
                        <PlayCircle className="h-4 w-4" />
                        <span>
                            Executado{' '}
                            {report.run_count === 1
                                ? '1 vez'
                                : `${report.run_count} vezes`}
                        </span>
                    </div>

                    {report.last_run_at_human && (
                        <div className="flex items-center gap-2">
                            <Calendar className="h-4 w-4" />
                            <span>Última execução: {report.last_run_at_human}</span>
                        </div>
                    )}
                </div>

                {/* Run button - visible on hover */}
                <Button
                    className="w-full mt-4 opacity-0 group-hover:opacity-100 transition-opacity"
                    onClick={(e) => {
                        e.stopPropagation();
                        handleRun();
                    }}
                >
                    <PlayCircle className="h-4 w-4 mr-2" />
                    Executar Relatório
                </Button>
            </CardContent>
        </Card>
    );
}
