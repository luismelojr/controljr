import {
    Wallet,
    TrendingUp,
    Bell,
    PieChart,
    Calendar,
    Shield,
    type LucideIcon,
} from 'lucide-react';

interface Feature {
    icon: LucideIcon;
    title: string;
    description: string;
}

const features: Feature[] = [
    {
        icon: Wallet,
        title: 'Múltiplas Carteiras',
        description: 'Gerencie múltiplas contas e carteiras em um único lugar. Visualize seu patrimônio total de forma consolidada.',
    },
    {
        icon: TrendingUp,
        title: 'Acompanhamento de Receitas',
        description: 'Registre e acompanhe todas as suas fontes de renda. Visualize o crescimento do seu patrimônio ao longo do tempo.',
    },
    {
        icon: PieChart,
        title: 'Relatórios Detalhados',
        description: 'Gráficos e relatórios intuitivos para entender para onde seu dinheiro está indo e como otimizar seus gastos.',
    },
    {
        icon: Calendar,
        title: 'Controle de Despesas',
        description: 'Categorize suas despesas, acompanhe gastos recorrentes e identifique oportunidades de economia.',
    },
    {
        icon: Bell,
        title: 'Alertas Inteligentes',
        description: 'Receba notificações sobre vencimentos, metas atingidas e gastos acima do esperado.',
    },
    {
        icon: Shield,
        title: 'Segurança Total',
        description: 'Seus dados financeiros protegidos com criptografia de ponta a ponta e autenticação segura.',
    },
];

export function FeaturesSection() {
    return (
        <section className="py-20 sm:py-32" id="features">
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                {/* Section Header */}
                <div className="mx-auto max-w-2xl text-center">
                    <h2 className="mb-4 text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">
                        Tudo que você precisa para{' '}
                        <span className="text-primary">organizar suas finanças</span>
                    </h2>
                    <p className="text-lg text-muted-foreground">
                        Ferramentas poderosas e intuitivas para você ter controle total sobre seu dinheiro
                    </p>
                </div>

                {/* Features Grid */}
                <div className="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    {features.map((feature, index) => (
                        <div
                            key={index}
                            className="group relative overflow-hidden rounded-lg border bg-card p-6 transition-all hover:border-primary/50 hover:shadow-lg"
                        >
                            {/* Icon */}
                            <div className="mb-4 inline-flex rounded-lg bg-primary/10 p-3 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                                <feature.icon className="h-6 w-6" />
                            </div>

                            {/* Content */}
                            <h3 className="mb-2 text-xl font-semibold">{feature.title}</h3>
                            <p className="text-muted-foreground">{feature.description}</p>

                            {/* Hover effect */}
                            <div className="absolute inset-0 -z-10 bg-gradient-to-br from-primary/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100" />
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
