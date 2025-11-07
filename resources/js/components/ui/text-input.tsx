import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { EyeIcon, EyeOffIcon } from 'lucide-react';
import React from 'react';

interface TextInputProps extends Omit<React.ComponentProps<'input'>, 'type'> {
    label: string;
    error?: string;
    type: string;
    id: string;
}

export default function TextInput(props: TextInputProps) {
    const [showPassword, setShowPassword] = React.useState(false);

    return (
        <div className={`grid w-full items-center gap-2 ${props.className}`}>
            <Label htmlFor={props.id}>{props.label}</Label>
            <div className={'relative'}>
                <Input
                    {...props}
                    type={showPassword ? 'text' : props.type}
                    id={props.id}
                    className={`${props.error && '!border-red-500'} w-full outline-none`}
                />
                {props.type === 'password' && (
                    <Button
                        type={'button'}
                        variant={'link'}
                        size={'icon'}
                        className={'absolute top-0.5 right-0'}
                        onClick={() => setShowPassword(!showPassword)}
                    >
                        {showPassword ? (
                            <EyeOffIcon className={'h-5 w-5 text-muted-foreground'} />
                        ) : (
                            <EyeIcon className={'h-5 w-5 text-muted-foreground'} />
                        )}
                    </Button>
                )}
            </div>
            {props.error && <p className={'text-xs text-red-500'}>{props.error}</p>}
        </div>
    );
}
