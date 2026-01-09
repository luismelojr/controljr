import AppHeader from '@/components/dashboard/app-header';
import FormCard from '@/components/dashboard/form-card';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import TextAreaCustom from '@/components/ui/text-area-custom';
import TextInput from '@/components/ui/text-input';
import TextMoney from '@/components/ui/text-money';
import TextSelect from '@/components/ui/text-select';
import { AccountFormData } from '@/types/account';
import { Category } from '@/types/category';
import { WalletInterface } from '@/types/wallet';
import { Head, router, useForm } from '@inertiajs/react';
import { Info } from 'lucide-react';
import { useMemo } from 'react';

import { TagInput, TagOption } from '@/components/tags/tag-input';

interface CreateAccountProps {
    wallets: WalletInterface[];
    categories: Category[];
    tags: TagOption[];
}

export default function CreateAccount({ wallets, categories, tags }: CreateAccountProps) {
    const { data, setData, post, processing, errors } = useForm<AccountFormData & { tags: any[] }>({
        wallet_id: '',
        category_id: '',
        name: '',
        description: '',
        total_amount: '',
        recurrence_type: '',
        installments: '',
        paid_installments: '0',
        start_date: new Date().toISOString().split('T')[0],
        tags: [],
    });

    // Wallet selecionado
    const selectedWallet = useMemo(() => {
        if (!data.wallet_id) return null;
        return wallets.find((w) => w.uuid === data.wallet_id);
    }, [data.wallet_id, wallets]);

    // Opções de carteiras
    const walletOptions = useMemo(() => {
        return wallets.map((wallet) => ({
            value: wallet.uuid,
            label: `${wallet.name} - ${wallet.type_label}`,
        }));
    }, [wallets]);

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
        post(route('dashboard.accounts.store'));
    };

    // Verifica se deve mostrar o campo de parcelas
    const showInstallments = data.recurrence_type === 'installments';

    // Verifica limite do cartão
    const limitAlert = useMemo(() => {
        if (!selectedWallet || !selectedWallet.is_credit_card || !data.total_amount) return null;

        const amount = typeof data.total_amount === 'string' ? parseFloat(data.total_amount) : data.total_amount;
        const available = selectedWallet.card_limit_available || 0;

        if (amount > available) {
            return {
                type: 'error',
                message: `O valor excede o limite disponível do cartão de R$ ${available.toFixed(2).replace('.', ',')}`,
            };
        }

        if (amount > available * 0.8) {
            return {
                type: 'warning',
                message: `Atenção: Este valor utilizará ${Math.round((amount / (selectedWallet.card_limit || 1)) * 100)}% do limite total do cartão`,
            };
        }

        return null;
    }, [selectedWallet, data.total_amount]);

    return (
        <DashboardLayout title="Nova Conta">
            <Head title="Nova Conta" />
            <div className="space-y-6">
                <AppHeader title="Nova conta" description="Cadastre um novo compromisso financeiro" routeBack={route('dashboard.accounts.index')} />

                <FormCard>
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Nome */}
                        <TextInput
                            label="Nome da Conta"
                            type="text"
                            id="name"
                            placeholder="Ex: iPhone 15 Pro, Netflix, Conta de Luz"
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

                        {/* Carteira */}
                        <TextSelect
                            label="Carteira"
                            id="wallet_id"
                            placeholder="Selecione a carteira"
                            options={walletOptions}
                            value={data.wallet_id}
                            onValueChange={(value) => setData('wallet_id', value)}
                            error={errors.wallet_id}
                            required
                        />

                        {/* Alerta de limite do cartão */}
                        {limitAlert && (
                            <Alert variant={limitAlert.type === 'error' ? 'destructive' : 'default'}>
                                <Info className="h-4 w-4" />
                                <AlertDescription>{limitAlert.message}</AlertDescription>
                            </Alert>
                        )}

                        <TextSelect
                            id="category_id"
                            label="Categoria"
                            placeholder="Selecione a categoria"
                            options={categoryOptions}
                            value={data.category_id}
                            onValueChange={(value) => setData('category_id', value)}
                            error={errors.category_id}
                            required
                        />

                        {/* Tags */}
                        <div className="space-y-2">
                            <label className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Tags</label>
                            <TagInput value={data.tags} onChange={(newTags) => setData('tags', newTags)} suggestions={tags} />
                        </div>

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
                            name="total_amount"
                            placeholder="R$ 0,00"
                            value={data.total_amount}
                            onChange={(value) => setData('total_amount', value)}
                            error={errors.total_amount}
                            required
                            helperText="Digite o valor total da conta"
                        />

                        {/* Parcelas (condicional) */}
                        {showInstallments && (
                            <>
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

                                <TextInput
                                    label="Parcelas Já Pagas"
                                    type="number"
                                    id="paid_installments"
                                    placeholder="0"
                                    value={data.paid_installments.toString()}
                                    onChange={(e) => setData('paid_installments', e.target.value as any)}
                                    error={errors.paid_installments}
                                    min="0"
                                    max={data.installments ? (parseInt(data.installments.toString()) - 1).toString() : '0'}
                                    helperText="Quantas parcelas já foram pagas? Apenas as parcelas restantes serão lançadas."
                                />
                            </>
                        )}

                        {/* Data de Início */}
                        <TextInput
                            label={
                                showInstallments && data.paid_installments && parseInt(data.paid_installments.toString()) > 0
                                    ? 'Data de Vencimento da Próxima Parcela'
                                    : 'Data de Início'
                            }
                            type="date"
                            id="start_date"
                            value={data.start_date}
                            onChange={(e) => setData('start_date', e.target.value)}
                            error={errors.start_date}
                            required
                            helperText={
                                showInstallments && data.paid_installments && parseInt(data.paid_installments.toString()) > 0
                                    ? 'Data em que a próxima parcela vence. As demais parcelas vencerão nos meses seguintes.'
                                    : undefined
                            }
                        />

                        {/* Info sobre geração automática */}
                        <Alert>
                            <Info className="h-4 w-4" />
                            <AlertDescription>
                                As transações serão geradas automaticamente com base no tipo de recorrência selecionado.
                                {showInstallments && data.paid_installments && parseInt(data.paid_installments.toString()) > 0 && (
                                    <>
                                        {' '}
                                        Você informou que {data.paid_installments} parcela(s) já foi(ram) paga(s). As{' '}
                                        {data.installments ? parseInt(data.installments.toString()) - parseInt(data.paid_installments.toString()) : 0}{' '}
                                        parcela(s) restante(s) serão lançadas a partir da data informada, vencendo mês a mês.
                                    </>
                                )}
                                {!showInstallments && <> Para contas recorrentes, sempre manteremos 12 meses futuros.</>}
                            </AlertDescription>
                        </Alert>

                        {/* Botões */}
                        <div className="flex justify-end gap-4">
                            <Button type="button" variant="outline" onClick={() => router.get(route('dashboard.accounts.index'))}>
                                Cancelar
                            </Button>
                            <Button type="submit" loading={processing} disabled={limitAlert?.type === 'error'}>
                                Salvar Conta
                            </Button>
                        </div>
                    </form>
                </FormCard>
            </div>
        </DashboardLayout>
    );
}
