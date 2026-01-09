import DashboardLayout from '@/components/layouts/dashboard-layout';
import { TagBadge } from '@/components/tags/tag-badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Head, useForm } from '@inertiajs/react';
import { Edit, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {route} from 'ziggy-js';

interface Tag {
    id: number;
    uuid: string;
    name: string;
    color: string;
    transactions_count?: number;
}

interface Props {
    tags: Tag[];
}

export default function TagsIndex({ tags }: Props) {
    const [createOpen, setCreateOpen] = useState(false);
    const [editingTag, setEditingTag] = useState<Tag | null>(null);
    const [deletingTag, setDeletingTag] = useState<Tag | null>(null);

    const {
        data: createData,
        setData: setCreateData,
        post: createPost,
        processing: createProcessing,
        errors: createErrors,
        reset: createReset,
    } = useForm({
        name: '',
        color: '#6366f1', // Default Indigo
    });

    const {
        data: editData,
        setData: setEditData,
        put: editPut,
        processing: editProcessing,
        errors: editErrors,
        reset: editReset,
    } = useForm({
        name: '',
        color: '',
    });

    // Delete form
    const { delete: destroy } = useForm();

    const handleCreate = (e: React.FormEvent) => {
        e.preventDefault();
        createPost(route('dashboard.tags.store'), {
            onSuccess: () => {
                setCreateOpen(false);
                createReset();
            },
        });
    };

    const handleEditClick = (tag: Tag) => {
        setEditingTag(tag);
        setEditData({
            name: tag.name,
            color: tag.color,
        });
    };

    const handleUpdate = (e: React.FormEvent) => {
        e.preventDefault();
        if (!editingTag) return;

        editPut(route('dashboard.tags.update', editingTag.uuid), {
            onSuccess: () => {
                setEditingTag(null);
                editReset();
            },
        });
    };

    const handleDelete = () => {
        if (!deletingTag) return;
        destroy(route('dashboard.tags.destroy', deletingTag.uuid), {
            onSuccess: () => {
                setDeletingTag(null);
            },
        });
    };

    return (
        <DashboardLayout title="Tags Personalizadas" subtitle="Gerencie suas tags para organizar transações">
            <Head title="Tags" />

            <div className="space-y-6">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Suas Tags</CardTitle>
                            <CardDescription>Crie tags para categorizar melhor seus lançamentos.</CardDescription>
                        </div>
                        <Dialog open={createOpen} onOpenChange={setCreateOpen}>
                            <DialogTrigger asChild>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Nova Tag
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Nova Tag</DialogTitle>
                                    <DialogDescription>Crie uma nova tag para suas transações.</DialogDescription>
                                </DialogHeader>
                                <form onSubmit={handleCreate} className="space-y-4 py-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="name">Nome</Label>
                                        <Input
                                            id="name"
                                            value={createData.name}
                                            onChange={(e) => setCreateData('name', e.target.value)}
                                            placeholder="Ex: Viagem, Reforma..."
                                        />
                                        {createErrors.name && <p className="text-sm text-red-500">{createErrors.name}</p>}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="color">Cor</Label>
                                        <div className="flex gap-2">
                                            <Input
                                                id="color"
                                                type="color"
                                                value={createData.color}
                                                onChange={(e) => setCreateData('color', e.target.value)}
                                                className="h-10 w-20 p-1"
                                            />
                                            <Input
                                                value={createData.color}
                                                onChange={(e) => setCreateData('color', e.target.value)}
                                                placeholder="#000000"
                                                className="uppercase"
                                                maxLength={7}
                                            />
                                        </div>
                                        {createErrors.color && <p className="text-sm text-red-500">{createErrors.color}</p>}
                                    </div>
                                    <DialogFooter>
                                        <Button type="submit" disabled={createProcessing}>
                                            Criar Tag
                                        </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </CardHeader>
                    <CardContent>
                        {tags.length === 0 ? (
                            <div className="py-8 text-center text-muted-foreground">Você ainda não possui tags cadastradas.</div>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Tag</TableHead>
                                        <TableHead>Cor</TableHead>
                                        <TableHead className="text-right">Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {tags.map((tag) => (
                                        <TableRow key={tag.id}>
                                            <TableCell>
                                                <TagBadge name={tag.name} color={tag.color} />
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <div className="h-4 w-4 rounded-full border shadow-sm" style={{ backgroundColor: tag.color }} />
                                                    <span className="text-sm text-muted-foreground">{tag.color}</span>
                                                </div>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button variant="ghost" size="sm" onClick={() => handleEditClick(tag)}>
                                                    <Edit className="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    className="text-red-500 hover:text-red-700"
                                                    onClick={() => setDeletingTag(tag)}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>

                {/* Edit Dialog */}
                <Dialog open={!!editingTag} onOpenChange={(open) => !open && setEditingTag(null)}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Editar Tag</DialogTitle>
                            <DialogDescription>Atualize as informações da tag.</DialogDescription>
                        </DialogHeader>
                        <form onSubmit={handleUpdate} className="space-y-4 py-4">
                            <div className="space-y-2">
                                <Label htmlFor="edit-name">Nome</Label>
                                <Input
                                    id="edit-name"
                                    value={editData.name}
                                    onChange={(e) => setEditData('name', e.target.value)}
                                    placeholder="Ex: Viagem, Reforma..."
                                />
                                {editErrors.name && <p className="text-sm text-red-500">{editErrors.name}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="edit-color">Cor</Label>
                                <div className="flex gap-2">
                                    <Input
                                        id="edit-color"
                                        type="color"
                                        value={editData.color}
                                        onChange={(e) => setEditData('color', e.target.value)}
                                        className="h-10 w-20 p-1"
                                    />
                                    <Input
                                        value={editData.color}
                                        onChange={(e) => setEditData('color', e.target.value)}
                                        placeholder="#000000"
                                        className="uppercase"
                                        maxLength={7}
                                    />
                                </div>
                                {editErrors.color && <p className="text-sm text-red-500">{editErrors.color}</p>}
                            </div>
                            <DialogFooter>
                                <Button type="submit" disabled={editProcessing}>
                                    Salvar Alterações
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                {/* Delete Confirmation Dialog */}
                <ConfirmDeleteDialog
                    open={!!deletingTag}
                    onOpenChange={(open) => !open && setDeletingTag(null)}
                    onConfirm={handleDelete}
                    title="Excluir Tag"
                    description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente a tag"
                    itemName={deletingTag?.name}
                />
            </div>
        </DashboardLayout>
    );
}
