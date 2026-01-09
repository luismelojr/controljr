import { FeaturesSection } from '@/components/landing/features-section';
import { HeroSection } from '@/components/landing/hero-section';
import { LandingFooter } from '@/components/landing/landing-footer';
import { LandingHeader } from '@/components/landing/landing-header';
import { PricingSection } from '@/components/landing/pricing-section';
import { ThemeProvider } from '@/components/providers/theme-provider';
import { SubscriptionPlan } from '@/types/subscription';
import { Head } from '@inertiajs/react';

interface LandingPageProps {
    plans: SubscriptionPlan[];
}

export default function LandingPage({ plans }: LandingPageProps) {
    return (
        <ThemeProvider defaultTheme="system" storageKey="melosys-theme">
            <Head title="Melosys - Controle Financeiro Simplificado" />

            <div className="flex min-h-screen flex-col">
                <LandingHeader />

                <main className="flex-1">
                    <HeroSection />
                    <FeaturesSection />
                    <PricingSection plans={plans} />
                </main>

                <LandingFooter />
            </div>
        </ThemeProvider>
    );
}
