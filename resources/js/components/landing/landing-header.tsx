import { Button } from '@/components/ui/button';
import { Link, usePage } from '@inertiajs/react';
import { home, login, register } from '@/routes';
import { home as dashboardHome } from '@/routes/dashboard';
import { LogIn, LayoutDashboard } from 'lucide-react';
import { MeloSysLogo } from '@/components/ui/melosys-logo';
import React from 'react';
import { User } from '@/types';

export function LandingHeader() {
    const { auth } = usePage().props as unknown as { auth: { user: User | null } };
    const isAuthenticated = !!auth.user;

    return (
        <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div className="container mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                {/* Logo */}
                <Link href={home.url()} className="flex items-center">
                    <MeloSysLogo className="h-10 w-10" showText />
                </Link>

                {/* Navigation */}
                <nav className="hidden items-center gap-6 md:flex">
                    <a
                        href="#features"
                        className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Recursos
                    </a>
                    <a
                        href="#pricing"
                        className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Planos
                    </a>
                </nav>

                {/* Auth Buttons */}
                <div className="flex items-center gap-2">
                    {isAuthenticated ? (
                        <Link href={dashboardHome.url()}>
                            <Button size="sm">
                                <LayoutDashboard className="mr-2 h-4 w-4" />
                                Ir para Dashboard
                            </Button>
                        </Link>
                    ) : (
                        <>
                            <Link href={login.url()}>
                                <Button variant="ghost" size="sm">
                                    <LogIn className="mr-2 h-4 w-4" />
                                    Entrar
                                </Button>
                            </Link>
                            <Link href={register.url()}>
                                <Button size="sm">Começar grátis</Button>
                            </Link>
                        </>
                    )}
                </div>
            </div>
        </header>
    );
}
