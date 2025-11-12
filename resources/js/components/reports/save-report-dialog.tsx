import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useState } from 'react';

interface SaveReportDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onSave: (name: string, description?: string) => void;
    isSaving: boolean;
}

export function SaveReportDialog({ open, onOpenChange, onSave, isSaving }: SaveReportDialogProps) {
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');

    const handleSave = () => {
        if (!name.trim()) return;
        onSave(name.trim(), description.trim() || undefined);
        setName('');
        setDescription('');
    };

    const handleCancel = () => {
        setName('');
        setDescription('');
        onOpenChange(false);
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Salvar Relatório</DialogTitle>
                    <DialogDescription>
                        Dê um nome ao seu relatório para salvá-lo e acessá-lo rapidamente depois.
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-4 py-4">
                    <div className="space-y-2">
                        <Label htmlFor="report-name">
                            Nome do Relatório <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            id="report-name"
                            placeholder="Ex: Despesas Mensais de Alimentação"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            disabled={isSaving}
                            autoFocus
                            onKeyDown={(e) => {
                                if (e.key === 'Enter' && name.trim()) {
                                    handleSave();
                                }
                            }}
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="report-description">Descrição (opcional)</Label>
                        <Textarea
                            id="report-description"
                            placeholder="Adicione uma descrição para lembrar do propósito deste relatório..."
                            value={description}
                            onChange={(e) => setDescription(e.target.value)}
                            disabled={isSaving}
                            rows={3}
                        />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" onClick={handleCancel} disabled={isSaving}>
                        Cancelar
                    </Button>
                    <Button onClick={handleSave} disabled={!name.trim() || isSaving}>
                        {isSaving ? 'Salvando...' : 'Salvar Relatório'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
