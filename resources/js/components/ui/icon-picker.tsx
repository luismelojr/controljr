import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { Check } from 'lucide-react';
import { useState } from 'react';

interface IconPickerProps {
    value: string;
    onChange: (value: string) => void;
    className?: string;
}

// Popular icons for savings goals
const SAVINGS_ICONS = [
    { value: '🎯', label: 'Alvo' },
    { value: '💰', label: 'Dinheiro' },
    { value: '🏠', label: 'Casa' },
    { value: '🚗', label: 'Carro' },
    { value: '✈️', label: 'Viagem' },
    { value: '🎓', label: 'Educação' },
    { value: '💍', label: 'Casamento' },
    { value: '👶', label: 'Bebê' },
    { value: '🏖️', label: 'Férias' },
    { value: '💻', label: 'Tecnologia' },
    { value: '📱', label: 'Celular' },
    { value: '🎮', label: 'Games' },
    { value: '🏃', label: 'Esporte' },
    { value: '🎸', label: 'Instrumento' },
    { value: '📚', label: 'Livros' },
    { value: '🎨', label: 'Arte' },
    { value: '🏥', label: 'Saúde' },
    { value: '🦷', label: 'Dentista' },
    { value: '👗', label: 'Roupa' },
    { value: '💄', label: 'Beleza' },
    { value: '🎉', label: 'Festa' },
    { value: '🎁', label: 'Presente' },
    { value: '🌟', label: 'Estrela' },
    { value: '💎', label: 'Diamante' },
    { value: '⭐', label: 'Estrela2' },
    { value: '🔥', label: 'Fogo' },
    { value: '🌈', label: 'Arco-íris' },
    { value: '🎪', label: 'Circo' },
    { value: '🎭', label: 'Teatro' },
    { value: '🎬', label: 'Cinema' },
];

export function IconPicker({ value, onChange, className }: IconPickerProps) {
    const [open, setOpen] = useState(false);

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    className={cn('h-10 justify-between', className)}
                >
                    <span className="text-2xl">{value || '🎯'}</span>
                    <Check className={cn('ml-2 h-4 w-4 shrink-0 opacity-50')} />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-[300px] p-0" align="start">
                <Command>
                    <CommandInput placeholder="Buscar ícone..." />
                    <CommandList>
                        <CommandEmpty>Nenhum ícone encontrado.</CommandEmpty>
                        <CommandGroup>
                            <div className="grid grid-cols-6 gap-1 p-2">
                                {SAVINGS_ICONS.map((icon) => (
                                    <CommandItem
                                        key={icon.value}
                                        value={icon.label}
                                        onSelect={() => {
                                            onChange(icon.value);
                                            setOpen(false);
                                        }}
                                        className="flex h-12 w-12 cursor-pointer items-center justify-center rounded-md hover:bg-accent"
                                    >
                                        <span
                                            className={cn('text-2xl transition-transform hover:scale-125', value === icon.value && 'scale-125')}
                                            title={icon.label}
                                        >
                                            {icon.value}
                                        </span>
                                    </CommandItem>
                                ))}
                            </div>
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
