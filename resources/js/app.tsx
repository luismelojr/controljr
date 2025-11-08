import '../css/app.css';

import { ThemeProvider } from '@/components/providers/theme-provider';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <ThemeProvider defaultTheme={'system'} storageKey={'controljr-theme'}>
                <App {...props} />
            </ThemeProvider>,
        );
    },
    progress: {
        color: '#FAAD33',
    },
});
