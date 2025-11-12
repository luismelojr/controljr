import { ReportType, ReportTypeOption } from '@/types/reports';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import * as LucideIcons from 'lucide-react';

interface Step1ReportTypeProps {
    reportTypes: ReportTypeOption[];
    selected: ReportType | null;
    onSelect: (type: ReportType) => void;
    onNext: () => void;
}

export function Step1ReportType({
    reportTypes,
    selected,
    onSelect,
    onNext,
}: Step1ReportTypeProps) {
    // Helper to get the icon component from lucide-react
    const getIcon = (iconName: string) => {
        const Icon = (LucideIcons as any)[iconName];
        return Icon ? <Icon className="h-8 w-8" /> : null;
    };

    // Defensive check for reportTypes
    if (!reportTypes || reportTypes.length === 0) {
        return (
            <div className="space-y-6">
                <div>
                    <h2 className="text-2xl font-bold">Escolha o tipo de relatório</h2>
                    <p className="text-muted-foreground mt-1">
                        Nenhum tipo de relatório disponível no momento.
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-8">
            <div className="space-y-3">
                <h2 className="text-2xl font-bold">Escolha o tipo de relatório</h2>
                <p className="text-muted-foreground text-base">
                    Selecione qual tipo de análise você deseja realizar
                </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                {reportTypes.map((type) => (
                    <Card
                        key={type.value}
                        className={cn(
                            'cursor-pointer transition-all hover:shadow-md',
                            selected === type.value &&
                                'ring-2 ring-primary border-primary'
                        )}
                        onClick={() => onSelect(type.value)}
                    >
                        <CardHeader>
                            <div className="flex items-start gap-4">
                                <div className="text-primary mt-1">
                                    {getIcon(type.icon)}
                                </div>
                                <div className="flex-1">
                                    <CardTitle className="text-lg">
                                        {type.label}
                                    </CardTitle>
                                    <CardDescription className="mt-2">
                                        {type.description}
                                    </CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                    </Card>
                ))}
            </div>

            <div className="flex justify-end pt-4">
                <Button onClick={onNext} disabled={!selected} size="lg" className="px-8">
                    Próximo
                </Button>
            </div>
        </div>
    );
}
