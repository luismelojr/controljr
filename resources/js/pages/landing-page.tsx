import { Head } from '@inertiajs/react';
import { LandingHeader } from '@/components/landing/landing-header';
import { HeroSection } from '@/components/landing/hero-section';
import { FeaturesSection } from '@/components/landing/features-section';
import { PricingSection } from '@/components/landing/pricing-section';
import { LandingFooter } from '@/components/landing/landing-footer';
import { ThemeProvider } from '@/components/providers/theme-provider';

export default function LandingPage() {
    return (
        <ThemeProvider defaultTheme="system" storageKey="melosys-theme">
            <Head title="Melosys - Controle Financeiro Simplificado" />

            <div className="flex min-h-screen flex-col">
                <LandingHeader />

                <main className="flex-1">
                    <HeroSection />
                    <FeaturesSection />
                    <PricingSection />
                </main>

                <LandingFooter />
            </div>
        </ThemeProvider>
    );
}
