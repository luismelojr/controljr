export interface ToastInterface {
    id: string;
    type: 'success' | 'error' | 'warning' | 'info' | 'loading' | string;
    title?: string;
    text: string;
    description?: string;
    icon?: string;
    duration: number;
    position: 'top-left' | 'top-center' | 'top-right' | 'bottom-left' | 'bottom-center' | 'bottom-right';
    dismissible: boolean;
    persistent: boolean;
    timestamp: string;
    actions?: ToastAction[];
    data?: Record<string, unknown>;
    sound?: string;
}

export interface ToastAction {
    label: string;
    url: string;
    method: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
}

export interface ToastConfig {
    maxToasts: number;
    defaultPosition: string;
    defaultDuration: number;
    soundsEnabled: boolean;
}
