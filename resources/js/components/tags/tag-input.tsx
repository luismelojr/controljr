import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { Check } from 'lucide-react';
import { useState } from 'react';
import { TagBadge } from './tag-badge';

/**
 * Simplified Tag object for the input
 */
export interface TagOption {
    id?: number;
    name: string;
    color: string;
}

interface TagInputProps {
    value?: TagOption[]; // Current selected tags
    onChange: (tags: TagOption[]) => void;
    suggestions: TagOption[]; // Available tags to select from
    placeholder?: string;
    maxTags?: number; // Optional limit
    allowCreate?: boolean; // If true, shows create option (could be handled externally)
    onCreate?: (name: string) => void;
}

export function TagInput({
    value = [],
    onChange,
    suggestions = [],
    placeholder = 'Select tags...',
    maxTags,
    allowCreate = false,
    onCreate,
}: TagInputProps) {
    const [open, setOpen] = useState(false);
    const [inputValue, setInputValue] = useState('');

    const handleSelect = (tag: TagOption) => {
        // Check if already selected
        const isSelected = value.some((t) => t.name === tag.name);

        if (isSelected) {
            onChange(value.filter((t) => t.name !== tag.name));
        } else {
            if (maxTags && value.length >= maxTags) return;
            onChange([...value, tag]);
        }
    };

    const handleRemove = (tagName: string) => {
        onChange(value.filter((t) => t.name !== tagName));
    };

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <div
                    className="flex min-h-[40px] w-full cursor-text flex-wrap gap-2 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                    onClick={() => setOpen(true)}
                >
                    {value.length > 0 ? (
                        value.map((tag) => <TagBadge key={tag.name} name={tag.name} color={tag.color} onRemove={() => handleRemove(tag.name)} />)
                    ) : (
                        <span className="text-muted-foreground">{placeholder}</span>
                    )}
                </div>
            </PopoverTrigger>
            <PopoverContent className="w-[300px] p-0" align="start">
                <Command>
                    <CommandInput placeholder="Search tags..." value={inputValue} onValueChange={setInputValue} />
                    <CommandList>
                        <CommandEmpty>No tag found.</CommandEmpty>
                        <CommandGroup heading="Available Tags">
                            {suggestions.map((tag) => {
                                const isSelected = value.some((t) => t.name === tag.name);
                                return (
                                    <CommandItem key={tag.name} value={tag.name} onSelect={() => handleSelect(tag)}>
                                        <div className="mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary">
                                            <Check className={cn('h-3 w-3', isSelected ? 'opacity-100' : 'opacity-0')} />
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <div className="h-3 w-3 rounded-full" style={{ backgroundColor: tag.color }} />
                                            <span>{tag.name}</span>
                                        </div>
                                    </CommandItem>
                                );
                            })}
                        </CommandGroup>
                        {allowCreate && inputValue && !suggestions.some((s) => s.name.toLowerCase() === inputValue.toLowerCase()) && (
                            <CommandGroup heading="Create">
                                <CommandItem
                                    onSelect={() => {
                                        if (onCreate) onCreate(inputValue);
                                        setInputValue('');
                                    }}
                                >
                                    Create "{inputValue}"
                                </CommandItem>
                            </CommandGroup>
                        )}
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
