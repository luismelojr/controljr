import { Badge } from '@/components/ui/badge';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/react';
import { ArrowUpRight, CheckCircle2, Clock, CreditCard, XCircle } from 'lucide-react';

interface Payment {
    uuid: string;
    user_name: string;
    amount_formatted: string;
    status: string;
    date: string;
    billing_type?: string;
}

import { cn } from '@/lib/utils';

interface AdminRecentPaymentsProps {
    payments: Payment[];
    className?: string;
}

const statusMap: Record<string, { label: string; variant: 'default' | 'success' | 'destructive' | 'warning' | 'secondary' | 'outline'; icon: any }> =
    {
        PENDING: { label: 'Pendente', variant: 'warning', icon: Clock },
        CONFIRMED: { label: 'Confirmado', variant: 'success', icon: CheckCircle2 },
        RECEIVED: { label: 'Recebido', variant: 'success', icon: CheckCircle2 },
        OVERDUE: { label: 'Atrasado', variant: 'destructive', icon: XCircle },
        REFUNDED: { label: 'Reembolsado', variant: 'secondary', icon: ArrowUpRight },
        CANCELLED: { label: 'Cancelado', variant: 'destructive', icon: XCircle },
    };

export function AdminRecentPayments({ payments, className }: AdminRecentPaymentsProps) {
    return (
        <Card className={cn('col-span-4 lg:col-span-7', className)}>
            <CardHeader className="flex flex-row items-center justify-between">
                <div className="space-y-1.5">
                    <CardTitle>Pagamentos Recentes</CardTitle>
                    <CardDescription>Últimas transações processadas no sistema.</CardDescription>
                </div>
                <Link href={route('admin.payments.index')} className={buttonVariants({ variant: 'outline', size: 'sm' })}>
                    Ver Todos
                </Link>
            </CardHeader>
            <CardContent>
                <div className="space-y-6">
                    {payments.length > 0 ? (
                        payments.map((payment) => {
                            const statusConfig = statusMap[payment.status] || { label: payment.status, variant: 'outline', icon: CreditCard };
                            const StatusIcon = statusConfig.icon;

                            return (
                                <div key={payment.uuid} className="group flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-9 w-9 items-center justify-center rounded-full border bg-muted transition-colors group-hover:bg-background">
                                            <CreditCard className="h-4 w-4 text-muted-foreground" />
                                        </div>
                                        <div className="space-y-1">
                                            <p className="text-sm leading-none font-medium">{payment.user_name}</p>
                                            <p className="mr-2 text-xs text-muted-foreground">{payment.date}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-4">
                                        <div className="font-medium">{payment.amount_formatted}</div>
                                        <Badge variant={statusConfig.variant as any} className="flex items-center gap-1">
                                            {StatusIcon && <StatusIcon className="h-3 w-3" />}
                                            {statusConfig.label}
                                        </Badge>
                                    </div>
                                </div>
                            );
                        })
                    ) : (
                        <div className="py-8 text-center text-muted-foreground">Nenhum pagamento recente.</div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
