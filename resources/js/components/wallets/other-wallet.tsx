import { Button } from '@/components/ui/button';
import { Edit, MoreVertical, Trash2, Wallet } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

interface OtherWalletProps {
    wallet: {
        uuid: string;
        name: string;
        status: boolean;
        initial_balance: number;
        balance_incomes?: number;
        balance_expenses?: number;
        balance_available?: number;
    };
    onEdit?: (uuid: string) => void;
    onDelete?: (uuid: string) => void;
}

export default function OtherWallet({ wallet, onEdit, onDelete }: OtherWalletProps) {
    return (
        <div className="group relative overflow-hidden rounded-xl border bg-gradient-to-br from-purple-50 to-pink-50 p-6 shadow-sm transition-all hover:shadow-md dark:from-purple-950/20 dark:to-pink-950/20">
            {/* Background Pattern */}
            <div className="absolute inset-0 opacity-5">
                <div className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-purple-500"></div>
                <div className="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-pink-500"></div>
            </div>

            {/* Content */}
            <div className="relative z-10 flex flex-col justify-between space-y-4">
                {/* Header */}
                <div className="flex items-start justify-between">
                    <div className="flex items-center gap-3">
                        <div className="rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 p-2.5 text-white shadow-sm">
                            <Wallet className="h-5 w-5" />
                        </div>
                        <div>
                            <p className="text-xs font-medium text-muted-foreground">Outros</p>
                            <h3 className="text-lg font-bold text-foreground">{wallet.name}</h3>
                        </div>
                    </div>

                    {/* Menu de Ações */}
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon" className="h-8 w-8">
                                <MoreVertical className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem onClick={() => onEdit?.(wallet.uuid)}>
                                <Edit className="mr-2 h-4 w-4" />
                                Editar
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                onClick={() => onDelete?.(wallet.uuid)}
                                className="text-destructive focus:text-destructive"
                            >
                                <Trash2 className="mr-2 h-4 w-4" />
                                Excluir
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>

                {/* Informações da Carteira */}
                <div className="space-y-3">
                    <div className="rounded-lg border border-purple-200 bg-white/50 p-4 backdrop-blur-sm dark:border-purple-800 dark:bg-purple-950/20">
                        <p className="text-xs font-medium text-muted-foreground">Saldo Disponível</p>
                        <p className="mt-1 text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {new Intl.NumberFormat('pt-BR', {
                                style: 'currency',
                                currency: 'BRL',
                            }).format(wallet.balance_available ?? 0)}
                        </p>
                    </div>

                    <div className="grid grid-cols-2 gap-3 text-sm">
                        <div className="rounded-lg border bg-white/50 p-3 backdrop-blur-sm dark:bg-background/50">
                            <p className="text-xs font-medium text-muted-foreground">Receitas</p>
                            <p className="mt-1 font-semibold text-green-600">
                                + {new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL',
                                }).format(wallet.balance_incomes ?? 0)}
                            </p>
                        </div>
                        <div className="rounded-lg border bg-white/50 p-3 backdrop-blur-sm dark:bg-background/50">
                            <p className="text-xs font-medium text-muted-foreground">Despesas</p>
                            <p className="mt-1 font-semibold text-red-600">
                                - {new Intl.NumberFormat('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL',
                                }).format(wallet.balance_expenses ?? 0)}
                            </p>
                        </div>
                    </div>
                </div>

                {/* Status Badge */}
                {!wallet.status && (
                    <div className="absolute right-4 top-4">
                        <span className="rounded-full bg-red-500 px-2 py-1 text-xs font-semibold text-white">
                            Inativo
                        </span>
                    </div>
                )}
            </div>
        </div>
    );
}
