import { Button } from '@/components/ui/button';
import { getFeatureLabel, getFeatureValue } from '@/lib/subscription';
import { register } from '@/routes';
import { SubscriptionPlan } from '@/types/subscription';
import { Link } from '@inertiajs/react';
import { Check, Crown, Sparkles } from 'lucide-react';

interface PricingSectionProps {
    plans: SubscriptionPlan[];
}

export function PricingSection({ plans }: PricingSectionProps) {
    return (
        <section className="py-20 sm:py-32" id="pricing">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                {/* Section Header */}
                <div className="mx-auto max-w-2xl text-center">
                    <h2 className="mb-4 text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">Escolha o plano ideal para você</h2>
                    <p className="text-lg text-muted-foreground">Comece gratuitamente e faça upgrade quando precisar de mais recursos</p>
                </div>

                {/* Pricing Cards */}
                <div className="mt-16 grid gap-8 lg:grid-cols-2 lg:gap-12">
                    {plans.map((plan) => {
                        const Icon = plan.is_premium ? Crown : Sparkles;
                        // Use "popular" visual style for premium plans
                        const isPopular = plan.is_premium;

                        return (
                            <div
                                key={plan.uuid}
                                className={`relative overflow-hidden rounded-2xl border bg-card p-8 ${
                                    isPopular ? 'border-primary shadow-xl shadow-primary/10' : 'border-border'
                                }`}
                            >
                                {/* Popular Badge */}
                                {isPopular && (
                                    <div className="absolute top-8 right-8">
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
                                        <span className="text-5xl font-bold">{plan.price_formatted}</span>
                                        {!plan.is_free && <span className="text-muted-foreground">/mês</span>}
                                    </div>
                                </div>

                                {/* CTA Button */}
                                <Link href={register.url()} className="mb-8">
                                    <Button className="w-full" size="lg" variant={isPopular ? 'default' : 'outline'}>
                                        {plan.is_free ? 'Começar grátis' : 'Assinar Premium'}
                                    </Button>
                                </Link>

                                {/* Features List */}
                                <div className="mt-4 space-y-4">
                                    {Object.entries(plan.features).map(([key, value]) => {
                                        const displayValue = getFeatureValue(value);
                                        const isAvailable = value !== 0 && value !== false;

                                        return (
                                            <div key={key} className={`flex items-start gap-3 ${!isAvailable ? 'opacity-50' : ''}`}>
                                                <div
                                                    className={`mt-0.5 rounded-full p-0.5 ${
                                                        isAvailable ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'
                                                    }`}
                                                >
                                                    <Check className="h-4 w-4" />
                                                </div>
                                                <span className={isAvailable ? 'text-foreground' : 'text-muted-foreground line-through'}>
                                                    <strong>{displayValue}</strong> {getFeatureLabel(key)}
                                                </span>
                                            </div>
                                        );
                                    })}
                                </div>

                                {/* Background decoration */}
                                {isPopular && (
                                    <div className="absolute inset-0 -z-10 bg-gradient-to-br from-primary/5 via-transparent to-transparent" />
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Additional Info */}
                <div className="mt-12 text-center">
                    <p className="text-sm text-muted-foreground">Todos os planos incluem atualizações automáticas e segurança de dados</p>
                </div>
            </div>
        </section>
    );
}
