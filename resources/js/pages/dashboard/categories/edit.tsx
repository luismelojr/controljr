import AppHeader from '@/components/dashboard/app-header';
import FormCard from '@/components/dashboard/form-card';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import TextInput from '@/components/ui/text-input';
import { Category } from '@/types/category';
import { Head, router, useForm } from '@inertiajs/react';

interface EditCategoryProps {
    category: Category;
}
export default function EditCategory({ category }: EditCategoryProps) {
    const { data, setData, put, processing, errors } = useForm({
        name: category.name,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('dashboard.categories.update', { category: category.uuid }));
    };

    return (
        <DashboardLayout title={'Editar Categoria'}>
            <Head title="Editar Categoria" />
            <div className={'space-y-6'}>
                <AppHeader
                    title={'Editar categoria'}
                    description={'Atualize as informações da sua categoria'}
                    routeBack={route('dashboard.categories.index')}
                />
                <FormCard>
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <TextInput
                            label={'Nome da Categoria'}
                            type={'text'}
                            id={'category'}
                            placeholder={'Ex: Alimentação, Transporte, Lazer'}
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                        />
                        <div className="flex justify-end gap-4">
                            <Button type="button" variant="outline" onClick={() => router.get(route('dashboard.categories.index'))}>
                                Cancelar
                            </Button>
                            <Button type="submit" loading={processing}>
                                Salvar Categoria
                            </Button>
                        </div>
                    </form>
                </FormCard>
            </div>
        </DashboardLayout>
    );
}
