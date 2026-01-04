import React from 'react';
import '../css/app.css';

import { ThemeProvider } from '@/components/providers/theme-provider';
import BugsnagPerformance from '@bugsnag/browser-performance';
import Bugsnag from '@bugsnag/js';
import BugsnagPluginReact from '@bugsnag/plugin-react';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

Bugsnag.start({
    apiKey: '5c8d6d5779bcd0b899b150d57d032974',
    plugins: [new BugsnagPluginReact()],
});

BugsnagPerformance.start({ apiKey: '5c8d6d5779bcd0b899b150d57d032974' });

const ErrorBoundary = Bugsnag.getPlugin('react')!.createErrorBoundary(React);

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <ErrorBoundary>
                <ThemeProvider defaultTheme={'system'} storageKey={'controljr-theme'}>
                    <App {...props} />
                </ThemeProvider>
            </ErrorBoundary>,
        );
    },
    progress: {
        color: '#FAAD33',
    },
});
