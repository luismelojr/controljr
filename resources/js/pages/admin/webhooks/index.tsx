import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head } from '@inertiajs/react';
import { Eye } from 'lucide-react';

interface WebhookCall {
    id: number;
    uuid: string;
    type: string;
    payload: any;
    exception: string | null;
    created_at: string;
}

interface Props {
    webhooks: {
        data: WebhookCall[];
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}

export default function AdminWebhooks({ webhooks }: Props) {
    return (
        <DashboardLayout title="Webhooks" subtitle="Logs de integração">
            <Head title="Logs de Webhooks" />

            <div className="flex h-full flex-1 flex-col gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Histórico de Webhooks</CardTitle>
                        <CardDescription>Registro de todas as notificações recebidas.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Data</TableHead>
                                    <TableHead>Tipo</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="text-right">Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {webhooks.data.map((webhook) => (
                                    <TableRow key={webhook.id}>
                                        <TableCell>{webhook.created_at}</TableCell>
                                        <TableCell className="font-mono text-xs font-medium">{webhook.type || 'N/A'}</TableCell>
                                        <TableCell>
                                            <Badge variant={webhook.exception ? 'destructive' : 'success'}>
                                                {webhook.exception ? 'Erro' : 'Sucesso'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="text-right">
                                            <Dialog>
                                                <DialogTrigger asChild>
                                                    <Button variant="ghost" size="icon">
                                                        <Eye className="h-4 w-4" />
                                                    </Button>
                                                </DialogTrigger>
                                                <DialogContent className="max-h-[80vh] max-w-2xl overflow-y-auto">
                                                    <DialogHeader>
                                                        <DialogTitle>Detalhes do Webhook</DialogTitle>
                                                        <DialogDescription>
                                                            {webhook.type} - {webhook.created_at}
                                                        </DialogDescription>
                                                    </DialogHeader>
                                                    <div className="space-y-4">
                                                        {webhook.exception && (
                                                            <div className="rounded-md bg-destructive/10 p-4 text-sm text-destructive">
                                                                <p className="font-semibold">Erro processando webhook:</p>
                                                                <p>{webhook.exception}</p>
                                                            </div>
                                                        )}
                                                        <div className="space-y-2">
                                                            <h4 className="text-sm font-medium">Payload Recebido</h4>
                                                            <pre className="overflow-x-auto rounded-md bg-muted p-4 text-xs whitespace-pre-wrap">
                                                                {JSON.stringify(webhook.payload, null, 2)}
                                                            </pre>
                                                        </div>
                                                    </div>
                                                </DialogContent>
                                            </Dialog>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {webhooks.data.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={4} className="py-8 text-center text-muted-foreground">
                                            Nenhum registro encontrado.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        {/* Pagination Controls */}
                        <div className="mt-4 flex items-center justify-end gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => (window.location.href = webhooks.prev_page_url || '#')}
                                disabled={!webhooks.prev_page_url}
                            >
                                Anterior
                            </Button>
                            <span className="text-sm text-muted-foreground">
                                Página {webhooks.current_page} de {webhooks.last_page}
                            </span>
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => (window.location.href = webhooks.next_page_url || '#')}
                                disabled={!webhooks.next_page_url}
                            >
                                Próxima
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </DashboardLayout>
    );
}
