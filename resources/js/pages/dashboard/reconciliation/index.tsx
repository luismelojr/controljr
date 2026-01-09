import { CreateTransactionDialog } from '@/components/dashboard/create-transaction-dialog';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { TagOption } from '@/components/tags/tag-input';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { router, useForm, usePage } from '@inertiajs/react'; // ensure router is imported if used directly, or use useForm helpers
import { CheckCircle, Upload, XCircle } from 'lucide-react';
import { useState } from 'react';

interface BankTransaction {
    bank_date: string;
    bank_amount: number;
    bank_description: string;
    external_id: string;
    suggested_match: {
        id: number;
        amount: number;
        description?: string;
        category?: { name: string };
        wallet?: { name: string };
        due_date: string;
    } | null;
    status: 'match_found' | 'new_entry' | 'reconciled';
}

interface Props {
    categories: Array<{ id: number; name: string }>;
    wallets: Array<{ id: number; name: string }>;
    tags: TagOption[];
}

export default function ReconciliationIndex({ categories, wallets, tags }: Props) {
    const { props } = usePage();

    const { data, setData, post, processing, errors } = useForm({
        file: null as File | null,
    });

    // Update local state when props change (after upload)
    const [localTransactions, setLocalTransactions] = useState<BankTransaction[]>([]);

    // Better approach: initialize state from props if available
    useState(() => {
        if ((props as any).transactions) {
            setLocalTransactions((props as any).transactions);
        }
    });

    const [createDialogOpen, setCreateDialogOpen] = useState(false);
    const [selectedTransaction, setSelectedTransaction] = useState<{
        amount: number;
        date: string;
        description: string;
        external_id: string;
        index: number; // To update status after creation
    } | null>(null);

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files) {
            setData('file', e.target.files[0]);
        }
    };

    const handleUpload = (e: React.FormEvent) => {
        e.preventDefault();
        if (!data.file) return;

        post(route('dashboard.reconciliation.upload'), {
            forceFormData: true,
            onSuccess: (page) => {
                const newTransactions = (page.props as any).transactions;
                if (newTransactions) {
                    setLocalTransactions(newTransactions);
                }
            },
        });
    };

    const handleReconcile = (transaction: BankTransaction, index: number) => {
        if (!transaction.suggested_match) return;

        router.post(
            route('dashboard.reconciliation.reconcile', transaction.suggested_match.id),
            {
                external_id: transaction.external_id,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    const newTransactions = [...localTransactions];
                    newTransactions[index].status = 'reconciled';
                    setLocalTransactions(newTransactions);
                },
            },
        );
    };

    const handleOpenCreate = (tx: BankTransaction, index: number) => {
        setSelectedTransaction({
            amount: tx.bank_amount,
            date: tx.bank_date,
            description: tx.bank_description,
            external_id: tx.external_id,
            index: index,
        });
        setCreateDialogOpen(true);
    };

    const handleCreateSuccess = () => {
        if (selectedTransaction) {
            const newTransactions = [...localTransactions];
            newTransactions[selectedTransaction.index].status = 'reconciled';
            setLocalTransactions(newTransactions);
            setCreateDialogOpen(false); // Close dialog
        }
    };

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('pt-BR');
    };

    return (
        <DashboardLayout title="Conciliação Bancária" subtitle="Importe seu extrato e confira suas transações">
            <div className="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Importar Extrato</CardTitle>
                        <CardDescription>Selecione um arquivo .OFX ou .TXT do seu banco.</CardDescription>
                    </CardHeader>
                    <CardContent className="flex items-center gap-4">
                        <input
                            type="file"
                            accept=".ofx,.txt,.xml"
                            onChange={handleFileChange}
                            className="block w-full text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-violet-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-violet-700 hover:file:bg-violet-100"
                        />
                        <Button onClick={handleUpload} disabled={!data.file || processing}>
                            {processing ? <Upload className="mr-2 h-4 w-4 animate-spin" /> : <Upload className="mr-2 h-4 w-4" />}
                            Importar
                        </Button>
                    </CardContent>
                </Card>

                {localTransactions.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Resultado da Análise</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Data</TableHead>
                                        <TableHead>Descrição (Banco)</TableHead>
                                        <TableHead>Valor</TableHead>
                                        <TableHead>Sugestão do Sistema</TableHead>
                                        <TableHead className="text-right">Ação</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {localTransactions.map((tx, index) => (
                                        <TableRow key={index} className={tx.status === 'reconciled' ? 'bg-green-50/50 opacity-60' : ''}>
                                            <TableCell>{formatDate(tx.bank_date)}</TableCell>
                                            <TableCell className="font-medium">{tx.bank_description}</TableCell>
                                            <TableCell className={tx.bank_amount < 0 ? 'text-red-600' : 'text-green-600'}>
                                                {formatCurrency(tx.bank_amount)}
                                            </TableCell>
                                            <TableCell>
                                                {tx.suggested_match ? (
                                                    <div className="flex flex-col text-sm">
                                                        <span className="flex items-center gap-2 font-semibold">
                                                            <CheckCircle className="h-3 w-3 text-green-500" />
                                                            Match Encontrado
                                                        </span>
                                                        <span className="text-xs text-muted-foreground">
                                                            {tx.suggested_match.wallet?.name} - {tx.suggested_match.category?.name}
                                                        </span>
                                                        <span className="text-xs">Venc: {formatDate(tx.suggested_match.due_date)}</span>
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                        <XCircle className="h-3 w-3" />
                                                        Nenhum match
                                                    </div>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {tx.status === 'reconciled' ? (
                                                    <span className="flex items-center justify-end gap-1 font-medium text-green-600">
                                                        <CheckCircle className="h-4 w-4" /> Conciliado
                                                    </span>
                                                ) : tx.suggested_match ? (
                                                    <Button size="sm" onClick={() => handleReconcile(tx, index)}>
                                                        Confirmar
                                                    </Button>
                                                ) : (
                                                    <Button variant="outline" size="sm" onClick={() => handleOpenCreate(tx, index)}>
                                                        Criar
                                                    </Button>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                )}

                <CreateTransactionDialog
                    open={createDialogOpen}
                    onOpenChange={setCreateDialogOpen}
                    initialData={selectedTransaction}
                    categories={categories}
                    wallets={wallets}
                    tags={tags}
                    onSuccess={handleCreateSuccess}
                />
            </div>
        </DashboardLayout>
    );
}
