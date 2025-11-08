import { ToastInterface } from '@/types/toast';
import { Config as ZiggyConfig } from 'ziggy-js';

export interface Auth {
    user: {
        data: User;
    };
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    toasts: ToastInterface[];
    ziggy?: ZiggyConfig;
    [key: string]: unknown;
}

export interface PageProps extends SharedData {
    errors: Record<string, string>;
}

export interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    status: boolean;
    [key: string]: unknown; // This allows for additional properties...
}

declare global {
    interface Window {
        Ziggy?: ZiggyConfig;
    }
    function route(name?: undefined, params?: undefined, absolute?: boolean): Record<string, any>;
    function route(name: string, params?: Record<string, any> | any[], absolute?: boolean): string;
}
