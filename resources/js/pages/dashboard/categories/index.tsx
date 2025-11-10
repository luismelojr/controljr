import { DataTable, DataTableFilters, DataTableHeader, DataTablePagination, FilterBadges } from '@/components/datatable';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ConfirmDeleteDialog } from '@/components/ui/confirm-delete-dialog';
import { Category } from '@/types/category';
import { ColumnDef, FilterConfig, PaginatedResponse } from '@/types/datatable';
import { Head, router } from '@inertiajs/react';
import { CircleMinus, CirclePlus, Edit, Plus, Trash2 } from 'lucide-react';
import { useMemo, useState } from 'react';

interface CategoriesIndexProps {
    categories: PaginatedResponse<Category>;
    filters?: {
        filter?: Record<string, any>;
        sort?: string;
    };
}

export default function CategoriesIndex({ categories, filters }: CategoriesIndexProps) {
    const [deleteDialog, setDeleteDialog] = useState<{
        open: boolean;
        categoryUuid: string | null;
        categoryName: string;
    }>({
        open: false,
        categoryUuid: null,
        categoryName: '',
    });

    const [statusDialogOpen, setStatusDialogOpen] = useState<{
        open: boolean;
        categoryUuid: string | null;
        categoryName: string;
    }>({
        open: false,
        categoryUuid: null,
        categoryName: '',
    });

    /**
     * Parse active filters from URL params
     */
    const activeFilters = useMemo(() => {
        return filters?.filter || {};
    }, [filters]);

    /**
     * Parse active sort from URL params
     */
    const activeSort = useMemo(() => {
        const sortValue = filters?.sort;

        if (!sortValue || typeof sortValue !== 'string') {
            return { key: '', direction: null as 'asc' | 'desc' | null };
        }

        const isDescending = sortValue.startsWith('-');
        const key = isDescending ? sortValue.slice(1) : sortValue;
        const direction = isDescending ? ('desc' as const) : ('asc' as const);

        return { key, direction };
    }, [filters]);

    /**
     * Filter configuration for Spatie Query Builder
     */
    const filterConfigs: FilterConfig[] = [
        {
            key: 'name',
            label: 'Nome',
            type: 'text',
            placeholder: 'Buscar por nome...',
        },
        {
            key: 'status',
            label: 'Status',
            type: 'boolean',
            options: [
                { value: '1', label: 'Ativo' },
                { value: '0', label: 'Inativo' },
            ],
        },
        {
            key: 'is_default',
            label: 'Tipo',
            type: 'boolean',
            options: [
                { value: '1', label: 'Padrão do Sistema' },
                { value: '0', label: 'Criada por Mim' },
            ],
        },
    ];

    /**
     * Column definitions for DataTable
     */
    const columns: ColumnDef<Category>[] = [
        {
            key: 'name',
            label: 'Nome',
            sortable: true,
            render: (category) => (
                <div className="flex items-center gap-2">
                    <span className="font-medium">{category.name}</span>
                    {category.is_default && (
                        <Badge variant="secondary" className="text-xs">
                            Padrão
                        </Badge>
                    )}
                </div>
            ),
        },
        {
            key: 'status',
            label: 'Status',
            sortable: true,
            render: (category) => <Badge variant={category.status ? 'default' : 'destructive'}>{category.status ? 'Ativo' : 'Inativo'}</Badge>,
        },
        {
            key: 'created_at',
            label: 'Criado em',
            sortable: true,
            render: (category) => new Date(category.created_at).toLocaleDateString('pt-BR'),
        },
        {
            key: 'actions',
            label: 'Ações',
            className: 'text-right',
            render: (category) => (
                <div className="flex items-center justify-end gap-2">
                    {category.can_edit && (
                        <>
                            <Button variant="ghost" size="icon-sm" onClick={() => handleEdit(category.uuid)} title="Editar categoria">
                                <Edit className="h-4 w-4" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon-sm"
                                onClick={() => handleStatus(category.uuid, category.name)}
                                title={`${category.status ? 'Desativar' : 'Ativar'} categoria`}
                            >
                                {category.status ? (
                                    <CircleMinus className={'text-warning h-4 w-4'} />
                                ) : (
                                    <CirclePlus className={'text-warning h-4 w-4'} />
                                )}
                            </Button>
                        </>
                    )}
                    {category.can_delete && (
                        <Button variant="ghost" size="icon-sm" onClick={() => handleDelete(category.uuid, category.name)} title="Excluir categoria">
                            <Trash2 className="h-4 w-4 text-destructive" />
                        </Button>
                    )}
                    {!category.can_edit && !category.can_delete && <span className="text-xs text-muted-foreground">Sem ações</span>}
                </div>
            ),
        },
    ];

    /**
     * Navigate to create page
     */
    const handleCreate = () => {
        router.get(route('dashboard.categories.create'));
    };

    /**
     * Navigate to edit page
     */
    const handleEdit = (uuid: string) => {
        router.get(route('dashboard.categories.edit', { category: uuid }));
    };

    /**
     * Open delete confirmation dialog
     */
    const handleDelete = (uuid: string, name: string) => {
        setDeleteDialog({
            open: true,
            categoryUuid: uuid,
            categoryName: name,
        });
    };

    const handleStatus = (uuid: string, name: string) => {
        setStatusDialogOpen({
            open: true,
            categoryUuid: uuid,
            categoryName: name,
        });
    };

    /**
     * Confirm delete action
     */
    const confirmDelete = () => {
        if (deleteDialog.categoryUuid) {
            router.delete(route('dashboard.categories.destroy', { category: deleteDialog.categoryUuid }), {
                preserveScroll: true,
                onSuccess: () => {
                    setDeleteDialog({ open: false, categoryUuid: null, categoryName: '' });
                },
            });
        }
    };

    const confirmStatus = () => {
        if (statusDialogOpen.categoryUuid) {
            router.patch(
                route('dashboard.categories.toggle-status', { category: statusDialogOpen.categoryUuid }),
                {
                    preserveScroll: true,
                },
                {
                    onSuccess: () => {
                        setStatusDialogOpen({ open: false, categoryUuid: null, categoryName: '' });
                    },
                },
            );
        }
    };

    return (
        <DashboardLayout title="Categorias">
            <Head title="Categorias" />

            <div className="space-y-6">
                {/* Header */}
                <DataTableHeader
                    title="Categorias"
                    description="Gerencie suas categorias de receitas e despesas"
                    actions={[
                        {
                            label: 'Nova Categoria',
                            onClick: handleCreate,
                            icon: <Plus className="h-4 w-4" />,
                            variant: 'default',
                        },
                    ]}
                />

                {/* Filters and Active Filters */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <DataTableFilters filters={filterConfigs} activeFilters={activeFilters} currentSort={filters?.sort} />

                    <FilterBadges filters={activeFilters} filterConfigs={filterConfigs} currentSort={filters?.sort} />
                </div>

                {/* DataTable */}
                <DataTable data={categories.data} columns={columns} activeSort={activeSort} currentFilters={activeFilters} />

                {/* Pagination */}
                {categories.meta && categories.links && <DataTablePagination meta={categories.meta} links={categories.links} />}
            </div>

            {/* Delete Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={deleteDialog.open}
                onOpenChange={(open) => setDeleteDialog((prev) => ({ ...prev, open }))}
                onConfirm={confirmDelete}
                title="Excluir categoria?"
                description="Esta ação não pode ser desfeita. Isso irá excluir permanentemente a categoria"
                itemName={deleteDialog.categoryName}
                confirmText="Excluir"
                cancelText="Cancelar"
            />

            {/* Status Confirmation Dialog */}
            <ConfirmDeleteDialog
                open={statusDialogOpen.open}
                onOpenChange={(open) => setStatusDialogOpen((prev) => ({ ...prev, open }))}
                onConfirm={confirmStatus}
                title={`${statusDialogOpen.categoryName} categoria?`}
                description="Esta ação irá alterar o status da categoria"
                itemName={statusDialogOpen.categoryName}
                confirmText="Confirmar"
                cancelText="Cancelar"
            />
        </DashboardLayout>
    );
}
