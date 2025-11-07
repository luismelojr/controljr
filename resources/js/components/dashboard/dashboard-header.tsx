import { Button } from '@/components/ui/button';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { PageProps } from '@/types';
import { usePage } from '@inertiajs/react';
import { Bell } from 'lucide-react';

interface DashboardHeaderProps {
    title: string;
    subtitle?: string;
}

export function DashboardHeader({ title, subtitle }: DashboardHeaderProps) {
    const { auth } = usePage<PageProps>().props;
    const userInitial = auth.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'U';

    return (
        <header className="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-background px-6">
            <div className="flex items-center gap-4">
                {/* Mobile Sidebar Trigger */}
                <SidebarTrigger className="lg:hidden" />

                <h1 className="text-xl font-semibold">
                    {title} {subtitle && <span className="text-2xl">🔥</span>}
                </h1>
            </div>

            <div className="flex items-center gap-4">
                {/* Notifications */}
                <Button variant="ghost" size="icon" className="relative">
                    <Bell className="h-5 w-5" />
                    <span className="absolute top-1 right-1 h-2 w-2 rounded-full bg-destructive" />
                </Button>

                {/* User Profile */}
                <div className="flex items-center gap-2">
                    <div className="hidden flex-col items-end md:flex">
                        <span className="text-sm font-medium">{auth.user?.name || 'User'}</span>
                        <span className="text-xs text-muted-foreground">Admin</span>
                    </div>
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-foreground">
                        <span className="text-sm font-medium">{userInitial}</span>
                    </div>
                </div>
            </div>
        </header>
    );
}
