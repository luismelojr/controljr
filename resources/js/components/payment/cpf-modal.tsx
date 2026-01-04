import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import TextMask from '@/components/ui/text-mask';

interface CpfModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export default function CpfModal({ open, onOpenChange }: CpfModalProps) {
    const [cpf, setCpf] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);

        router.post(
            '/dashboard/profile/cpf',
            { cpf },
            {
                onSuccess: () => {
                    onOpenChange(false);
                    setCpf('');
                },
                onFinish: () => setIsSubmitting(false),
            }
        );
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <form onSubmit={handleSubmit}>
                    <DialogHeader>
                        <DialogTitle>CPF Necessário</DialogTitle>
                        <DialogDescription>
                            Para processar pagamentos, precisamos do seu CPF conforme
                            exigido pela legislação brasileira.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <TextMask
                            label="CPF"
                            mask="000.000.000-00"
                            placeholder="000.000.000-00"
                            value={cpf}
                            onChange={(e) => setCpf(e.target.value)}
                            required
                            autoFocus
                        />

                        <div className="text-sm text-muted-foreground space-y-2">
                            <p className="font-medium">Por que precisamos do CPF?</p>
                            <ul className="list-disc list-inside space-y-1 text-xs">
                                <li>Exigência legal para processar pagamentos</li>
                                <li>Prevenção de fraudes e lavagem de dinheiro</li>
                                <li>Emissão de nota fiscal (se necessário)</li>
                                <li>Seus dados estão seguros e protegidos</li>
                            </ul>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => onOpenChange(false)}
                            disabled={isSubmitting}
                        >
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={isSubmitting || !cpf}>
                            {isSubmitting ? 'Salvando...' : 'Salvar CPF'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
