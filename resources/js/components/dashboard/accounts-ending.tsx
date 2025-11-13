import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { CheckCircle2, CreditCard, FolderOpen, Calendar } from 'lucide-react';

interface AccountEndingProps {
    accounts: Array<{
        uuid: string;
        name: string;
        category: string;
        wallet: string;
        total_amount: number;
        installments: number;
        installment_info: string;
        due_date: string;
    }>;
}

export function AccountsEnding({ accounts }: AccountEndingProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center justify-between">
                    <span className="flex items-center gap-2">
                        <CheckCircle2 className="h-5 w-5 text-green-600" />
                        Contas Finalizando Este Mês
                    </span>
                    <Badge variant="secondary">{accounts.length}</Badge>
                </CardTitle>
            </CardHeader>
            <CardContent>
                {accounts.length > 0 ? (
                    <div className="space-y-3">
                        {accounts.map((account) => (
                            <div
                                key={account.uuid}
                                className="flex flex-col gap-3 rounded-lg border p-4 transition-colors hover:bg-muted/50"
                            >
                                {/* Top Row: Name and Badge */}
                                <div className="flex items-start justify-between gap-3">
                                    <div className="flex-1 min-w-0">
                                        <p className="font-semibold text-base truncate">{account.name}</p>
                                    </div>
                                    <Badge variant="outline" className="bg-green-50 text-green-700 border-green-200 shrink-0">
                                        Última Parcela
                                    </Badge>
                                </div>

                                {/* Middle Row: Details */}
                                <div className="grid grid-cols-2 gap-2 text-sm text-muted-foreground">
                                    <div className="flex items-center gap-2">
                                        <FolderOpen className="h-4 w-4 shrink-0" />
                                        <span className="truncate">{account.category}</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <CreditCard className="h-4 w-4 shrink-0" />
                                        <span className="truncate">{account.wallet}</span>
                                    </div>
                                </div>

                                {/* Bottom Row: Amount and Installment Info */}
                                <div className="flex items-center justify-between pt-2 border-t">
                                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                        <Calendar className="h-4 w-4" />
                                        <span>Vence: {account.due_date}</span>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <span className="text-xs text-muted-foreground font-medium">
                                            {account.installment_info}
                                        </span>
                                        <span className="font-bold text-base text-green-600">
                                            {formatCurrency(account.total_amount)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="flex flex-col items-center justify-center py-12 text-center">
                        <CheckCircle2 className="h-12 w-12 text-muted-foreground/50 mb-3" />
                        <p className="text-muted-foreground font-medium">Nenhuma conta finalizando este mês</p>
                        <p className="text-sm text-muted-foreground mt-1">
                            As contas parceladas que terminam este mês aparecerão aqui
                        </p>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
