import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Step1ReportType } from '@/components/reports/wizard/step-1-report-type';
import { Step2Filters } from '@/components/reports/wizard/step-2-filters';
import { Step3Visualization } from '@/components/reports/wizard/step-3-visualization';
import { Step4Actions } from '@/components/reports/wizard/step-4-actions';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { ReportBuilderProps, ReportConfig, ReportFilters } from '@/types/reports';
import { Head, router } from '@inertiajs/react';
import { Check } from 'lucide-react';
import { useState } from 'react';

export default function ReportBuilder({ categories, wallets, report_types, visualization_types }: ReportBuilderProps) {
    const [currentStep, setCurrentStep] = useState(1);
    const [config, setConfig] = useState<Partial<ReportConfig>>({
        filters: {},
    });

    /**
     * Update a specific config key
     */
    const updateConfig = (key: string, value: any) => {
        setConfig((prev) => ({ ...prev, [key]: value }));
    };

    /**
     * Update filters
     */
    const updateFilters = (filters: ReportFilters) => {
        setConfig((prev) => ({ ...prev, filters }));
    };

    /**
     * Generate report (preview without saving)
     */
    const handleGenerate = () => {
        if (!config.report_type || !config.visualization_type) return;
        console.log('aqui', {
            report_type: config.report_type,
            visualization_type: config.visualization_type,
            ...config.filters,
        });
        router.post(route('dashboard.reports.generate'), {
            report_type: config.report_type,
            visualization_type: config.visualization_type,
            ...config.filters,
        });
    };

    /**
     * Save configuration and generate report
     */
    const handleSaveAndGenerate = (name: string, description?: string, isFavorite?: boolean) => {
        if (!config.report_type || !config.visualization_type) return;

        router.post(route('dashboard.reports.store'), {
            name,
            description,
            is_favorite: isFavorite,
            report_type: config.report_type,
            visualization_type: config.visualization_type,
            ...config.filters,
        });
    };

    return (
        <DashboardLayout title="Criar Relatório">
            <Head title="Criar Relatório" />

            <div className="space-y-10 py-6">
                {/* Header */}
                <div className="space-y-2">
                    <h1 className="text-3xl font-bold">Criar Novo Relatório</h1>
                    <p className="text-lg text-muted-foreground">Configure seu relatório personalizado em 4 etapas simples</p>
                </div>

                {/* Stepper */}
                <div className="flex items-center justify-between px-4">
                    {[1, 2, 3, 4].map((step) => (
                        <div key={step} className="flex flex-1 items-center">
                            <div className="flex items-center gap-4">
                                <div
                                    className={cn(
                                        'flex h-12 w-12 items-center justify-center rounded-full border-2 font-semibold transition-all',
                                        currentStep === step && 'scale-110 border-primary bg-primary text-primary-foreground shadow-lg',
                                        currentStep > step && 'border-primary bg-primary text-primary-foreground',
                                        currentStep < step && 'border-muted-foreground/25 bg-muted text-muted-foreground',
                                    )}
                                >
                                    {currentStep > step ? <Check className="h-5 w-5" /> : step}
                                </div>
                                <div className="hidden md:block">
                                    <p className={cn('text-sm font-semibold', currentStep >= step ? 'text-foreground' : 'text-muted-foreground')}>
                                        Etapa {step}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {step === 1 && 'Tipo'}
                                        {step === 2 && 'Filtros'}
                                        {step === 3 && 'Visualização'}
                                        {step === 4 && 'Gerar'}
                                    </p>
                                </div>
                            </div>
                            {step < 4 && <div className={cn('mx-6 h-0.5 flex-1', currentStep > step ? 'bg-primary' : 'bg-muted-foreground/25')} />}
                        </div>
                    ))}
                </div>

                {/* Step Content */}
                <Card className="border-2">
                    <CardContent className="p-8 md:p-10">
                        {currentStep === 1 && (
                            <Step1ReportType
                                reportTypes={report_types}
                                selected={config.report_type || null}
                                onSelect={(type) => updateConfig('report_type', type)}
                                onNext={() => setCurrentStep(2)}
                            />
                        )}

                        {currentStep === 2 && (
                            <Step2Filters
                                reportType={config.report_type || null}
                                categories={categories}
                                wallets={wallets}
                                filters={config.filters || {}}
                                onChange={updateFilters}
                                onNext={() => setCurrentStep(3)}
                                onBack={() => setCurrentStep(1)}
                            />
                        )}

                        {currentStep === 3 && (
                            <Step3Visualization
                                visualizationTypes={visualization_types}
                                selected={config.visualization_type || null}
                                onChange={updateConfig}
                                onNext={() => setCurrentStep(4)}
                                onBack={() => setCurrentStep(2)}
                            />
                        )}

                        {currentStep === 4 && (
                            <>
                                <div>aaa</div>
                                <Step4Actions
                                    config={config}
                                    reportTypes={report_types}
                                    visualizationTypes={visualization_types}
                                    onGenerate={handleGenerate}
                                    onSaveAndGenerate={handleSaveAndGenerate}
                                    onBack={() => setCurrentStep(3)}
                                />
                            </>
                        )}
                    </CardContent>
                </Card>
            </div>
        </DashboardLayout>
    );
}
