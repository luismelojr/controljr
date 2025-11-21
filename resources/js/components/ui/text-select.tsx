import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import React from 'react';

interface SelectOption {
    value: string;
    label: string;
}

interface TextSelectProps {
    label: string;
    id: string;
    placeholder?: string;
    options: SelectOption[];
    value?: string;
    onValueChange?: (value: string) => void;
    error?: string;
    className?: string;
    required?: boolean;
    disabled?: boolean;
}

export default function TextSelect({
    label,
    id,
    placeholder = "Selecione uma opção",
    options,
    value,
    onValueChange,
    error,
    className,
    required = false,
    disabled = false
}: TextSelectProps) {
    return (
        <div className={`grid w-full items-center gap-2 ${className}`}>
            <Label htmlFor={id}>
                {label}
                {required && <span className="text-red-500 ml-1">*</span>}
            </Label>
            <div className="relative">
                <Select value={value} onValueChange={onValueChange} disabled={disabled}>
                    <SelectTrigger
                        id={id}
                        className={`w-full outline-none ${error ? '!border-red-500' : ''}`}
                    >
                        <SelectValue placeholder={placeholder} />
                    </SelectTrigger>
                    <SelectContent>
                        {options.map((option) => (
                            <SelectItem key={option.value} value={option.value}>
                                {option.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>
            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    );
}
