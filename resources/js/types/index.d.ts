import { Subscription } from '@/types/subscription';
import { ToastInterface } from '@/types/toast';
import { Config as ZiggyConfig } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    toasts: ToastInterface[];
    unreadNotificationsCount: number;
    ziggy?: ZiggyConfig;
    [key: string]: unknown;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
        subscription: Subscription | null;
        plan_limits: any;
    };
    ziggy: Config & { location: string };
    toast?: {
        message: string;
        type: 'success' | 'error' | 'info' | 'warning';
        action?: { label: string; url: string };
        duration?: number;
    };
};

export * from './category';
export * from './savings-goal';

export interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    status: boolean;
    subscription?: Subscription | null;
    [key: string]: unknown; // This allows for additional properties...
}

declare global {
    interface Window {
        Ziggy?: ZiggyConfig;
    }
    function route(name?: undefined, params?: undefined, absolute?: boolean): Record<string, any>;
    function route(name: string, params?: Record<string, any> | any[], absolute?: boolean): string;
}
