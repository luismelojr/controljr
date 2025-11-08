import { Button } from '@/components/ui/button';
import { CreditCard, Edit, MoreVertical, Trash2 } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

interface CreditCardWalletProps {
    wallet: {
        uuid: string;
        name: string;
        card_limit: number;
        card_limit_used?: number;
        day_close: number;
        best_shopping_day: number;
        status: boolean;
    };
    onEdit?: (uuid: string) => void;
    onDelete?: (uuid: string) => void;
}

export default function CreditCardWallet({ wallet, onEdit, onDelete }: CreditCardWalletProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const limitUsed = wallet.card_limit_used || 0;
    const limitAvailable = wallet.card_limit - limitUsed;
    const usagePercentage = wallet.card_limit > 0 ? (limitUsed / wallet.card_limit) * 100 : 0;

    // Determina a cor baseada na porcentagem de uso
    const getUsageColor = (percentage: number) => {
        if (percentage < 50) return 'bg-green-400';
        if (percentage < 80) return 'bg-yellow-400';
        return 'bg-red-400';
    };

    const getUsageTextColor = (percentage: number) => {
        if (percentage < 50) return 'text-green-400';
        if (percentage < 80) return 'text-yellow-400';
        return 'text-red-400';
    };

    return (
        <div className="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 p-6 text-white shadow-lg transition-all hover:shadow-xl">
            {/* Background Pattern */}
            <div className="absolute inset-0 opacity-10">
                <div className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white"></div>
                <div className="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white"></div>
            </div>

            {/* Content */}
            <div className="relative z-10 flex flex-col justify-between space-y-4">
                {/* Header */}
                <div className="flex items-start justify-between">
                    <div className="flex items-center gap-2">
                        <div className="rounded-lg bg-white/20 p-2 backdrop-blur-sm">
                            <CreditCard className="h-5 w-5" />
                        </div>
                        <div>
                            <p className="text-xs font-medium text-white/80">Cartão de Crédito</p>
                            <h3 className="text-lg font-bold">{wallet.name}</h3>
                        </div>
                    </div>

                    {/* Menu de Ações */}
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button
                                variant="ghost"
                                size="icon"
                                className="h-8 w-8 text-white hover:bg-white/20 hover:text-white"
                            >
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

                {/* Chip simulado */}
                <div className="flex items-center gap-2">
                    <div className="h-10 w-14 rounded-md bg-gradient-to-br from-yellow-200 to-yellow-400 shadow-sm"></div>
                </div>

                {/* Informações do Cartão */}
                <div className="space-y-4">
                    {/* Barra de Uso do Limite */}
                    <div className="space-y-2">
                        <div className="flex items-center justify-between text-sm">
                            <span className="text-xs font-medium text-white/70">Limite Usado</span>
                            <span className={`text-sm font-bold ${getUsageTextColor(usagePercentage)}`}>
                                {usagePercentage.toFixed(1)}%
                            </span>
                        </div>
                        <div className="h-2.5 w-full overflow-hidden rounded-full bg-white/20 backdrop-blur-sm">
                            <div
                                className={`h-full transition-all duration-500 ${getUsageColor(usagePercentage)}`}
                                style={{ width: `${Math.min(usagePercentage, 100)}%` }}
                            />
                        </div>
                        <div className="grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <p className="text-white/60">Usado</p>
                                <p className="font-semibold">{formatCurrency(limitUsed)}</p>
                            </div>
                            <div>
                                <p className="text-white/60">Disponível</p>
                                <p className="font-semibold">{formatCurrency(limitAvailable)}</p>
                            </div>
                            <div>
                                <p className="text-white/60">Total</p>
                                <p className="font-semibold">{formatCurrency(wallet.card_limit)}</p>
                            </div>
                        </div>
                    </div>

                    {/* Dias de Fechamento */}
                    <div className="grid grid-cols-2 gap-4 border-t border-white/20 pt-3 text-sm">
                        <div>
                            <p className="text-xs font-medium text-white/70">Dia de Fechamento</p>
                            <p className="font-semibold">Dia {wallet.day_close}</p>
                        </div>
                        <div>
                            <p className="text-xs font-medium text-white/70">Melhor Dia de Compra</p>
                            <p className="font-semibold">Dia {wallet.best_shopping_day}</p>
                        </div>
                    </div>
                </div>

                {/* Status Badge */}
                {!wallet.status && (
                    <div className="absolute right-4 top-4">
                        <span className="rounded-full bg-red-500 px-2 py-1 text-xs font-semibold">Inativo</span>
                    </div>
                )}
            </div>
        </div>
    );
}
