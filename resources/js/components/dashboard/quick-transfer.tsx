import { Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

interface Contact {
    id: number;
    name: string;
    avatar?: string;
    initials: string;
}

interface QuickTransferProps {
    contacts: Contact[];
    className?: string;
}

export function QuickTransfer({ contacts, className }: QuickTransferProps) {
    const displayedContacts = contacts.slice(0, 5);

    return (
        <div className={cn('rounded-lg border bg-card p-6', className)}>
            <div className="mb-6 flex items-center justify-between">
                <h3 className="text-lg font-semibold">Quick transfer</h3>
                <select className="rounded-md border bg-background px-3 py-1.5 text-sm">
                    <option>This Week</option>
                    <option>This Month</option>
                </select>
            </div>

            <div className="flex items-center gap-3">
                {displayedContacts.map((contact) => (
                    <button
                        key={contact.id}
                        className="group relative flex flex-col items-center gap-2 transition-transform hover:scale-105"
                        onClick={() => {
                            // Handle contact selection
                        }}
                    >
                        {contact.id === 1 && (
                            <Badge className="absolute -top-2 -right-2 z-10 bg-primary px-2 py-0.5 text-xs">
                                Robert Fox
                            </Badge>
                        )}
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-primary/20 to-primary/5 text-sm font-semibold ring-2 ring-primary/20 group-hover:ring-primary/40">
                            {contact.avatar ? <img src={contact.avatar} alt={contact.name} className="h-full w-full rounded-full object-cover" /> : <span>{contact.initials}</span>}
                        </div>
                    </button>
                ))}

                <button className="flex h-12 w-12 items-center justify-center rounded-full border-2 border-dashed border-muted-foreground/30 text-muted-foreground transition-colors hover:border-primary hover:text-primary">
                    <Plus className="h-5 w-5" />
                </button>
            </div>
        </div>
    );
}
