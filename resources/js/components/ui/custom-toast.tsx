import { SharedData } from '@/types';
import { ToastAction, ToastInterface } from '@/types/toast';
import { router, usePage } from '@inertiajs/react';
import { AlertTriangle, CheckCircle, Info, Loader2, X, XCircle } from 'lucide-react';
import { useEffect, useState } from 'react';

export default function CustomToast() {
    const page = usePage<SharedData>();
    const [toasts, setToasts] = useState<ToastInterface[]>([]);

    useEffect(() => {
        const newToasts = page.props.toasts || [];

        if (newToasts.length > 0) {
            setToasts((prev) => {
                const existingIds = prev.map((t) => t.id);
                const uniqueNewToasts = newToasts.filter((t: ToastInterface) => !existingIds.includes(t.id));
                return [...prev, ...uniqueNewToasts];
            });

            newToasts.forEach((toast: ToastInterface) => {
                if (!toast.persistent && toast.duration > 0) {
                    setTimeout(() => {
                        removeToast(toast.id);
                    }, toast.duration);
                }
            });
        }
    }, [page.props.toasts]);

    const removeToast = (id: string) => {
        setToasts((prev) => prev.filter((t) => t.id !== id));
    };

    const handleAction = (action: ToastAction) => {
        if (action.method.toUpperCase() === 'GET') {
            router.visit(action.url);
        } else {
            router.visit(action.url, {
                method: action.method.toLowerCase() as 'get' | 'post' | 'put' | 'patch' | 'delete',
                preserveScroll: true,
            });
        }
    };

    const getIcon = (type: string) => {
        switch (type) {
            case 'success':
                return <CheckCircle className="h-4 w-4" />;
            case 'error':
                return <XCircle className="h-4 w-4" />;
            case 'warning':
                return <AlertTriangle className="h-4 w-4" />;
            case 'info':
                return <Info className="h-4 w-4" />;
            case 'loading':
                return <Loader2 className="h-4 w-4 animate-spin" />;
            default:
                return <Info className="h-4 w-4" />;
        }
    };

    const getIconColor = (type: string) => {
        switch (type) {
            case 'success':
                return 'text-green-600 dark:text-green-400';
            case 'error':
                return 'text-red-600 dark:text-red-400';
            case 'warning':
                return 'text-yellow-600 dark:text-yellow-400';
            case 'info':
                return 'text-blue-600 dark:text-blue-400';
            case 'loading':
                return 'text-muted-foreground';
            default:
                return 'text-muted-foreground';
        }
    };

    const getTypeText = (type: string) => {
        switch (type) {
            case 'success':
                return 'Success notification';
            case 'error':
                return 'Error notification';
            case 'warning':
                return 'Warning notification';
            case 'info':
                return 'Informational notification';
            case 'loading':
                return 'Loading notification';
            default:
                return 'Notification';
        }
    };

    if (toasts.length === 0) return null;

    return (
        <div className="fixed top-4 right-4 z-50 w-80 space-y-2">
            {toasts.map((toast) => (
                <div
                    key={toast.id}
                    className="transform rounded-lg border border-border bg-card p-4 text-card-foreground shadow-lg transition-all duration-300 ease-out"
                    style={{
                        animation: 'slideInFromRight 0.3s ease-out forwards',
                    }}
                >
                    {/* Header with icon, type and close button */}
                    <div className="mb-2 flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <span className={getIconColor(toast.type)}>{getIcon(toast.type)}</span>
                            <span className="text-sm font-medium text-foreground">{toast.title || getTypeText(toast.type)}</span>
                        </div>
                        {toast.dismissible && (
                            <button onClick={() => removeToast(toast.id)} className="text-muted-foreground transition-colors hover:text-foreground">
                                <X className="h-4 w-4" />
                            </button>
                        )}
                    </div>

                    {/* Message */}
                    <div className="mb-3 text-sm text-foreground">
                        {toast.text}
                        {toast.description && <div className="mt-1 text-muted-foreground">{toast.description}</div>}
                    </div>

                    {/* Actions */}
                    {toast.actions && toast.actions.length > 0 && (
                        <div className="flex gap-2">
                            {toast.actions.map((action, index) => (
                                <button
                                    key={index}
                                    onClick={() => handleAction(action)}
                                    className="rounded bg-secondary px-3 py-1 text-xs text-secondary-foreground transition-colors hover:bg-secondary/80"
                                >
                                    {action.label}
                                </button>
                            ))}
                        </div>
                    )}

                    {/* Progress indicator for loading */}
                    {toast.type === 'loading' && toast.data && 'progress' in toast.data && typeof toast.data.progress === 'number' && (
                        <div className="mt-3">
                            <div className="mb-1 flex justify-between text-xs text-muted-foreground">
                                <span>Progress</span>
                                <span>{toast.data.progress as number}%</span>
                            </div>
                            <div className="h-1 w-full rounded-full bg-secondary">
                                <div
                                    className="h-1 rounded-full bg-blue-600 dark:bg-blue-400 transition-all duration-300"
                                    style={{ width: `${toast.data.progress as number}%` }}
                                ></div>
                            </div>
                        </div>
                    )}

                    {/* Non-persistent indicator */}
                    {!toast.persistent && toast.duration > 0 && (
                        <div className="mt-2">
                            <div className="h-0.5 overflow-hidden rounded-full bg-muted">
                                <div
                                    className="h-full animate-pulse rounded-full bg-primary"
                                    style={{
                                        animation: `shrink ${toast.duration}ms linear forwards`,
                                    }}
                                ></div>
                            </div>
                        </div>
                    )}
                </div>
            ))}

            <style
                dangerouslySetInnerHTML={{
                    __html: `
                    @keyframes slideInFromRight {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                    @keyframes shrink {
                        from { width: 100%; }
                        to { width: 0%; }
                    }
                `,
                }}
            />
        </div>
    );
}
