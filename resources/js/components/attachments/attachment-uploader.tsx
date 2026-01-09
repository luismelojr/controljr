import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { router } from '@inertiajs/react';
import { Upload, X } from 'lucide-react';
import { useState } from 'react';

interface AttachmentUploaderProps {
    attachableType: string;
    attachableId: number;
    onUploadComplete?: () => void;
    maxSize?: number; // in MB
    className?: string;
}

export function AttachmentUploader({
    attachableType,
    attachableId,
    onUploadComplete,
    maxSize = 5,
    className = '',
}: AttachmentUploaderProps) {
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [isUploading, setIsUploading] = useState(false);
    const [dragActive, setDragActive] = useState(false);

    const allowedTypes = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'text/csv',
    ];

    const handleFileSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (file) {
            validateAndSetFile(file);
        }
    };

    const validateAndSetFile = (file: File) => {
        // Validate file type
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Use PDF, imagens, Excel, Word, TXT ou CSV.');
            return;
        }

        // Validate file size (convert MB to bytes)
        const maxSizeBytes = maxSize * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            alert(`O arquivo não pode ser maior que ${maxSize}MB.`);
            return;
        }

        setSelectedFile(file);
    };

    const handleUpload = () => {
        if (!selectedFile) return;

        setIsUploading(true);

        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('attachable_type', attachableType);
        formData.append('attachable_id', attachableId.toString());

        router.post(route('dashboard.attachments.store'), formData, {
            forceFormData: true,
            onSuccess: () => {
                setSelectedFile(null);
                setIsUploading(false);
                onUploadComplete?.();
            },
            onError: () => {
                setIsUploading(false);
            },
        });
    };

    const handleDrag = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === 'dragenter' || e.type === 'dragover') {
            setDragActive(true);
        } else if (e.type === 'dragleave') {
            setDragActive(false);
        }
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);

        const file = e.dataTransfer.files?.[0];
        if (file) {
            validateAndSetFile(file);
        }
    };

    const formatFileSize = (bytes: number): string => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    };

    return (
        <Card className={className}>
            <CardContent className="p-6">
                {!selectedFile ? (
                    <div
                        className={`flex flex-col items-center justify-center rounded-lg border-2 border-dashed p-8 transition-colors ${
                            dragActive ? 'border-primary bg-primary/5' : 'border-border'
                        }`}
                        onDragEnter={handleDrag}
                        onDragLeave={handleDrag}
                        onDragOver={handleDrag}
                        onDrop={handleDrop}
                    >
                        <Upload className="mb-4 h-12 w-12 text-muted-foreground" />
                        <Label htmlFor="file-upload" className="mb-2 cursor-pointer text-center">
                            <span className="text-primary hover:underline">Clique para selecionar</span>
                            <span className="text-muted-foreground"> ou arraste um arquivo aqui</span>
                        </Label>
                        <p className="text-xs text-muted-foreground">
                            PDF, Imagens, Excel, Word, TXT ou CSV (máx. {maxSize}MB)
                        </p>
                        <input
                            id="file-upload"
                            type="file"
                            className="hidden"
                            onChange={handleFileSelect}
                            accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.xls,.xlsx,.doc,.docx,.txt,.csv"
                        />
                    </div>
                ) : (
                    <div className="space-y-4">
                        <div className="flex items-center justify-between rounded-lg border p-4">
                            <div className="flex-1">
                                <p className="font-medium">{selectedFile.name}</p>
                                <p className="text-sm text-muted-foreground">{formatFileSize(selectedFile.size)}</p>
                            </div>
                            <Button
                                variant="ghost"
                                size="icon"
                                onClick={() => setSelectedFile(null)}
                                disabled={isUploading}
                            >
                                <X className="h-4 w-4" />
                            </Button>
                        </div>
                        <div className="flex gap-2">
                            <Button onClick={handleUpload} disabled={isUploading} className="flex-1">
                                {isUploading ? 'Enviando...' : 'Fazer Upload'}
                            </Button>
                            <Button variant="outline" onClick={() => setSelectedFile(null)} disabled={isUploading}>
                                Cancelar
                            </Button>
                        </div>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
