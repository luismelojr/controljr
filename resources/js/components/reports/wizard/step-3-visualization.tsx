import { VisualizationType, VisualizationTypeOption } from '@/types/reports';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import * as LucideIcons from 'lucide-react';

interface Step3VisualizationProps {
    visualizationTypes: VisualizationTypeOption[];
    selected: VisualizationType | null;
    onChange: (key: string, value: any) => void;
    onNext: () => void;
    onBack: () => void;
}

export function Step3Visualization({
    visualizationTypes,
    selected,
    onChange,
    onNext,
    onBack,
}: Step3VisualizationProps) {
    // Helper to get the icon component from lucide-react
    const getIcon = (iconName: string) => {
        const Icon = (LucideIcons as any)[iconName];
        return Icon ? <Icon className="h-6 w-6" /> : null;
    };

    return (
        <div className="space-y-8">
            <div className="space-y-3">
                <h2 className="text-2xl font-bold">Escolha a visualização</h2>
                <p className="text-muted-foreground text-base">
                    Selecione como você quer visualizar os dados do relatório
                </p>
            </div>

            <div className="space-y-4">
                <h3 className="text-lg font-semibold">
                    Tipo de Visualização
                </h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    {visualizationTypes.map((type) => (
                        <Card
                            key={type.value}
                            className={cn(
                                'cursor-pointer transition-all hover:shadow-md',
                                selected === type.value &&
                                    'ring-2 ring-primary border-primary'
                            )}
                            onClick={() =>
                                onChange('visualization_type', type.value)
                            }
                        >
                            <CardHeader className="text-center">
                                <div className="flex justify-center text-primary mb-2">
                                    {getIcon(type.icon)}
                                </div>
                                <CardTitle className="text-base">
                                    {type.label}
                                </CardTitle>
                            </CardHeader>
                        </Card>
                    ))}
                </div>
            </div>

            <div className="flex justify-between pt-6">
                <Button variant="outline" onClick={onBack} size="lg" className="px-8">
                    Voltar
                </Button>
                <Button onClick={onNext} disabled={!selected} size="lg" className="px-8">
                    Próximo
                </Button>
            </div>
        </div>
    );
}
