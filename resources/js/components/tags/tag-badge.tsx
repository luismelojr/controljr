import { cn } from '@/lib/utils';
import { X } from 'lucide-react';

interface TagBadgeProps {
    name: string;
    color: string;
    onRemove?: () => void;
    className?: string; // Add className prop for flexibility
}

export function TagBadge({ name, color, onRemove, className }: TagBadgeProps) {
    return (
        <span
            className={cn('inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium', className)}
            style={{ backgroundColor: color + '20', color: color }}
        >
            {name}
            {onRemove && (
                <button
                    type="button" // Ensure it doesn't submit forms
                    onClick={(e) => {
                        e.stopPropagation(); // Prevent triggering parent clicks
                        onRemove();
                    }}
                    className="hover:opacity-70 focus:outline-none"
                >
                    <X className="h-3 w-3" />
                </button>
            )}
        </span>
    );
}
