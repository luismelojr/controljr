import AppHeader from '@/components/dashboard/app-header';
import FormCard from '@/components/dashboard/form-card';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { TagOption } from '@/components/tags/tag-input';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import TextAreaCustom from '@/components/ui/text-area-custom';
import TextInput from '@/components/ui/text-input';
import TextSelect from '@/components/ui/text-select';
import { Income, IncomeStatus } from '@/types/income';
import { Head, router, useForm } from '@inertiajs/react';
import { Info } from 'lucide-react';

interface EditIncomeProps {
    income: Income;
    tags: TagOption[];
}

export default function EditIncome({ income, tags }: EditIncomeProps) {
    const { data, setData, patch, processing, errors } = useForm({
        name: income.name,
        notes: income.notes || '',
        status: income.status,
        tags: income.tags?.map((t) => ({ name: t.name, color: t.color })) || [],
    });

    const statusOptions = [
        { value: 'active', label: 'Ativa' },
        { value: 'completed', label: 'Completa' },
        { value: 'cancelled', label: 'Cancelada' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(route('dashboard.incomes.update', { income: income.uuid }));
    };

    return (
        <DashboardLayout title="Editar Receita">
            <Head title="Editar Receita" />
            <div className="space-y-6">
                <AppHeader title="Editar receita" description="Atualize as informações da receita" routeBack={route('dashboard.incomes.index')} />

                <FormCard>
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Informações não editáveis */}
                        <Alert className="border-green-600/20 bg-green-50 dark:bg-green-950/20">
                            <Info className="h-4 w-4 text-green-600" />
                            <AlertDescription className="text-green-900 dark:text-green-100">
                                Você pode editar apenas o nome, observações e status da receita. Valores, parcelas e tipo de recorrência não podem ser
                                alterados após a criação.
                            </AlertDescription>
                        </Alert>

                        {/* Informações da receita (somente leitura) */}
                        <div className="grid gap-4 rounded-lg border p-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-muted-foreground">Categoria</p>
                                    <p className="font-medium">{income.category?.name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Tipo</p>
                                    <p className="font-medium">
                                        {income.recurrence_type_label}
                                        {income.installments && ` (${income.installments}x)`}
                                    </p>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-muted-foreground">Valor Total</p>
                                    <p className="font-medium text-green-600">
                                        {new Intl.NumberFormat('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL',
                                        }).format(income.total_amount)}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Data de Início</p>
                                    <p className="font-medium">{new Date(income.start_date).toLocaleDateString('pt-BR')}</p>
                                </div>
                            </div>
                        </div>

                        {/* Nome */}
                        <TextInput
                            label="Nome da Receita"
                            type="text"
                            id="name"
                            placeholder="Nome da receita"
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

                        <div className="space-y-2">
                            <label className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Tags</label>
                            <TagInput value={data.tags} onChange={(newTags) => setData('tags', newTags)} suggestions={tags} />
                        </div>

                        {/* Status */}
                        <TextSelect
                            label="Status"
                            id="status"
                            placeholder="Selecione o status"
                            options={statusOptions}
                            value={data.status}
                            onValueChange={(value) => setData('status', value as IncomeStatus)}
                            error={errors.status}
                        />

                        {/* Botões */}
                        <div className="flex justify-end gap-4">
                            <Button type="button" variant="outline" onClick={() => router.get(route('dashboard.incomes.index'))}>
                                Cancelar
                            </Button>
                            <Button type="submit" loading={processing}>
                                Atualizar Receita
                            </Button>
                        </div>
                    </form>
                </FormCard>
            </div>
        </DashboardLayout>
    );
}
