import AppHeader from '@/components/dashboard/app-header';
import FormCard from '@/components/dashboard/form-card';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { TagOption } from '@/components/tags/tag-input';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import TextAreaCustom from '@/components/ui/text-area-custom';
import TextInput from '@/components/ui/text-input';
import TextSelect from '@/components/ui/text-select';
import { Account, AccountStatus } from '@/types/account';
import { Head, router, useForm } from '@inertiajs/react';
import { Info } from 'lucide-react';

interface EditAccountProps {
    account: Account;
    tags: TagOption[];
}

export default function EditAccount({ account, tags }: EditAccountProps) {
    const { data, setData, patch, processing, errors } = useForm({
        name: account.name,
        description: account.description || '',
        status: account.status,
        tags: account.tags?.map((t) => ({ name: t.name, color: t.color })) || [],
    });

    const statusOptions = [
        { value: 'active', label: 'Ativa' },
        { value: 'completed', label: 'Completa' },
        { value: 'cancelled', label: 'Cancelada' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(route('dashboard.accounts.update', { account: account.uuid }));
    };

    return (
        <DashboardLayout title="Editar Conta">
            <Head title="Editar Conta" />
            <div className="space-y-6">
                <AppHeader title="Editar conta" description="Atualize as informações da conta" routeBack={route('dashboard.accounts.index')} />

                <FormCard>
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Informações não editáveis */}
                        <Alert>
                            <Info className="h-4 w-4" />
                            <AlertDescription>
                                Você pode editar apenas o nome, descrição e status da conta. Valores, parcelas e tipo de recorrência não podem ser
                                alterados após a criação.
                            </AlertDescription>
                        </Alert>

                        {/* Informações da conta (somente leitura) */}
                        <div className="grid gap-4 rounded-lg border p-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-muted-foreground">Carteira</p>
                                    <p className="font-medium">{account.wallet?.name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Categoria</p>
                                    <p className="font-medium">{account.category?.name}</p>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-muted-foreground">Tipo</p>
                                    <p className="font-medium">
                                        {account.recurrence_type_label}
                                        {account.installments && ` (${account.installments}x)`}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-sm text-muted-foreground">Valor Total</p>
                                    <p className="font-medium">
                                        {new Intl.NumberFormat('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL',
                                        }).format(account.total_amount)}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Nome */}
                        <TextInput
                            label="Nome da Conta"
                            type="text"
                            id="name"
                            placeholder="Nome da conta"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                            required
                        />

                        {/* Descrição */}
                        <TextAreaCustom
                            label="Descrição (opcional)"
                            id="description"
                            placeholder="Adicione detalhes sobre esta conta..."
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            error={errors.description}
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
                            onValueChange={(value) => setData('status', value as AccountStatus)}
                            error={errors.status}
                        />

                        {/* Botões */}
                        <div className="flex justify-end gap-4">
                            <Button type="button" variant="outline" onClick={() => router.get(route('dashboard.accounts.index'))}>
                                Cancelar
                            </Button>
                            <Button type="submit" loading={processing}>
                                Atualizar Conta
                            </Button>
                        </div>
                    </form>
                </FormCard>
            </div>
        </DashboardLayout>
    );
}
