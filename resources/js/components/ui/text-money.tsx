import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import React from 'react';

interface TextMoneyProps {
    label: string;
    error?: string;
    helperText?: string;
    id: string;
    className?: string;
    onChange?: (value: number) => void;
    onValueChange?: (value: string) => void; // Para compatibilidade com outros componentes
    value?: number | string;
    placeholder?: string;
    name?: string;
    required?: boolean;
    disabled?: boolean;
    showPrefix?: boolean; // Mostrar R$ ou não
    autoFocus?: boolean;
}

export default function TextMoney(props: TextMoneyProps) {
    const {
        label,
        error,
        helperText,
        id,
        className,
        onChange,
        onValueChange,
        value,
        placeholder = 'R$ 0,00',
        name,
        required,
        disabled,
        showPrefix = true,
        autoFocus,
    } = props;

    /**
     * Formata um número para o formato BRL
     * Ex: 1234.56 → "R$ 1.234,56"
     */
    const formatToBRL = (num: number): string => {
        const formatted = new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(num);

        return showPrefix ? `R$ ${formatted}` : formatted;
    };

    /**
     * Converte string formatada para número
     * Ex: "R$ 1.234,56" → 1234.56
     */
    const parseFromBRL = (str: string): number => {
        // Remove tudo exceto números e vírgula
        const cleanedStr = str.replace(/[^\d,]/g, '');
        // Substitui vírgula por ponto
        const numberStr = cleanedStr.replace(',', '.');
        const parsed = parseFloat(numberStr);
        return isNaN(parsed) ? 0 : parsed;
    };

    /**
     * Gera o valor formatado inicial
     */
    const getInitialFormattedValue = (): string => {
        if (value === undefined || value === null || value === '') {
            return '';
        }

        const numValue = typeof value === 'string' ? parseFromBRL(value) : value;
        return formatToBRL(numValue);
    };

    const [displayValue, setDisplayValue] = React.useState(getInitialFormattedValue());
    const isTypingRef = React.useRef(false);

    // Atualiza o display quando o value prop mudar (mas não quando está digitando)
    React.useEffect(() => {
        if (!isTypingRef.current) {
            setDisplayValue(getInitialFormattedValue());
        }
        isTypingRef.current = false;
    }, [value]);

    /**
     * Manipula mudanças no input
     */
    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        isTypingRef.current = true;
        const inputValue = e.target.value;

        // Remove tudo exceto dígitos
        const digitsOnly = inputValue.replace(/\D/g, '');

        if (digitsOnly === '') {
            setDisplayValue('');
            onChange?.(0);
            onValueChange?.('0');
            return;
        }

        // Converte para número dividindo por 100 (para centavos)
        const numValue = parseInt(digitsOnly, 10) / 100;

        // Formata para BRL
        const formattedValue = formatToBRL(numValue);

        setDisplayValue(formattedValue);
        onChange?.(numValue);
        onValueChange?.(numValue.toString());
    };

    /**
     * Quando o input perde o foco, garante formatação completa
     */
    const handleBlur = () => {
        if (displayValue === '') {
            return;
        }

        const numValue = parseFromBRL(displayValue);
        setDisplayValue(formatToBRL(numValue));
    };

    return (
        <div className={`grid w-full items-center gap-2 ${className || ''}`}>
            <Label htmlFor={id}>
                {label}
                {required && <span className="text-red-500 ml-1">*</span>}
            </Label>
            <div className={'relative'}>
                <Input
                    id={id}
                    name={name}
                    type="text"
                    value={displayValue}
                    onChange={handleChange}
                    onBlur={handleBlur}
                    placeholder={placeholder}
                    disabled={disabled}
                    required={required}
                    autoFocus={autoFocus}
                    className={`${error && '!border-red-500'} w-full outline-none`}
                    autoComplete="off"
                />
            </div>
            {error && <p className={'text-xs text-red-500'}>{error}</p>}
            {helperText && !error && <p className={'text-xs text-muted-foreground'}>{helperText}</p>}
        </div>
    );
}
