import { Button } from '@/components/ui/button';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Attachment } from '@/types/attachment';
import { router } from '@inertiajs/react';
import { Download, FileText, Image, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface AttachmentItemProps {
    attachment: Attachment;
    onDelete?: () => void;
}

export function AttachmentItem({ attachment, onDelete }: AttachmentItemProps) {
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);

    const handleDownload = () => {
        window.location.href = route('dashboard.attachments.download', { attachment: attachment.uuid });
    };

    const handleDelete = () => {
        setIsDeleteOpen(true);
    };

    const confirmDelete = () => {
        router.delete(route('dashboard.attachments.destroy', { attachment: attachment.uuid }), {
            onSuccess: () => {
                onDelete?.();
            },
        });
    };

    const getFileIcon = () => {
        if (attachment.is_image) {
            return <Image className="h-5 w-5 text-blue-500" />;
        }
        if (attachment.is_pdf) {
            return <FileText className="h-5 w-5 text-red-500" />;
        }
        return <FileText className="h-5 w-5 text-muted-foreground" />;
    };

    const getFileTypeLabel = () => {
        if (attachment.is_image) return 'Imagem';
        if (attachment.is_pdf) return 'PDF';
        return attachment.extension.toUpperCase();
    };

    return (
        <>
            <div className="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-muted/50">
                <div className="flex items-center gap-3">
                    {getFileIcon()}
                    <div>
                        <p className="font-medium">{attachment.original_name}</p>
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <span>{getFileTypeLabel()}</span>
                            <span>•</span>
                            <span>{attachment.file_size_formatted}</span>
                        </div>
                    </div>
                </div>
                <div className="flex items-center gap-2">
                    <Button variant="ghost" size="icon" onClick={handleDownload} title="Baixar">
                        <Download className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon" onClick={handleDelete} title="Excluir">
                        <Trash2 className="h-4 w-4 text-destructive" />
                    </Button>
                </div>
            </div>

            <ConfirmDeleteDialog
                open={isDeleteOpen}
                onOpenChange={setIsDeleteOpen}
                onConfirm={confirmDelete}
                title="Excluir Anexo"
                description="Esta ação não pode ser desfeita. O arquivo será excluído permanentemente"
                itemName={attachment.original_name}
                confirmText="Excluir"
            />
        </>
    );
}
