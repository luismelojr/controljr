import { SubscriptionAlertCard } from '@/components/dashboard/subscription-alert-card';
import { MeloSysLogo } from '@/components/ui/melosys-logo';
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
import { router, usePage } from '@inertiajs/react';
import {
    ArrowLeftRightIcon,
    BanknoteArrowDownIcon,
    BanknoteArrowUpIcon,
    BarChart3,
    CircleAlert,
    FileCheck,
    Home,
    LogOut,
    PieChart,
    Tag,
    Wallet,
} from 'lucide-react';

interface MenuItemInterface {
    title: string;
    items: {
        title: string;
        icon: React.ComponentType<{ className?: string }>;
        url: string;
        badge?: number;
    }[];
}

const menuItems: MenuItemInterface[] = [
    {
        title: 'Menu',
        items: [
            { title: 'Dashboard', icon: Home, url: route('dashboard.home') },
            { title: 'Carteiras', icon: Wallet, url: route('dashboard.wallets.index') },
            { title: 'Categorias', icon: Tag, url: route('dashboard.categories.index') },
            { title: 'Contas', icon: BanknoteArrowDownIcon, url: route('dashboard.accounts.index') },
            { title: 'Receitas', icon: BanknoteArrowUpIcon, url: route('dashboard.incomes.index') },
            { title: 'Transações Contas', icon: ArrowLeftRightIcon, url: route('dashboard.transactions.index') },
            { title: 'Transações Receitas', icon: ArrowLeftRightIcon, url: route('dashboard.income-transactions.index') },
            { title: 'Orçamentos', icon: PieChart, url: route('dashboard.budgets.index') },
            { title: 'Relatórios', icon: BarChart3, url: route('dashboard.reports.index') },
            { title: 'Conciliação', icon: FileCheck, url: route('dashboard.reconciliation.index') },
            { title: 'Alertas', icon: CircleAlert, url: route('dashboard.alerts.index') },
        ],
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

/**
 * Verifica se uma rota está ativa baseado na URL atual
 * @param itemUrl - URL do item do menu
 * @param currentUrl - URL atual da página
 * @returns true se a rota está ativa
 */
function isActiveRoute(itemUrl: string, currentUrl: string): boolean {
    // Remove a base URL e query strings para comparação
    const cleanItemUrl = itemUrl.replace(window.location.origin, '').split('?')[0];
    const cleanCurrentUrl = currentUrl.split('?')[0];

    // Verifica se é uma correspondência exata
    if (cleanItemUrl === cleanCurrentUrl) {
        return true;
    }

    // Para rotas como /dashboard, não ativar se estiver em /dashboard/wallets
    if (cleanItemUrl === '/dashboard' && cleanCurrentUrl !== '/dashboard') {
        return false;
    }

    // Para outras rotas, verifica se a URL atual começa com a URL do item
    // Isso permite que /dashboard/wallets ative o item "Carteiras"
    return cleanCurrentUrl.startsWith(cleanItemUrl);
}

export function AppSidebar() {
    const { url } = usePage();

    const handleLogout = () => {
        router.post(route('logout'));
    };

    return (
        <Sidebar>
            <SidebarHeader className="border-b px-6 py-4">
                <MeloSysLogo showText />
            </SidebarHeader>

            <SidebarContent className="px-3 py-4">
                {menuItems.map((section) => (
                    <SidebarGroup key={section.title}>
                        <SidebarGroupLabel className="px-3 text-xs font-medium text-muted-foreground">{section.title}</SidebarGroupLabel>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                {section.items.map((item) => {
                                    const isActive = isActiveRoute(item.url, url);
                                    return (
                                        <SidebarMenuItem key={item.title}>
                                            <SidebarMenuButton asChild isActive={isActive} tooltip={item.title}>
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
                                    );
                                })}
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                ))}
                <SubscriptionAlertCard />
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
