import { Label } from '@/components/ui/label';
import { MultiSelect, MultiSelectOption } from '@/components/ui/multi-select';
import React from 'react';

interface TextMultiSelectProps {
    label: string;
    id: string;
    placeholder?: string;
    searchPlaceholder?: string;
    emptyText?: string;
    options: MultiSelectOption[];
    selected: string[];
    onChange: (selected: string[]) => void;
    error?: string;
    className?: string;
    required?: boolean;
}

export default function TextMultiSelect({
    label,
    id,
    placeholder = "Selecione uma ou mais opções",
    searchPlaceholder = "Pesquisar...",
    emptyText = "Nenhum resultado encontrado.",
    options,
    selected,
    onChange,
    error,
    className,
    required = false
}: TextMultiSelectProps) {
    return (
        <div className={`grid w-full items-center gap-2 ${className}`}>
            <Label htmlFor={id}>
                {label}
                {required && <span className="text-red-500 ml-1">*</span>}
            </Label>
            <div className="relative">
                <MultiSelect
                    options={options}
                    selected={selected}
                    onChange={onChange}
                    placeholder={placeholder}
                    searchPlaceholder={searchPlaceholder}
                    emptyText={emptyText}
                    className={`w-full outline-none ${error ? '!border-red-500' : ''}`}
                />
            </div>
            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    );
}
