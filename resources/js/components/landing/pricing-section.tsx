import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { Check, Crown, Sparkles } from 'lucide-react';
import { register } from '@/routes';

interface PricingFeature {
    text: string;
    included: boolean;
}

interface PricingPlan {
    name: string;
    price: string;
    description: string;
    features: PricingFeature[];
    cta: string;
    popular?: boolean;
    icon: typeof Crown | typeof Sparkles;
}

const plans: PricingPlan[] = [
    {
        name: 'Free',
        price: 'Grátis',
        description: 'Perfeito para começar a organizar suas finanças',
        icon: Sparkles,
        cta: 'Começar grátis',
        features: [
            { text: 'Até 2 carteiras', included: true },
            { text: 'Controle de receitas e despesas', included: true },
            { text: 'Categorização básica', included: true },
            { text: 'Relatórios mensais', included: true },
            { text: 'Alertas personalizados', included: false },
            { text: 'Relatórios avançados', included: false },
            { text: 'Suporte prioritário', included: false },
        ],
    },
    {
        name: 'Premium',
        price: 'R$ 49,90',
        description: 'Para quem busca controle total e recursos avançados',
        icon: Crown,
        cta: 'Assinar Premium',
        popular: true,
        features: [
            { text: 'Carteiras ilimitadas', included: true },
            { text: 'Controle de receitas e despesas', included: true },
            { text: 'Categorização avançada', included: true },
            { text: 'Relatórios ilimitados', included: true },
            { text: 'Alertas personalizados', included: true },
            { text: 'Relatórios avançados com insights', included: true },
            { text: 'Suporte prioritário', included: true },
        ],
    },
];

export function PricingSection() {
    return (
        <section className="py-20 sm:py-32" id="pricing">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                {/* Section Header */}
                <div className="mx-auto max-w-2xl text-center">
                    <h2 className="mb-4 text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">
                        Escolha o plano ideal para você
                    </h2>
                    <p className="text-lg text-muted-foreground">
                        Comece gratuitamente e faça upgrade quando precisar de mais recursos
                    </p>
                </div>

                {/* Pricing Cards */}
                <div className="mt-16 grid gap-8 lg:grid-cols-2 lg:gap-12">
                    {plans.map((plan) => {
                        const Icon = plan.icon;
                        return (
                            <div
                                key={plan.name}
                                className={`relative overflow-hidden rounded-2xl border bg-card p-8 ${
                                    plan.popular
                                        ? 'border-primary shadow-xl shadow-primary/10'
                                        : 'border-border'
                                }`}
                            >
                                {/* Popular Badge */}
                                {plan.popular && (
                                    <div className="absolute right-8 top-8">
                                        <div className="rounded-full bg-primary px-3 py-1 text-xs font-semibold text-primary-foreground">
                                            Mais popular
                                        </div>
                                    </div>
                                )}

                                {/* Plan Header */}
                                <div className="mb-8">
                                    <div className="mb-4 inline-flex rounded-lg bg-primary/10 p-3 text-primary">
                                        <Icon className="h-6 w-6" />
                                    </div>
                                    <h3 className="mb-2 text-2xl font-bold">{plan.name}</h3>
                                    <p className="text-muted-foreground">{plan.description}</p>
                                </div>

                                {/* Price */}
                                <div className="mb-8">
                                    <div className="flex items-baseline gap-2">
                                        <span className="text-5xl font-bold">{plan.price}</span>
                                        {plan.name === 'Premium' && (
                                            <span className="text-muted-foreground">/mês</span>
                                        )}
                                    </div>
                                </div>

                                {/* CTA Button */}
                                <Link href={register.url()} className="mb-8">
                                    <Button
                                        className="w-full"
                                        size="lg"
                                        variant={plan.popular ? 'default' : 'outline'}
                                    >
                                        {plan.cta}
                                    </Button>
                                </Link>

                                {/* Features List */}
                                <div className="space-y-4 mt-4">
                                    {plan.features.map((feature, index) => (
                                        <div
                                            key={index}
                                            className={`flex items-start gap-3 ${
                                                !feature.included ? 'opacity-50' : ''
                                            }`}
                                        >
                                            <div
                                                className={`mt-0.5 rounded-full p-0.5 ${
                                                    feature.included
                                                        ? 'bg-primary text-primary-foreground'
                                                        : 'bg-muted text-muted-foreground'
                                                }`}
                                            >
                                                <Check className="h-4 w-4" />
                                            </div>
                                            <span
                                                className={
                                                    feature.included
                                                        ? 'text-foreground'
                                                        : 'text-muted-foreground line-through'
                                                }
                                            >
                                                {feature.text}
                                            </span>
                                        </div>
                                    ))}
                                </div>

                                {/* Background decoration */}
                                {plan.popular && (
                                    <div className="absolute inset-0 -z-10 bg-gradient-to-br from-primary/5 via-transparent to-transparent" />
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Additional Info */}
                <div className="mt-12 text-center">
                    <p className="text-sm text-muted-foreground">
                        Todos os planos incluem atualizações automáticas e segurança de dados
                    </p>
                </div>
            </div>
        </section>
    );
}
