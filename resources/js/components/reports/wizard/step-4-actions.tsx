import {
    ReportConfig,
    ReportTypeOption,
    VisualizationTypeOption,
} from '@/types/reports';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';

interface Step4ActionsProps {
    config: Partial<ReportConfig>;
    reportTypes: ReportTypeOption[];
    visualizationTypes: VisualizationTypeOption[];
    onGenerate: () => void;
    onSaveAndGenerate: (name: string, description?: string, isFavorite?: boolean) => void;
    onBack: () => void;
}

export function Step4Actions({
    config,
    reportTypes,
    visualizationTypes,
    onGenerate,
    onSaveAndGenerate,
    onBack,
}: Step4ActionsProps) {
    const [shouldSave, setShouldSave] = useState(false);
    const [saveName, setSaveName] = useState('');
    const [saveDescription, setSaveDescription] = useState('');
    const [isFavorite, setIsFavorite] = useState(false);

    // Get labels for display
    const reportTypeLabel =
        reportTypes.find((t) => t.value === config.report_type)?.label ||
        'N/A';
    const visualizationLabel =
        visualizationTypes.find(
            (v) => v.value === config.visualization_type
        )?.label || 'N/A';

    const handleGenerate = () => {
        if (shouldSave && saveName) {
            onSaveAndGenerate(saveName, saveDescription, isFavorite);
        } else {
            onGenerate();
        }
    };

    // Count active filters
    const activeFiltersCount = () => {
        let count = 0;
        if (config.filters?.category_ids?.length) count++;
        if (config.filters?.wallet_ids?.length) count++;
        if (config.filters?.status && config.filters.status !== 'all') count++;
        if (config.filters?.min_amount || config.filters?.max_amount) count++;
        if (config.filters?.limit) count++;
        return count;
    };

    return (
        <div className="space-y-8">
            <div className="space-y-3">
                <h2 className="text-2xl font-bold">Revisar e Gerar</h2>
                <p className="text-muted-foreground text-base">
                    Revise as configurações e gere seu relatório
                </p>
            </div>

            {/* Summary Card */}
            <Card>
                <CardHeader>
                    <CardTitle>Resumo da Configuração</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div>
                        <Label className="text-muted-foreground">
                            Tipo de Relatório
                        </Label>
                        <p className="text-lg font-medium">{reportTypeLabel}</p>
                    </div>

                    <Separator />

                    <div>
                        <Label className="text-muted-foreground">Período</Label>
                        <p className="font-medium">
                            {config.filters?.start_date &&
                            config.filters?.end_date
                                ? `${new Date(
                                      config.filters.start_date
                                  ).toLocaleDateString('pt-BR')} - ${new Date(
                                      config.filters.end_date
                                  ).toLocaleDateString('pt-BR')}`
                                : 'Não definido'}
                        </p>
                    </div>

                    {activeFiltersCount() > 0 && (
                        <>
                            <Separator />
                            <div>
                                <Label className="text-muted-foreground">
                                    Filtros Aplicados
                                </Label>
                                <div className="flex flex-wrap gap-2 mt-2">
                                    {config.filters?.category_ids?.length && (
                                        <Badge variant="secondary">
                                            {config.filters.category_ids.length}{' '}
                                            {config.filters.category_ids
                                                .length === 1
                                                ? 'Categoria'
                                                : 'Categorias'}
                                        </Badge>
                                    )}
                                    {config.filters?.wallet_ids?.length && (
                                        <Badge variant="secondary">
                                            {config.filters.wallet_ids.length}{' '}
                                            {config.filters.wallet_ids
                                                .length === 1
                                                ? 'Carteira'
                                                : 'Carteiras'}
                                        </Badge>
                                    )}
                                    {config.filters?.status &&
                                        config.filters.status !== 'all' && (
                                            <Badge variant="secondary">
                                                Status:{' '}
                                                {config.filters.status ===
                                                'paid'
                                                    ? 'Pagas'
                                                    : 'Pendentes'}
                                            </Badge>
                                        )}
                                    {(config.filters?.min_amount ||
                                        config.filters?.max_amount) && (
                                        <Badge variant="secondary">
                                            Faixa de Valor
                                        </Badge>
                                    )}
                                    {config.filters?.limit && (
                                        <Badge variant="secondary">
                                            Top {config.filters.limit}
                                        </Badge>
                                    )}
                                </div>
                            </div>
                        </>
                    )}

                    <Separator />

                    <div>
                        <Label className="text-muted-foreground">
                            Visualização
                        </Label>
                        <p className="font-medium">{visualizationLabel}</p>
                    </div>
                </CardContent>
            </Card>

            {/* Save Configuration Option */}
            <Card>
                <CardHeader>
                    <div className="flex items-center gap-3">
                        <Checkbox
                            id="shouldSave"
                            checked={shouldSave}
                            onCheckedChange={(checked: boolean) =>
                                setShouldSave(checked)
                            }
                        />
                        <Label htmlFor="shouldSave" className="cursor-pointer">
                            Salvar configuração para uso futuro
                        </Label>
                    </div>
                </CardHeader>

                {shouldSave && (
                    <CardContent className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="saveName">
                                Nome do Relatório *
                            </Label>
                            <Input
                                id="saveName"
                                placeholder="Ex: Despesas Mensais por Categoria"
                                value={saveName}
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                                    setSaveName(e.target.value)
                                }
                            />
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="saveDescription">
                                Descrição (opcional)
                            </Label>
                            <Textarea
                                id="saveDescription"
                                placeholder="Adicione uma descrição para este relatório..."
                                value={saveDescription}
                                onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) =>
                                    setSaveDescription(e.target.value)
                                }
                                rows={3}
                            />
                        </div>

                        <div className="flex items-center gap-3">
                            <Checkbox
                                id="isFavorite"
                                checked={isFavorite}
                                onCheckedChange={(checked: boolean) =>
                                    setIsFavorite(checked)
                                }
                            />
                            <Label
                                htmlFor="isFavorite"
                                className="cursor-pointer"
                            >
                                Marcar como favorito
                            </Label>
                        </div>
                    </CardContent>
                )}
            </Card>

            {/* Action Buttons */}
            <div className="flex justify-between pt-6">
                <Button variant="outline" onClick={onBack} size="lg" className="px-8">
                    Voltar
                </Button>
                <Button
                    onClick={handleGenerate}
                    disabled={shouldSave && !saveName}
                    size="lg"
                    className="px-8"
                >
                    {shouldSave ? 'Salvar e Gerar Relatório' : 'Gerar Relatório'}
                </Button>
            </div>
        </div>
    );
}
