import { AttachmentItem } from '@/components/attachments/attachment-item';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Attachment } from '@/types/attachment';
import { Paperclip } from 'lucide-react';

interface AttachmentListProps {
    attachments: Attachment[];
    onDelete?: () => void;
    className?: string;
}

export function AttachmentList({ attachments, onDelete, className = '' }: AttachmentListProps) {
    if (attachments.length === 0) {
        return (
            <Card className={className}>
                <CardContent className="flex flex-col items-center justify-center py-8 text-center">
                    <Paperclip className="mb-2 h-8 w-8 text-muted-foreground" />
                    <p className="text-sm text-muted-foreground">Nenhum anexo adicionado ainda</p>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <Paperclip className="h-5 w-5" />
                    Anexos ({attachments.length})
                </CardTitle>
                <CardDescription>Documentos, notas fiscais e comprovantes</CardDescription>
            </CardHeader>
            <CardContent className="space-y-2">
                {attachments.map((attachment) => (
                    <AttachmentItem key={attachment.uuid} attachment={attachment} onDelete={onDelete} />
                ))}
            </CardContent>
        </Card>
    );
}
