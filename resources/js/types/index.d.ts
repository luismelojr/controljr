import { ToastInterface } from '@/types/toast';

export interface Auth {
    user: User;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    toasts: ToastInterface[];
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    status: boolean;
    [key: string]: unknown; // This allows for additional properties...
}
