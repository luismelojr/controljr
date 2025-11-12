import { ChartDataPoint, ReportSummary } from '@/types/reports';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    TableFooter,
} from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface ReportTableProps {
    data: ChartDataPoint[];
    summary?: ReportSummary;
    showPercentage?: boolean;
    showCount?: boolean;
}

export function ReportTable({
    data,
    summary,
    showPercentage = true,
    showCount = true,
}: ReportTableProps) {
    // Format currency
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value / 100);
    };

    // Format percentage
    const formatPercentage = (value: number) => {
        return `${value.toFixed(2)}%`;
    };

    if (!data || data.length === 0) {
        return (
            <Card>
                <CardContent className="py-8">
                    <div className="flex items-center justify-center text-muted-foreground">
                        Nenhum dado disponível para exibição
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-lg">Dados do Relatório</CardTitle>
            </CardHeader>
            <CardContent>
                <div className="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="font-semibold">
                                    #
                                </TableHead>
                                <TableHead className="font-semibold">
                                    Descrição
                                </TableHead>
                                <TableHead className="text-right font-semibold">
                                    Valor
                                </TableHead>
                                {showPercentage && (
                                    <TableHead className="text-right font-semibold">
                                        Percentual
                                    </TableHead>
                                )}
                                {showCount && (
                                    <TableHead className="text-right font-semibold">
                                        Quantidade
                                    </TableHead>
                                )}
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.map((item, index) => (
                                <TableRow key={index}>
                                    <TableCell className="font-medium">
                                        {index + 1}
                                    </TableCell>
                                    <TableCell>{item.name}</TableCell>
                                    <TableCell className="text-right font-medium">
                                        {formatCurrency(item.value)}
                                    </TableCell>
                                    {showPercentage && (
                                        <TableCell className="text-right">
                                            {item.percentage !== undefined
                                                ? formatPercentage(
                                                      item.percentage
                                                  )
                                                : '-'}
                                        </TableCell>
                                    )}
                                    {showCount && (
                                        <TableCell className="text-right">
                                            {item.count !== undefined
                                                ? item.count
                                                : '-'}
                                        </TableCell>
                                    )}
                                </TableRow>
                            ))}
                        </TableBody>
                        {summary && (
                            <TableFooter>
                                <TableRow>
                                    <TableCell
                                        colSpan={2}
                                        className="font-semibold"
                                    >
                                        Total
                                    </TableCell>
                                    <TableCell className="text-right font-semibold">
                                        {formatCurrency(summary.total)}
                                    </TableCell>
                                    {showPercentage && (
                                        <TableCell className="text-right font-semibold">
                                            100%
                                        </TableCell>
                                    )}
                                    {showCount && (
                                        <TableCell className="text-right font-semibold">
                                            {summary.count || '-'}
                                        </TableCell>
                                    )}
                                </TableRow>
                                {summary.average !== undefined && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={2}
                                            className="font-medium text-muted-foreground"
                                        >
                                            Média
                                        </TableCell>
                                        <TableCell className="text-right font-medium text-muted-foreground">
                                            {formatCurrency(summary.average)}
                                        </TableCell>
                                        {showPercentage && <TableCell />}
                                        {showCount && <TableCell />}
                                    </TableRow>
                                )}
                            </TableFooter>
                        )}
                    </Table>
                </div>

                {/* Summary Cards */}
                {summary && (
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div className="bg-muted rounded-lg p-4">
                            <p className="text-xs text-muted-foreground mb-1">
                                Total
                            </p>
                            <p className="text-lg font-semibold">
                                {formatCurrency(summary.total)}
                            </p>
                        </div>
                        {summary.count !== undefined && (
                            <div className="bg-muted rounded-lg p-4">
                                <p className="text-xs text-muted-foreground mb-1">
                                    Registros
                                </p>
                                <p className="text-lg font-semibold">
                                    {summary.count}
                                </p>
                            </div>
                        )}
                        {summary.average !== undefined && (
                            <div className="bg-muted rounded-lg p-4">
                                <p className="text-xs text-muted-foreground mb-1">
                                    Média
                                </p>
                                <p className="text-lg font-semibold">
                                    {formatCurrency(summary.average)}
                                </p>
                            </div>
                        )}
                        {summary.categories_count !== undefined && (
                            <div className="bg-muted rounded-lg p-4">
                                <p className="text-xs text-muted-foreground mb-1">
                                    Categorias
                                </p>
                                <p className="text-lg font-semibold">
                                    {summary.categories_count}
                                </p>
                            </div>
                        )}
                        {summary.wallets_count !== undefined && (
                            <div className="bg-muted rounded-lg p-4">
                                <p className="text-xs text-muted-foreground mb-1">
                                    Carteiras
                                </p>
                                <p className="text-lg font-semibold">
                                    {summary.wallets_count}
                                </p>
                            </div>
                        )}
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
