import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useForm } from '@inertiajs/react';

interface Props {
    open: boolean;
    onClose: () => void;
}

export default function CreateAlertDialog({ open, onClose }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        type: 'credit_card_usage',
        alertable_type: null as string | null,
        alertable_id: null as string | null,
        trigger_value: 80,
        trigger_days: [3] as number[],
        notification_channels: ['database', 'mail'] as string[],
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('dashboard.alerts.store'), {
            onSuccess: () => {
                onClose();
                // Reset form
                setData({
                    type: 'credit_card_usage',
                    alertable_type: null,
                    alertable_id: null,
                    trigger_value: 80,
                    trigger_days: [3],
                    notification_channels: ['database', 'mail'],
                });
            },
        });
    };

    const handleDayToggle = (day: number, checked: boolean) => {
        if (checked) {
            setData(
                'trigger_days',
                [...data.trigger_days, day].sort((a, b) => b - a),
            );
        } else {
            setData(
                'trigger_days',
                data.trigger_days.filter((d) => d !== day),
            );
        }
    };

    const handleChannelToggle = (channel: string, checked: boolean) => {
        if (checked) {
            setData('notification_channels', [...data.notification_channels, channel]);
        } else {
            setData(
                'notification_channels',
                data.notification_channels.filter((c) => c !== channel),
            );
        }
    };

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="max-w-md">
                <DialogHeader>
                    <DialogTitle>Criar Novo Alerta</DialogTitle>
                    <DialogDescription>Configure um alerta para ser notificado sobre eventos importantes.</DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {/* Tipo de Alerta */}
                    <div className="space-y-2">
                        <Label htmlFor="type">Tipo de Alerta</Label>
                        <Select value={data.type} onValueChange={(value) => setData('type', value)}>
                            <SelectTrigger id="type" className={'w-full'}>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="credit_card_usage">Uso de Cartão de Crédito</SelectItem>
                                <SelectItem value="bill_due_date">Vencimento de Contas</SelectItem>
                                <SelectItem value="account_balance">Saldo da Conta</SelectItem>
                            </SelectContent>
                        </Select>
                        {errors.type && <p className="text-sm text-destructive">{errors.type}</p>}
                    </div>

                    {/* Valor de ativação para uso de cartão */}
                    {data.type === 'credit_card_usage' && (
                        <div className="space-y-2">
                            <Label htmlFor="trigger_value">Percentual de Uso (%)</Label>
                            <Input
                                id="trigger_value"
                                type="number"
                                value={data.trigger_value}
                                onChange={(e) => setData('trigger_value', Number(e.target.value))}
                                min={1}
                                max={100}
                            />
                            <p className="text-xs text-muted-foreground">
                                Você será notificado quando o uso atingir {data.trigger_value}% do limite.
                            </p>
                            {errors.trigger_value && <p className="text-sm text-destructive">{errors.trigger_value}</p>}
                        </div>
                    )}

                    {/* Valor de saldo mínimo */}
                    {data.type === 'account_balance' && (
                        <div className="space-y-2">
                            <Label htmlFor="trigger_value">Saldo Mínimo (R$)</Label>
                            <Input
                                id="trigger_value"
                                type="number"
                                value={data.trigger_value}
                                onChange={(e) => setData('trigger_value', Number(e.target.value))}
                                min={0}
                                step="0.01"
                            />
                            <p className="text-xs text-muted-foreground">
                                Você será notificado quando o saldo ficar abaixo de R$ {data.trigger_value}.
                            </p>
                            {errors.trigger_value && <p className="text-sm text-destructive">{errors.trigger_value}</p>}
                        </div>
                    )}

                    {/* Dias de antecedência para vencimento */}
                    {data.type === 'bill_due_date' && (
                        <div className="space-y-2">
                            <Label>Avisar quantos dias antes?</Label>
                            <div className="space-y-2">
                                {[1, 3, 7, 10, 15].map((day) => (
                                    <div key={day} className="flex items-center space-x-2">
                                        <Checkbox
                                            id={`days-${day}`}
                                            checked={data.trigger_days.includes(day)}
                                            onCheckedChange={(checked) => handleDayToggle(day, checked as boolean)}
                                        />
                                        <label
                                            htmlFor={`days-${day}`}
                                            className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                        >
                                            {day} dia{day > 1 ? 's' : ''} antes
                                        </label>
                                    </div>
                                ))}
                            </div>
                            {errors.trigger_days && <p className="text-sm text-destructive">{errors.trigger_days}</p>}
                        </div>
                    )}

                    {/* Canais de notificação */}
                    <div className="space-y-2">
                        <Label>Canais de Notificação</Label>
                        <div className="space-y-2">
                            <div className="flex items-center space-x-2">
                                <Checkbox id="channel-database" checked={data.notification_channels.includes('database')} disabled />
                                <label
                                    htmlFor="channel-database"
                                    className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    Notificação no App (sempre ativo)
                                </label>
                            </div>
                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="channel-mail"
                                    checked={data.notification_channels.includes('mail')}
                                    onCheckedChange={(checked) => handleChannelToggle('mail', checked as boolean)}
                                />
                                <label
                                    htmlFor="channel-mail"
                                    className="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    E-mail
                                </label>
                            </div>
                        </div>
                        {errors.notification_channels && <p className="text-sm text-destructive">{errors.notification_channels}</p>}
                    </div>

                    {/* Botões */}
                    <div className="flex justify-end space-x-2 pt-4">
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancelar
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Criando...' : 'Criar Alerta'}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
