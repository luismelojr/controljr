import CustomToast from '@/components/ui/custom-toast';
import { MeloSysLogo } from '@/components/ui/melosys-logo';
import { Head } from '@inertiajs/react';
import React from 'react';

interface AuthLayoutProps {
    children: React.ReactNode;
    title: string;
}

export default function AuthLayout({ children, title }: AuthLayoutProps) {
    return (
        <>
            <Head title={title} />
            <div className="flex min-h-screen">
                {/* Left Side - Decorative SVG */}
                <div className="relative hidden overflow-hidden bg-gradient-to-br from-primary via-primary/90 to-primary/80 p-12 lg:flex lg:w-1/2">
                    {/* Background Pattern */}
                    <div className="absolute inset-0 opacity-10">
                        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" strokeWidth="0.5" />
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#grid)" />
                        </svg>
                    </div>

                    <div className="relative z-10 flex w-full flex-col justify-between">
                        {/* Logo/Brand */}
                        <div>
                            <div className="mb-8">
                                <MeloSysLogo className="h-10 w-10" showText />
                            </div>
                        </div>

                        {/* Main Illustration */}
                        <div className="flex flex-1 items-center justify-center">
                            <svg viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg" className="w-full max-w-md">
                                {/* Abstract Shapes */}
                                <g opacity="0.9">
                                    {/* Large Circle */}
                                    <circle cx="300" cy="300" r="200" stroke="white" strokeWidth="2" fill="none" opacity="0.3" />
                                    <circle cx="300" cy="300" r="150" stroke="white" strokeWidth="2" fill="none" opacity="0.5" />

                                    {/* Floating Cards */}
                                    <g transform="translate(150, 150)">
                                        <rect x="0" y="0" width="120" height="80" rx="8" fill="white" opacity="0.95" />
                                        <rect x="12" y="12" width="40" height="8" rx="4" fill="black" opacity="0.7" />
                                        <rect x="12" y="28" width="96" height="4" rx="2" fill="black" opacity="0.15" />
                                        <rect x="12" y="38" width="80" height="4" rx="2" fill="black" opacity="0.15" />
                                        <rect x="12" y="48" width="70" height="4" rx="2" fill="black" opacity="0.15" />
                                    </g>

                                    <g transform="translate(330, 220)">
                                        <rect x="0" y="0" width="120" height="80" rx="8" fill="white" opacity="0.95" />
                                        <circle cx="30" cy="30" r="18" fill="black" opacity="0.7" />
                                        <rect x="54" y="18" width="54" height="6" rx="3" fill="black" opacity="0.15" />
                                        <rect x="54" y="30" width="40" height="6" rx="3" fill="black" opacity="0.15" />
                                        <rect x="12" y="54" width="96" height="4" rx="2" fill="black" opacity="0.1" />
                                        <rect x="12" y="64" width="80" height="4" rx="2" fill="black" opacity="0.1" />
                                    </g>

                                    <g transform="translate(200, 340)">
                                        <rect x="0" y="0" width="120" height="80" rx="8" fill="white" opacity="0.95" />
                                        <rect x="12" y="12" width="96" height="32" rx="4" fill="black" opacity="0.2" />
                                        <rect x="12" y="52" width="40" height="16" rx="4" fill="black" opacity="0.6" />
                                        <rect x="58" y="52" width="50" height="16" rx="4" fill="black" opacity="0.15" />
                                    </g>

                                    {/* Connection Lines */}
                                    <line x1="210" y1="230" x2="330" y2="260" stroke="white" strokeWidth="2" opacity="0.4" strokeDasharray="5,5" />
                                    <line x1="270" y1="230" x2="260" y2="340" stroke="white" strokeWidth="2" opacity="0.4" strokeDasharray="5,5" />
                                    <line x1="390" y1="300" x2="320" y2="380" stroke="white" strokeWidth="2" opacity="0.4" strokeDasharray="5,5" />

                                    {/* Decorative Dots */}
                                    <circle cx="180" cy="200" r="4" fill="white" opacity="0.8" />
                                    <circle cx="420" cy="250" r="4" fill="white" opacity="0.8" />
                                    <circle cx="240" cy="420" r="4" fill="white" opacity="0.8" />
                                    <circle cx="350" cy="380" r="4" fill="white" opacity="0.8" />
                                </g>
                            </svg>
                        </div>

                        {/* Bottom Text */}
                        <div className="text-white">
                            <h2 className="mb-3 text-3xl font-bold">Gerencie Tudo em Um Só Lugar</h2>
                            <p className="text-lg text-white/80">
                                Um boilerplate moderno para iniciar seu próximo projeto com recursos poderosos e design elegante.
                            </p>
                        </div>
                    </div>

                    {/* Decorative Circles */}
                    <div className="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
                    <div className="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-black/10 blur-3xl"></div>
                </div>

                {/* Right Side - Form */}
                <div className="flex flex-1 items-center justify-center bg-background p-8">
                    <div className="w-full max-w-md">{children}</div>
                </div>
            </div>
            <CustomToast />
        </>
    );
}
