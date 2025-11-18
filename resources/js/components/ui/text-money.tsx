import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import CurrencyInput from 'react-currency-input-field';

interface TextMoneyProps {
    label: string;
    error?: string;
    id: string;
    className?: string;
    onChange?: (value: number) => void;
    value?: number | string;
    placeholder?: string;
    name?: string;
    autoComplete?: string;
    required?: boolean;
    disabled?: boolean;
}

export default function TextMoney(props: TextMoneyProps) {
    const handleValueChange = (value: string | undefined, name?: string, values?: any) => {
        if (!value || value === '') {
            props.onChange?.(0);
            return;
        }

        // O valor vem como string com ponto decimal (ex: "145.25")
        const numericValue = parseFloat(value);

        if (isNaN(numericValue)) {
            props.onChange?.(0);
            return;
        }

        // Envia o valor numérico com precisão
        props.onChange?.(numericValue);
    };

    return (
        <div className={`grid w-full items-center gap-2 ${props.className}`}>
            <Label htmlFor={props.id}>
                {props.label}
                {props.required && <span className="ml-1 text-red-500">*</span>}
            </Label>
            <div className={'relative'}>
                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">R$</span>
                <CurrencyInput
                    id={props.id}
                    name={props.name}
                    placeholder={props.placeholder || '0,00'}
                    defaultValue={props.value}
                    decimalsLimit={2}
                    decimalSeparator=","
                    groupSeparator="."
                    onValueChange={handleValueChange}
                    autoComplete={props.autoComplete}
                    required={props.required}
                    disabled={props.disabled}
                    allowNegativeValue={false}
                    className={cn(
                        'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input h-9 w-full min-w-0 rounded-md border bg-transparent py-1 pl-10 pr-3 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                        'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                        props.error && '!border-red-500'
                    )}
                />
            </div>
            {props.error && <p className={'text-xs text-red-500'}>{props.error}</p>}
        </div>
    );
}
