import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CreditCard } from 'lucide-react';
import { router } from '@inertiajs/react';

interface WalletSummary {
    id: string;
    name: string;
    type: string;
    balance: number;
    card_limit?: number;
    card_limit_used?: number;
    usage_percentage?: number;
}

interface WalletsSummaryListProps {
    wallets: WalletSummary[];
    formatCurrency: (value: number) => string;
}

export function WalletsSummaryList({ wallets, formatCurrency }: WalletsSummaryListProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>Minhas Carteiras</CardTitle>
            </CardHeader>
            <CardContent>
                {wallets.length > 0 ? (
                    <div className="space-y-4">
                        {wallets.map((wallet) => (
                            <div key={wallet.id} className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <CreditCard className="h-4 w-4 text-muted-foreground" />
                                        <span className="font-medium text-sm">{wallet.name}</span>
                                    </div>
                                    <span className="font-semibold text-sm">
                                        {formatCurrency(wallet.balance)}
                                    </span>
                                </div>

                                {/* Credit Card Progress Bar */}
                                {wallet.type === 'card_credit' && wallet.card_limit && wallet.card_limit > 0 && (
                                    <div className="space-y-1">
                                        <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                                            <div
                                                className={`h-full transition-all ${
                                                    (wallet.usage_percentage ?? 0) >= 80
                                                        ? 'bg-red-500'
                                                        : (wallet.usage_percentage ?? 0) >= 50
                                                          ? 'bg-orange-500'
                                                          : 'bg-green-500'
                                                }`}
                                                style={{ width: `${wallet.usage_percentage ?? 0}%` }}
                                            />
                                        </div>
                                        <div className="flex items-center justify-between text-xs text-muted-foreground">
                                            <span>
                                                {formatCurrency(wallet.card_limit_used ?? 0)} de{' '}
                                                {formatCurrency(wallet.card_limit)}
                                            </span>
                                            <span>{wallet.usage_percentage?.toFixed(1)}%</span>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-center text-sm text-muted-foreground">
                        Nenhuma carteira cadastrada
                    </p>
                )}
                <Button
                    variant="outline"
                    className="mt-4 w-full"
                    onClick={() => router.visit(route('dashboard.wallets.index'))}
                >
                    Gerenciar Carteiras
                </Button>
            </CardContent>
        </Card>
    );
}
