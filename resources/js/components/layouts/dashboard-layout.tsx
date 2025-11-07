import { Head } from '@inertiajs/react';
import { ReactNode } from 'react';
import { SidebarProvider } from '@/components/ui/sidebar';
import { AppSidebar } from '@/components/dashboard/app-sidebar';
import { DashboardHeader } from '@/components/dashboard/dashboard-header';

interface DashboardLayoutProps {
    children: ReactNode;
    title: string;
    subtitle?: string;
}

export default function DashboardLayout({ children, title, subtitle }: DashboardLayoutProps) {
    return (
        <>
            <Head title={title} />

            <SidebarProvider defaultOpen>
                <div className="flex min-h-screen w-full">
                    <AppSidebar />

                    <div className="flex flex-1 flex-col">
                        <DashboardHeader title={title} subtitle={subtitle} />

                        <main className="flex-1 overflow-auto bg-muted/10 p-6">{children}</main>
                    </div>
                </div>
            </SidebarProvider>
        </>
    );
}
