import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ModeToggle } from '@/components/ui/mode-toggle';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { PageProps } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { Bell, LogOut, Settings, User } from 'lucide-react';

interface DashboardHeaderProps {
    title: string;
    subtitle?: string;
}

export function DashboardHeader({ title, subtitle }: DashboardHeaderProps) {
    const { auth, unreadNotificationsCount } = usePage<PageProps>().props;
    const userInitial = auth.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'U';

    const handleLogout = () => {
        router.post(route('logout'));
    };

    const handleNotificationsClick = () => {
        router.get(route('dashboard.notifications.index'));
    };

    return (
        <header className="sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-background px-6">
            <div className="flex items-center gap-4">
                {/* Mobile Sidebar Trigger */}
                <SidebarTrigger />

                <h1 className="text-xl font-semibold">
                    {title} {subtitle && <span className="text-2xl">🔥</span>}
                </h1>
            </div>

            <div className="flex items-center gap-4">
                <ModeToggle />
                {/* Notifications */}
                <Button variant="ghost" size="icon" className="relative" onClick={handleNotificationsClick}>
                    <Bell className="h-5 w-5" />
                    {unreadNotificationsCount > 0 && (
                        <Badge variant="destructive" className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center p-0 text-xs">
                            {unreadNotificationsCount > 99 ? '99+' : unreadNotificationsCount}
                        </Badge>
                    )}
                </Button>

                {/* User Profile Dropdown */}
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <button className="flex cursor-pointer items-center gap-2 outline-none focus:outline-none">
                            <div className="hidden flex-col items-end md:flex">
                                <span className="text-sm font-medium">{auth.user?.name || 'User'}</span>
                                <span className="text-xs text-muted-foreground">Admin</span>
                            </div>
                            <div className="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full bg-primary text-primary-foreground transition-opacity hover:opacity-80">
                                <span className="text-sm font-medium">{userInitial}</span>
                            </div>
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent className="w-56" align="end" forceMount>
                        <DropdownMenuLabel className="font-normal">
                            <div className="flex flex-col space-y-1">
                                <p className="text-sm leading-none font-medium">{auth.user?.name || 'User'}</p>
                                <p className="text-xs leading-none text-muted-foreground">{auth.user?.email || 'user@example.com'}</p>
                            </div>
                        </DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuGroup>
                            <DropdownMenuItem className="cursor-pointer">
                                <User className="mr-2 h-4 w-4" />
                                <span>Editar Perfil</span>
                            </DropdownMenuItem>
                            <DropdownMenuItem className="cursor-pointer">
                                <Settings className="mr-2 h-4 w-4" />
                                <span>Configurações</span>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem className="cursor-pointer" onClick={handleLogout}>
                            <LogOut className="mr-2 h-4 w-4" />
                            <span>Sair</span>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </header>
    );
}
