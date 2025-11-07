import { TrendingUp, Copy, ArrowUpRight, ArrowDownRight, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

interface BalanceCardProps {
    balance: number;
    accountNumber: string;
    percentageChange: number;
    className?: string;
}

export function BalanceCard({ balance, accountNumber, percentageChange, className }: BalanceCardProps) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const copyAccountNumber = () => {
        navigator.clipboard.writeText(accountNumber);
    };

    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="mb-4 flex items-center justify-between">
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                    <span>My Balance</span>
                    <div className="h-4 w-4 rounded-full border border-muted-foreground/30 flex items-center justify-center">
                        <span className="text-xs">?</span>
                    </div>
                </div>
                <Badge variant={percentageChange >= 0 ? 'default' : 'destructive'} className="gap-1">
                    <TrendingUp className="h-3 w-3" />
                    {Math.abs(percentageChange)}%
                </Badge>
            </div>

            <div className="mb-4">
                <h2 className="text-4xl font-bold">{formatCurrency(balance)}</h2>
                <div className="mt-2 flex items-center gap-2 text-sm text-muted-foreground">
                    <span>{accountNumber}</span>
                    <Button variant="ghost" size="icon" className="h-6 w-6" onClick={copyAccountNumber}>
                        <Copy className="h-3 w-3" />
                    </Button>
                </div>
            </div>

            <div className="flex gap-2">
                <Button className="flex-1 gap-2">
                    <ArrowUpRight className="h-4 w-4" />
                    Transfer
                </Button>
                <Button variant="outline" className="flex-1 gap-2">
                    <ArrowDownRight className="h-4 w-4" />
                    Received
                </Button>
                <Button variant="outline" size="icon">
                    <Plus className="h-4 w-4" />
                </Button>
            </div>
        </div>
    );
}
