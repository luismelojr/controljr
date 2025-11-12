import { SavedReport } from '@/types/reports';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Sparkles, PlayCircle } from 'lucide-react';
import { router } from '@inertiajs/react';
import * as LucideIcons from 'lucide-react';

interface TemplateCardProps {
    template: SavedReport;
}

export function TemplateCard({ template }: TemplateCardProps) {
    // Get icon component dynamically
    const getIcon = (iconName: string) => {
        const Icon = (LucideIcons as any)[iconName];
        return Icon ? <Icon className="h-5 w-5" /> : null;
    };

    // Handle use template
    const handleUseTemplate = () => {
        // Navigate to builder with template pre-filled
        router.get(route('dashboard.reports.builder'), {
            template_id: template.uuid,
        });
    };

    return (
        <Card className="group hover:shadow-lg transition-all duration-200 border-2 border-primary/20 bg-gradient-to-br from-background to-primary/5">
            <CardHeader className="pb-3">
                <div className="flex items-start gap-3">
                    {/* Icon */}
                    <div className="text-primary mt-1">
                        {getIcon(template.report_type_icon)}
                    </div>

                    {/* Title and description */}
                    <div className="flex-1 min-w-0">
                        <div className="flex items-center gap-2 mb-1">
                            <CardTitle className="text-base line-clamp-1">
                                {template.name}
                            </CardTitle>
                            <Badge variant="default" className="gap-1 flex-shrink-0">
                                <Sparkles className="h-3 w-3" />
                                Template
                            </Badge>
                        </div>
                        {template.description && (
                            <CardDescription className="line-clamp-2">
                                {template.description}
                            </CardDescription>
                        )}
                    </div>
                </div>
            </CardHeader>

            <CardContent>
                {/* Type badge */}
                <Badge variant="secondary" className="mb-3">
                    {template.report_type_label}
                </Badge>

                {/* Description of what this template does */}
                <p className="text-sm text-muted-foreground mb-4">
                    {template.report_type_description}
                </p>

                {/* Metadata if template was used before */}
                {template.run_count > 0 && (
                    <div className="flex items-center gap-2 text-sm text-muted-foreground mb-4">
                        <PlayCircle className="h-4 w-4" />
                        <span>
                            Usado{' '}
                            {template.run_count === 1
                                ? '1 vez'
                                : `${template.run_count} vezes`}
                        </span>
                    </div>
                )}

                {/* Use template button */}
                <Button
                    className="w-full"
                    variant="default"
                    onClick={handleUseTemplate}
                >
                    <Sparkles className="h-4 w-4 mr-2" />
                    Usar Template
                </Button>
            </CardContent>
        </Card>
    );
}
