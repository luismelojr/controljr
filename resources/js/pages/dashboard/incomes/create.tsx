import AppHeader from '@/components/dashboard/app-header';
import FormCard from '@/components/dashboard/form-card';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import TextAreaCustom from '@/components/ui/text-area-custom';
import TextInput from '@/components/ui/text-input';
import TextMoney from '@/components/ui/text-money';
import TextSelect from '@/components/ui/text-select';
import { IncomeFormData } from '@/types/income';
import { Category } from '@/types/category';
import { Head, router, useForm } from '@inertiajs/react';
import { Info } from 'lucide-react';
import { useMemo } from 'react';

interface CreateIncomeProps {
    categories: Category[];
}

export default function CreateIncome({ categories }: CreateIncomeProps) {
    const { data, setData, post, processing, errors } = useForm<IncomeFormData>({
        category_id: '',
        name: '',
        notes: '',
        total_amount: '',
        recurrence_type: '',
        installments: '',
        start_date: new Date().toISOString().split('T')[0],
    });

    // Opções de categorias
    const categoryOptions = useMemo(() => {
        return categories.map((category) => ({
            value: category.uuid,
            label: category.name,
        }));
    }, [categories]);

    // Opções de tipo de recorrência
    const recurrenceOptions = [
        { value: 'one_time', label: 'Única' },
        { value: 'installments', label: 'Parcelada' },
        { value: 'recurring', label: 'Recorrente' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('dashboard.incomes.store'));
    };

    // Verifica se deve mostrar o campo de parcelas
    const showInstallments = data.recurrence_type === 'installments';

    return (
        <DashboardLayout title="Nova Receita">
            <Head title="Nova Receita" />
            <div className="space-y-6">
                <AppHeader
                    title="Nova receita"
                    description="Cadastre uma nova fonte de renda"
                    routeBack={route('dashboard.incomes.index')}
                />

                <FormCard>
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Nome */}
                        <TextInput
                            label="Nome da Receita"
                            type="text"
                            id="name"
                            placeholder="Ex: Salário, Freelance, Comissão"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                            required
                        />

                        {/* Observações */}
                        <TextAreaCustom
                            label="Observações (opcional)"
                            id="notes"
                            placeholder="Adicione detalhes sobre esta receita..."
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                            error={errors.notes}
                            rows={3}
                        />

                        {/* Categoria */}
                        <TextSelect
                            label="Categoria"
                            id="category_id"
                            placeholder="Selecione a categoria"
                            options={categoryOptions}
                            value={data.category_id}
                            onValueChange={(value) => setData('category_id', value)}
                            error={errors.category_id}
                            required
                        />

                        {/* Tipo de Recorrência */}
                        <TextSelect
                            label="Tipo de Recorrência"
                            id="recurrence_type"
                            placeholder="Selecione o tipo"
                            options={recurrenceOptions}
                            value={data.recurrence_type}
                            onValueChange={(value) => {
                                setData('recurrence_type', value as any);
                                if (value !== 'installments') {
                                    setData('installments', '');
                                }
                            }}
                            error={errors.recurrence_type}
                            required
                        />

                        {/* Valor Total */}
                        <TextMoney
                            label="Valor Total"
                            id="total_amount"
                            placeholder="0,00"
                            value={data.total_amount}
                            onChange={(value) => setData('total_amount', value)}
                            error={errors.total_amount}
                            required
                        />

                        {/* Parcelas (condicional) */}
                        {showInstallments && (
                            <TextInput
                                label="Número de Parcelas"
                                type="number"
                                id="installments"
                                placeholder="Ex: 10"
                                value={data.installments.toString()}
                                onChange={(e) => setData('installments', e.target.value as any)}
                                error={errors.installments}
                                required
                                min="2"
                                max="120"
                            />
                        )}

                        {/* Data de Início */}
                        <TextInput
                            label="Data de Início"
                            type="date"
                            id="start_date"
                            value={data.start_date}
                            onChange={(e) => setData('start_date', e.target.value)}
                            error={errors.start_date}
                            required
                        />

                        {/* Info sobre geração automática */}
                        <Alert className="border-green-600/20 bg-green-50 dark:bg-green-950/20">
                            <Info className="h-4 w-4 text-green-600" />
                            <AlertDescription className="text-green-900 dark:text-green-100">
                                As transações de recebimento serão geradas automaticamente com base no tipo de recorrência selecionado.
                                Para receitas recorrentes, sempre manteremos 12 meses futuros.
                            </AlertDescription>
                        </Alert>

                        {/* Botões */}
                        <div className="flex justify-end gap-4">
                            <Button type="button" variant="outline" onClick={() => router.get(route('dashboard.incomes.index'))}>
                                Cancelar
                            </Button>
                            <Button type="submit" loading={processing}>
                                Salvar Receita
                            </Button>
                        </div>
                    </form>
                </FormCard>
            </div>
        </DashboardLayout>
    );
}
