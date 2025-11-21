import { Head, usePage, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Upload, CheckCircle, XCircle } from 'lucide-react';
import { router } from '@inertiajs/react';
import { CreateTransactionDialog } from '@/components/dashboard/create-transaction-dialog';

interface BankTransaction {
    bank_date: string;
    bank_amount: number;
    bank_description: string;
    external_id: string;
    suggested_match: {
        id: number;
        amount: number; // Note: this might come as number from JSON
        description?: string;
        category?: { name: string };
        wallet?: { name: string };
        due_date: string;
    } | null;
    status: 'match_found' | 'new_entry' | 'reconciled';
}

export default function ReconciliationIndex() {
    const { props } = usePage();
    const categories = (props as any).categories || [];
    const wallets = (props as any).wallets || [];
    
    const { data, setData, post, processing, errors } = useForm({
        file: null as File | null,
    });

    // Update local state when props change (after upload)
    const [localTransactions, setLocalTransactions] = useState<BankTransaction[]>([]);
    
    // If transactions come from props (after upload), use them
    if ((props as any).transactions && localTransactions.length === 0 && (props as any).transactions.length > 0) {
         setLocalTransactions((props as any).transactions);
    }

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

    const handleUpload = () => {
        if (!data.file) return;

        post(route('dashboard.reconciliation.upload'), {
            forceFormData: true,
            onSuccess: (page) => {
                // The page will reload with new props.transactions
                // We can also set local state if we want to manipulate it client-side
                const newTransactions = (page.props as any).transactions;
                if (newTransactions) {
                    setLocalTransactions(newTransactions);
                }
            },
        });
    };

    const handleReconcile = (transaction: BankTransaction, index: number) => {
        if (!transaction.suggested_match) return;

        router.post(route('dashboard.reconciliation.reconcile', transaction.suggested_match.id), {
            external_id: transaction.external_id
        }, {
            preserveScroll: true,
            onSuccess: () => {
                const newTransactions = [...localTransactions];
                newTransactions[index].status = 'reconciled';
                setLocalTransactions(newTransactions);
                // Success toast will be shown automatically by CustomToast via Inertia flash messages
            }
        });
    };

    const handleOpenCreate = (tx: BankTransaction, index: number) => {
        setSelectedTransaction({
            amount: tx.bank_amount, // Amount from bank (negative for expense)
            date: tx.bank_date,
            description: tx.bank_description,
            external_id: tx.external_id,
            index: index
        });
        setCreateDialogOpen(true);
    };

    const handleCreateSuccess = () => {
        if (selectedTransaction) {
            const newTransactions = [...localTransactions];
            newTransactions[selectedTransaction.index].status = 'reconciled';
            setLocalTransactions(newTransactions);
        }
    };

    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
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
                            className="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-violet-50 file:text-violet-700
                                hover:file:bg-violet-100
                            "
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
                                                        <span className="font-semibold flex items-center gap-2">
                                                            <CheckCircle className="h-3 w-3 text-green-500" />
                                                            Match Encontrado
                                                        </span>
                                                        <span className="text-muted-foreground text-xs">
                                                            {tx.suggested_match.wallet?.name} - {tx.suggested_match.category?.name}
                                                        </span>
                                                        <span className="text-xs">
                                                            Venc: {formatDate(tx.suggested_match.due_date)}
                                                        </span>
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-2 text-muted-foreground text-sm">
                                                        <XCircle className="h-3 w-3" />
                                                        Nenhum match
                                                    </div>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {tx.status === 'reconciled' ? (
                                                    <span className="text-green-600 font-medium flex items-center justify-end gap-1">
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
                    onSuccess={handleCreateSuccess}
                />
            </div>
        </DashboardLayout>
    );
}

