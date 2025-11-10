import { Button } from '@/components/ui/button';
import { ActionButton } from '@/types/datatable';
import { ReactNode } from 'react';

interface DataTableHeaderProps {
    title: string;
    description?: string;
    actions?: ActionButton[];
    children?: ReactNode;
}

/**
 * Header component for DataTable
 * Displays title, description, and action buttons
 */
export function DataTableHeader({ title, description, actions, children }: DataTableHeaderProps) {
    return (
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            {/* Title and description */}
            <div>
                <h1 className="text-3xl font-bold tracking-tight">{title}</h1>
                {description && <p className="text-muted-foreground mt-1">{description}</p>}
            </div>

            {/* Actions */}
            {(actions || children) && (
                <div className="flex flex-wrap items-center gap-2">
                    {children}
                    {actions?.map((action, index) => (
                        <Button key={index} variant={action.variant || 'default'} onClick={action.onClick}>
                            {action.icon}
                            {action.label}
                        </Button>
                    ))}
                </div>
            )}
        </div>
    );
}
