import DashboardLayout from '@/components/layouts/dashboard-layout';
import CustomToast from '@/components/ui/custom-toast';
import { PageProps } from '@/types';
import { usePage } from '@inertiajs/react';

export default function Home() {
    const { auth } = usePage<PageProps>().props;

    const firstName = auth.user?.name ? auth.user.name.split(' ')[0] : 'User';

    return (
        <DashboardLayout title={`Welcome, ${firstName}`} subtitle="👋">
            <div className="space-y-6">
                <h1>Bem vindo</h1>
            </div>

            <CustomToast />
        </DashboardLayout>
    );
}
