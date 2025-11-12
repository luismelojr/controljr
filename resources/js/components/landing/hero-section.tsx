import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { ArrowRight, Sparkles } from 'lucide-react';
import { register } from '@/routes';

export function HeroSection() {
    return (
        <section className="relative overflow-hidden py-20 sm:py-32">
            {/* Background gradient */}
            <div className="absolute inset-0 -z-10 bg-gradient-to-b from-primary/5 via-background to-background" />

            {/* Grid pattern */}
            <div className="absolute inset-0 -z-10 bg-[linear-gradient(to_right,hsl(var(--border))_1px,transparent_1px),linear-gradient(to_bottom,hsl(var(--border))_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000_70%,transparent_110%)]" />

            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-3xl text-center">
                    {/* Badge */}
                    <div className="mb-8 inline-flex items-center gap-2 rounded-full border bg-background/50 px-4 py-1.5 text-sm backdrop-blur-sm">
                        <Sparkles className="h-4 w-4 text-primary" />
                        <span className="font-medium">Controle financeiro simplificado</span>
                    </div>

                    {/* Heading */}
                    <h1 className="mb-6 text-4xl font-bold tracking-tight sm:text-6xl lg:text-7xl">
                        Gerencie suas finanças com{' '}
                        <span className="bg-gradient-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                            Melosys
                        </span>
                    </h1>

                    {/* Description */}
                    <p className="mb-10 text-lg text-muted-foreground sm:text-xl">
                        Simplifique o controle das suas finanças pessoais. Acompanhe receitas, despesas e alcance seus objetivos financeiros de forma inteligente.
                    </p>

                    {/* CTA Buttons */}
                    <div className="flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <Link href={register.url()}>
                            <Button size="lg" className="group">
                                Começar gratuitamente
                                <ArrowRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                            </Button>
                        </Link>
                        <a href="#pricing">
                            <Button size="lg" variant="outline">Ver planos</Button>
                        </a>
                    </div>

                    {/* Social proof */}
                    <div className="mt-12 flex flex-col items-center gap-2 text-sm text-muted-foreground">
                        <div className="flex -space-x-2">
                            {[1, 2, 3, 4].map((i) => (
                                <div
                                    key={i}
                                    className="h-8 w-8 rounded-full border-2 border-background bg-gradient-to-br from-primary/20 to-primary/5"
                                />
                            ))}
                        </div>
                        <p>Junte-se a milhares de usuários que já controlam suas finanças</p>
                    </div>
                </div>
            </div>
        </section>
    );
}
