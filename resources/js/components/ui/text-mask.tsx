import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { IMaskInput } from 'react-imask';

interface TextMaskProps {
    label: string;
    error?: string;
    id: string;
    className?: string;
    mask: string;
    onChange?: (value: string, event?: React.ChangeEvent<HTMLInputElement>) => void;
    value?: string;
    placeholder?: string;
    name?: string;
    autoComplete?: string;
    required?: boolean;
}

export default function TextMask(props: TextMaskProps) {
    return (
        <div className={`grid w-full items-center gap-2 ${props.className}`}>
            <Label htmlFor={props.id}>
                {props.label}
                {props.required && <span className="text-red-500 ml-1">*</span>}
            </Label>
            <div className={'relative'}>
                <IMaskInput
                    id={props.id}
                    mask={props.mask}
                    value={props.value}
                    onAccept={(value: string) => props.onChange?.(value)}
                    placeholder={props.placeholder}
                    name={props.name}
                    autoComplete={props.autoComplete}
                    required={props.required}
                    className={cn(
                        "file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
                        "focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]",
                        "aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
                        props.error && '!border-red-500'
                    )}
                />
            </div>
            {props.error && <p className={'text-xs text-red-500'}>{props.error}</p>}
        </div>
    );
}
