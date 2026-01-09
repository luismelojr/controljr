import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { SidebarGroup, SidebarGroupContent } from '@/components/ui/sidebar';
import { PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Sparkles } from 'lucide-react';

export function SubscriptionAlertCard() {
    const { auth } = usePage<PageProps>().props;
    const user = auth.user;

    if (!user) {
        return null;
    }

    // Se o usuário já tem uma assinatura ativa premium, não mostra o card
    if (user.subscription && user.subscription.plan.is_premium && user.subscription.is_active) {
        return null;
    }

    return (
        <SidebarGroup className="mt-auto group-data-[collapsible=icon]:hidden">
            <SidebarGroupContent>
                <Card className="border-primary/20 bg-primary/5 shadow-none">
                    <CardHeader className="p-4 pb-2">
                        <CardTitle className="flex items-center gap-2 text-sm font-semibold text-primary">
                            <Sparkles className="h-4 w-4" />
                            Faça um upgrade
                        </CardTitle>
                        <CardDescription className="text-xs">Desbloqueie todos os recursos e tenha controle total.</CardDescription>
                    </CardHeader>
                    <CardContent className="p-4 pt-2">
                        <Link href={route('dashboard.subscription.index')} className={buttonVariants({ size: 'sm', className: 'w-full text-xs' })}>
                            Assinar Agora
                        </Link>
                    </CardContent>
                </Card>
            </SidebarGroupContent>
        </SidebarGroup>
    );
}
