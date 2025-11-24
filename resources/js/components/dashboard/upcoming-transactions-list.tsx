import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CheckCircle2, Clock } from 'lucide-react';

interface Transaction {
    id: string;
    name: string;
    category: string;
    due_date: string;
    due_date_raw: string;
    amount: number;
    status: string;
    installment_info: string | null;
}

interface UpcomingTransactionsListProps {
    transactions: Transaction[];
    onMarkAsPaid: (id: string) => void;
    formatCurrency: (value: number) => string;
}

export function UpcomingTransactionsList({ transactions, onMarkAsPaid, formatCurrency }: UpcomingTransactionsListProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center justify-between">
                    <span className="flex items-center gap-2">
                        <Clock className="h-5 w-5 text-orange-600" />
                        Próximas Contas
                    </span>
                    <Badge variant="secondary">{transactions.length}</Badge>
                </CardTitle>
            </CardHeader>
            <CardContent>
                {transactions.length > 0 ? (
                    <div className="space-y-3">
                        {transactions.map((transaction) => (
                            <div
                                key={transaction.id}
                                className="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50"
                            >
                                <div className="flex items-center gap-3 flex-1 min-w-0">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-950 shrink-0">
                                        <Clock className="h-5 w-5 text-orange-600" />
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className="font-medium truncate">{transaction.name}</p>
                                        <p className="text-sm text-muted-foreground">
                                            {transaction.category} • Vence em {transaction.due_date}
                                            {transaction.installment_info && (
                                                <span className="ml-2">({transaction.installment_info})</span>
                                            )}
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3 shrink-0">
                                    <span className="font-semibold text-red-600">
                                        {formatCurrency(transaction.amount)}
                                    </span>
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        onClick={() => onMarkAsPaid(transaction.id)}
                                    >
                                        <CheckCircle2 className="mr-2 h-4 w-4" />
                                        Pagar
                                    </Button>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <div className="flex flex-col items-center justify-center py-12 text-center">
                        <Clock className="h-12 w-12 text-muted-foreground/50 mb-3" />
                        <p className="text-muted-foreground font-medium">Nenhuma conta próxima do vencimento</p>
                        <p className="text-sm text-muted-foreground mt-1">
                            Suas próximas contas aparecerão aqui
                        </p>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
