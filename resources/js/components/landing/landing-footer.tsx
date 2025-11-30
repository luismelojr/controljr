import { Github, Twitter, Linkedin, Heart } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { home, register } from '@/routes';
import { MeloSysLogo } from '@/components/ui/melosys-logo';
import React from 'react';

export function LandingFooter() {
    const currentYear = new Date().getFullYear();

    return (
        <footer className="border-t bg-background">
            <div className="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
                {/* Main Footer Content */}
                <div className="grid gap-8 lg:grid-cols-4">
                    {/* Brand Section */}
                    <div className="lg:col-span-2">
                        <Link href={home.url()} className="mb-4 flex items-center">
                            <MeloSysLogo className="h-10 w-10" showText />
                        </Link>
                        <p className="mb-4 max-w-md text-sm text-muted-foreground">
                            Simplifique o controle das suas finanças pessoais. Acompanhe receitas, despesas e alcance seus objetivos financeiros de forma inteligente.
                        </p>
                        {/* Social Links */}
                        <div className="flex gap-4">
                            <a
                                href="#"
                                className="text-muted-foreground transition-colors hover:text-foreground"
                                aria-label="Twitter"
                            >
                                <Twitter className="h-5 w-5" />
                            </a>
                            <a
                                href="#"
                                className="text-muted-foreground transition-colors hover:text-foreground"
                                aria-label="GitHub"
                            >
                                <Github className="h-5 w-5" />
                            </a>
                            <a
                                href="#"
                                className="text-muted-foreground transition-colors hover:text-foreground"
                                aria-label="LinkedIn"
                            >
                                <Linkedin className="h-5 w-5" />
                            </a>
                        </div>
                    </div>

                    {/* Product Links */}
                    <div>
                        <h3 className="mb-4 text-sm font-semibold">Produto</h3>
                        <ul className="space-y-3 text-sm">
                            <li>
                                <a
                                    href="#features"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Recursos
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#pricing"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Planos
                                </a>
                            </li>
                            <li>
                                <Link
                                    href={register.url()}
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Começar grátis
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Legal Links */}
                    <div>
                        <h3 className="mb-4 text-sm font-semibold">Legal</h3>
                        <ul className="space-y-3 text-sm">
                            <li>
                                <a
                                    href="#"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Privacidade
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Termos de Uso
                                </a>
                            </li>
                            <li>
                                <a
                                    href="#"
                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Cookies
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {/* Bottom Bar */}
                <div className="mt-12 flex flex-col items-center justify-between gap-4 border-t pt-8 sm:flex-row">
                    <p className="text-sm text-muted-foreground">
                        © {currentYear} Melosys. Todos os direitos reservados.
                    </p>
                    <p className="flex items-center gap-1 text-sm text-muted-foreground">
                        Feito com <Heart className="h-4 w-4 fill-red-500 text-red-500" /> no Brasil
                    </p>
                </div>
            </div>
        </footer>
    );
}
