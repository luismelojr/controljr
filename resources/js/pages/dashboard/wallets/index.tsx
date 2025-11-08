import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import BankAccountWallet from '@/components/wallets/bank-account-wallet';
import CreditCardWallet from '@/components/wallets/credit-card-wallet';
import OtherWallet from '@/components/wallets/other-wallet';
import { WalletInterface } from '@/types/wallet';
import { Head, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';

interface WalletsProps {
    wallets: WalletInterface[];
}

export default function Wallets({ wallets = [] }: WalletsProps) {
    const [deleteDialog, setDeleteDialog] = useState<{ open: boolean; walletUuid: string | null; walletName: string }>({
        open: false,
        walletUuid: null,
        walletName: '',
    });

    const handleEdit = (uuid: string) => {
        router.get(route('dashboard.wallets.edit', { wallet: uuid }));
    };

    const handleDelete = (uuid: string) => {
        const wallet = wallets.find((w) => w.uuid === uuid);
        setDeleteDialog({
            open: true,
            walletUuid: uuid,
            walletName: wallet?.name || '',
        });
    };

    const confirmDelete = () => {
        if (deleteDialog.walletUuid) {
            router.delete(route('dashboard.wallets.destroy', { wallet: deleteDialog.walletUuid }));
        }
    };

    const renderWallet = (wallet: WalletInterface) => {
        switch (wallet.type) {
            case 'card_credit':
                return (
                    <CreditCardWallet
                        key={wallet.uuid}
                        wallet={{
                            uuid: wallet.uuid,
                            name: wallet.name,
                            card_limit: wallet.card_limit || 0,
                            card_limit_used: wallet.card_limit_used || 0,
                            day_close: wallet.day_close || 0,
                            best_shopping_day: wallet.best_shopping_day || 0,
                            status: wallet.status,
                        }}
                        onEdit={handleEdit}
                        onDelete={handleDelete}
                    />
                );
            case 'bank_account':
                return <BankAccountWallet key={wallet.uuid} wallet={wallet} onEdit={handleEdit} onDelete={handleDelete} />;
            case 'other':
                return <OtherWallet key={wallet.uuid} wallet={wallet} onEdit={handleEdit} onDelete={handleDelete} />;
            default:
                return null;
        }
    };

    return (
        <DashboardLayout title="Carteiras">
            <Head title="Carteiras" />
            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold">Carteiras</h1>
                        <p className="text-muted-foreground">Gerencie suas carteiras e contas</p>
                    </div>
                    <Button onClick={() => router.get(route('dashboard.wallets.create'))}>
                        <Plus className="mr-2 h-4 w-4" />
                        Nova Carteira
                    </Button>
                </div>

                {/* Lista de Carteiras */}
                {wallets.length === 0 ? (
                    <div className="rounded-lg border bg-card p-12 text-center">
                        <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                            <Plus className="h-10 w-10 text-muted-foreground" />
                        </div>
                        <h2 className="mt-4 text-xl font-semibold">Nenhuma carteira cadastrada</h2>
                        <p className="mt-2 text-muted-foreground">Comece criando sua primeira carteira para gerenciar suas finanças.</p>
                        <Button onClick={() => router.get(route('dashboard.wallets.create'))} className="mt-6">
                            <Plus className="mr-2 h-4 w-4" />
                            Criar Primeira Carteira
                        </Button>
                    </div>
                ) : (
                    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{wallets.map(renderWallet)}</div>
                )}
            </div>

            <ConfirmDeleteDialog
                open={deleteDialog.open}
                onOpenChange={(open) => setDeleteDialog({ ...deleteDialog, open })}
                onConfirm={confirmDelete}
                title="Excluir carteira?"
                description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente a carteira"
                itemName={deleteDialog.walletName}
                confirmText="Excluir"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
