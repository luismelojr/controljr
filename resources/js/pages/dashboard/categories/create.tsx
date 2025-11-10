import AppHeader from '@/components/dashboard/app-header';
import FormCard from '@/components/dashboard/form-card';
import DashboardLayout from '@/components/layouts/dashboard-layout';
import { Button } from '@/components/ui/button';
import TextInput from '@/components/ui/text-input';
import { Head, router, useForm } from '@inertiajs/react';

export default function CreateCategory() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('dashboard.categories.store'));
    };

    return (
        <DashboardLayout title={'Nova Categoria'}>
            <Head title="Nova Categoria" />
            <div className={'space-y-6'}>
                <AppHeader title={'Nova categoria'} description={'Cadastre uma nova categoria'} routeBack={route('dashboard.categories.index')} />
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
