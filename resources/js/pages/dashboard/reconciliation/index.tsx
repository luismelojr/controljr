import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import axios from 'axios';
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
    
    const [file, setFile] = useState<File | null>(null);
    const [transactions, setTransactions] = useState<BankTransaction[]>([]);
    const [loading, setLoading] = useState(false);
    
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
            setFile(e.target.files[0]);
        }
    };

    const handleUpload = async () => {
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        setLoading(true);
        
        // We use router.post instead of axios to leverage Inertia's native toast handling (if configured for manual visits)
        // However, for file upload that returns JSON data without page reload, axios is better.
        // We will use axios but we can't use the native toast easily here unless we dispatch a custom event or have a global toaster store.
        // Since the user has a CustomToast that listens to page.props, let's stick to axios for data fetching
        // and we can rely on the UI state for feedback (showing the table is feedback enough).
        
        try {
            const response = await axios.post(route('dashboard.reconciliation.upload'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            setTransactions(response.data);
        } catch (error: any) {
            console.error(error);
            const errorMessage = error.response?.data?.error || "Erro desconhecido ao processar arquivo.";
            alert(`Erro: ${errorMessage}`);
        } finally {
            setLoading(false);
        }
    };

    const handleReconcile = (transaction: BankTransaction, index: number) => {
        if (!transaction.suggested_match) return;

        router.post(route('dashboard.reconciliation.reconcile', transaction.suggested_match.id), {
            external_id: transaction.external_id
        }, {
            preserveScroll: true,
            onSuccess: () => {
                const newTransactions = [...transactions];
                newTransactions[index].status = 'reconciled';
                setTransactions(newTransactions);
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
            const newTransactions = [...transactions];
            newTransactions[selectedTransaction.index].status = 'reconciled';
            setTransactions(newTransactions);
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
                        <Button onClick={handleUpload} disabled={!file || loading}>
                            {loading ? <Upload className="mr-2 h-4 w-4 animate-spin" /> : <Upload className="mr-2 h-4 w-4" />}
                            Importar
                        </Button>
                    </CardContent>
                </Card>

                {transactions.length > 0 && (
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
                                    {transactions.map((tx, index) => (
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

