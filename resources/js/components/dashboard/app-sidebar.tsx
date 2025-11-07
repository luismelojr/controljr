import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { router } from '@inertiajs/react';
import { Home, LogOut } from 'lucide-react';

interface MenuItemInterface {
    title: string;
    items: {
        title: string;
        icon: React.ComponentType<{ className?: string }>;
        url: string;
        active?: boolean;
        badge?: number;
    }[];
}

const menuItems: MenuItemInterface[] = [
    {
        title: 'Menu',
        items: [{ title: 'Dashboard', icon: Home, url: route('dashboard.home'), active: true }],
    },
    // {
    //     title: 'Help & Settings',
    //     items: [
    //         { title: 'Settings', icon: Settings, url: '#' },
    //         { title: 'Feedback', icon: HelpCircle, url: '#' },
    //         { title: 'Help & Center', icon: HelpCircle, url: '#' },
    //     ],
    // },
];

export function AppSidebar() {
    const handleLogout = () => {
        router.post(route('logout'));
    };

    return (
        <Sidebar>
            <SidebarHeader className="border-b px-6 py-4">
                <div className="flex items-center gap-2">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                        <span className="text-sm font-bold">C</span>
                    </div>
                    <span className="text-lg font-semibold">ControlJr</span>
                </div>
            </SidebarHeader>

            <SidebarContent className="px-3 py-4">
                {menuItems.map((section) => (
                    <SidebarGroup key={section.title}>
                        <SidebarGroupLabel className="px-3 text-xs font-medium text-muted-foreground">{section.title}</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                {section.items.map((item) => (
                                    <SidebarMenuItem key={item.title}>
                                        <SidebarMenuButton asChild isActive={item.active} tooltip={item.title}>
                                            <a href={item.url} className="flex items-center gap-3 px-3">
                                                <item.icon className="h-4 w-4" />
                                                <span>{item.title}</span>
                                                {item.badge && (
                                                    <span className="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-destructive text-xs text-destructive-foreground">
                                                        {item.badge}
                                                    </span>
                                                )}
                                            </a>
                                        </SidebarMenuButton>
                                    </SidebarMenuItem>
                                ))}
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                ))}
            </SidebarContent>

            <SidebarFooter className="border-t p-3">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton onClick={handleLogout} tooltip="Logout" className={'cursor-pointer'}>
                            <LogOut className="h-4 w-4" />
                            <span>Sair</span>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarFooter>
        </Sidebar>
    );
}
