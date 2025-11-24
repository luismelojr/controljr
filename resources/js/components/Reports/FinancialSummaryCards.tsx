import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { ArrowDownIcon, ArrowUpIcon, DollarSign, PiggyBank } from "lucide-react";

interface OverviewData {
    total_income: number;
    total_expenses: number;
    net_result: number;
    savings_rate: number;
}

export default function FinancialSummaryCards({ data }: { data: OverviewData }) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    return (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle className="text-sm font-medium">Receita Total</CardTitle>
                    <ArrowUpIcon className="h-4 w-4 text-emerald-500" />
                </CardHeader>
                <CardContent>
                    <div className="text-2xl font-bold text-emerald-600">{formatCurrency(data.total_income)}</div>
                    <p className="text-xs text-muted-foreground">
                        Entradas no período
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle className="text-sm font-medium">Despesas Totais</CardTitle>
                    <ArrowDownIcon className="h-4 w-4 text-rose-500" />
                </CardHeader>
                <CardContent>
                    <div className="text-2xl font-bold text-rose-600">{formatCurrency(data.total_expenses)}</div>
                    <p className="text-xs text-muted-foreground">
                        Saídas no período
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle className="text-sm font-medium">Resultado Líquido</CardTitle>
                    <DollarSign className="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div className={`text-2xl font-bold ${data.net_result >= 0 ? 'text-emerald-600' : 'text-rose-600'}`}>
                        {formatCurrency(data.net_result)}
                    </div>
                    <p className="text-xs text-muted-foreground">
                        Receitas - Despesas
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle className="text-sm font-medium">Taxa de Poupança</CardTitle>
                    <PiggyBank className="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div className="text-2xl font-bold">{data.savings_rate}%</div>
                    <p className="text-xs text-muted-foreground">
                        % da renda poupada
                    </p>
                </CardContent>
            </Card>
        </div>
    );
}
