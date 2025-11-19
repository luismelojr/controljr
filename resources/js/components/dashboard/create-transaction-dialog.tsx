import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useForm } from "@inertiajs/react";
import { useEffect } from "react";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

interface CreateTransactionDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    initialData: {
        amount: number;
        date: string;
        description: string;
        external_id: string;
    } | null;
    categories: Array<{ id: number; name: string }>;
    wallets: Array<{ id: number; name: string }>;
    onSuccess: () => void;
}

export function CreateTransactionDialog({
    open,
    onOpenChange,
    initialData,
    categories = [],
    wallets = [],
    onSuccess,
}: CreateTransactionDialogProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        amount: "",
        due_date: "",
        category_id: "",
        wallet_id: "",
        external_id: "",
        is_reconciled: true,
        status: "paid", // Since it comes from bank, it is paid
        paid_at: "",
    });

    useEffect(() => {
        if (open && initialData) {
            setData({
                ...data,
                amount: Math.abs(initialData.amount).toString(),
                due_date: initialData.date,
                paid_at: initialData.date,
                external_id: initialData.external_id,
            });
        }
    }, [open, initialData]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('dashboard.transactions.store'), {
            onSuccess: () => {
                onSuccess();
                onOpenChange(false);
                reset();
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Nova Transação</DialogTitle>
                    <DialogDescription>
                        Confirme os dados para criar e conciliar esta transação.
                    </DialogDescription>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="grid gap-4 py-4">
                    <div className="grid gap-2">
                        <Label htmlFor="description">Descrição (do Banco)</Label>
                        <Input
                            id="description"
                            value={initialData?.description || ""}
                            disabled
                            className="bg-muted"
                        />
                        <p className="text-xs text-muted-foreground">Esta descrição vem do extrato bancário</p>
                    </div>
                    
                    <div className="grid grid-cols-2 gap-4">
                        <div className="grid gap-2">
                            <Label htmlFor="amount">Valor</Label>
                            <Input
                                id="amount"
                                type="number"
                                step="0.01"
                                value={data.amount}
                                onChange={(e) => setData("amount", e.target.value)}
                            />
                            {errors.amount && <span className="text-xs text-red-500">{errors.amount}</span>}
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="date">Data</Label>
                            <Input
                                id="date"
                                type="date"
                                value={data.due_date}
                                onChange={(e) => setData("due_date", e.target.value)}
                            />
                             {errors.due_date && <span className="text-xs text-red-500">{errors.due_date}</span>}
                        </div>
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="category">Categoria</Label>
                        <Select
                            value={data.category_id}
                            onValueChange={(val) => setData("category_id", val)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione..." />
                            </SelectTrigger>
                            <SelectContent>
                                {categories.map((cat) => (
                                    <SelectItem key={cat.id} value={String(cat.id)}>
                                        {cat.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.category_id && <span className="text-xs text-red-500">{errors.category_id}</span>}
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="wallet">Carteira</Label>
                        <Select
                            value={data.wallet_id}
                            onValueChange={(val) => setData("wallet_id", val)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione..." />
                            </SelectTrigger>
                            <SelectContent>
                                {wallets.map((wallet) => (
                                    <SelectItem key={wallet.id} value={String(wallet.id)}>
                                        {wallet.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.wallet_id && <span className="text-xs text-red-500">{errors.wallet_id}</span>}
                    </div>

                    <DialogFooter>
                        <Button type="submit" disabled={processing}>
                            {processing ? "Salvando..." : "Salvar e Conciliar"}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}

