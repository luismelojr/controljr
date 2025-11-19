import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import TextInput from '@/components/ui/text-input';
import TextMoney from '@/components/ui/text-money';
import TextSelect from '@/components/ui/text-select';
import { WalletInterface, WalletType } from '@/types/wallet';
import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

interface EditWalletProps {
    wallet: WalletInterface;
}

const walletTypeOptions = [
    { value: 'card_credit', label: 'Cartão de Crédito' },
    { value: 'bank_account', label: 'Conta Bancária' },
    { value: 'other', label: 'Outros' },
];

export default function EditWallet({ wallet }: EditWalletProps) {
    const { data, setData, put, processing, errors } = useForm({
        name: wallet.name,
        type: wallet.type,
        day_close: wallet.day_close?.toString() || '',
        best_shopping_day: wallet.best_shopping_day?.toString() || '',
        card_limit: wallet.card_limit || 0,
        initial_balance: wallet.initial_balance || 0,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('dashboard.wallets.update', { wallet: wallet.uuid }));
    };

    const isCreditCard = data.type === 'card_credit';
    const isBankAccountOrOther = data.type === 'bank_account' || data.type === 'other';

    return (
        <DashboardLayout title="Editar Carteira">
            <Head title="Editar Carteira" />
            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Editar Carteira</h1>
                        <p className="text-muted-foreground">Atualize as informações da sua carteira</p>
                    </div>
                    <Button variant="outline" onClick={() => router.get(route('dashboard.wallets.index'))}>
                        <ArrowLeft className="mr-2 h-4 w-4" />
                        Voltar
                    </Button>
                </div>

                {/* Formulário */}
                <div className="rounded-lg border bg-card p-6">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Informações Básicas */}
                        <div className="space-y-4">
                            <h2 className="text-lg font-semibold">Informações Básicas</h2>
                            <div className="grid gap-4 md:grid-cols-2">
                                <TextInput
                                    label="Nome da Carteira"
                                    id="name"
                                    name="name"
                                    type="text"
                                    placeholder="Ex: Nubank, Itaú, Carteira Principal"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    error={errors.name}
                                    required
                                />

                                <TextSelect
                                    label="Tipo"
                                    id="type"
                                    placeholder="Selecione o tipo"
                                    options={walletTypeOptions}
                                    value={data.type}
                                    onValueChange={(value) => setData('type', value as WalletType)}
                                    error={errors.type}
                                    required
                                />
                            </div>
                        </div>

                        {/* Campos específicos para Cartão de Crédito */}
                        {isCreditCard && (
                            <div className="space-y-4 rounded-lg border border-primary/20 bg-primary/5 p-4">
                                <h2 className="text-lg font-semibold">Informações do Cartão de Crédito</h2>
                                <div className="grid gap-4 md:grid-cols-3">
                                    <TextInput
                                        label="Dia de Fechamento"
                                        id="day_close"
                                        name="day_close"
                                        type="number"
                                        placeholder="Ex: 10"
                                        value={data.day_close}
                                        onChange={(e) => setData('day_close', e.target.value)}
                                        error={errors.day_close}
                                        min="1"
                                        max="31"
                                        required={isCreditCard}
                                    />

                                    <TextInput
                                        label="Melhor Dia de Compra"
                                        id="best_shopping_day"
                                        name="best_shopping_day"
                                        type="number"
                                        placeholder="Ex: 11"
                                        value={data.best_shopping_day}
                                        onChange={(e) => setData('best_shopping_day', e.target.value)}
                                        error={errors.best_shopping_day}
                                        min="1"
                                        max="31"
                                        required={isCreditCard}
                                    />

                                    <TextMoney
                                        label="Limite Total"
                                        id="card_limit"
                                        name="card_limit"
                                        placeholder="R$ 0,00"
                                        value={data.card_limit}
                                        onChange={(value) => setData('card_limit', value)}
                                        error={errors.card_limit}
                                        required={isCreditCard}
                                        helperText="Limite total do cartão"
                                    />
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    O dia de fechamento é quando a fatura fecha. O melhor dia de compra é um dia após o fechamento para aproveitar o
                                    ciclo completo.
                                </p>
                            </div>
                        )}

                        {/* Campo de Saldo Inicial para Conta Bancária e Outros */}
                        {isBankAccountOrOther && (
                            <div className="space-y-4">
                                <h2 className="text-lg font-semibold">Saldo Inicial</h2>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <TextMoney
                                        label="Saldo Inicial"
                                        id="initial_balance"
                                        name="initial_balance"
                                        placeholder="R$ 0,00"
                                        value={data.initial_balance}
                                        onChange={(value) => setData('initial_balance', value)}
                                        error={errors.initial_balance}
                                        helperText="Informe o saldo atual desta conta"
                                    />
                                    <div className="flex items-center text-sm text-muted-foreground">
                                        Informe o saldo atual desta conta
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Botões de Ação */}
                        <div className="flex justify-end gap-4">
                            <Button type="button" variant="outline" onClick={() => router.get(route('dashboard.wallets.index'))}>
                                Cancelar
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Salvando...' : 'Atualizar Carteira'}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
