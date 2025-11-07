import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import React from 'react';

interface TextTextareaProps extends React.ComponentProps<'textarea'> {
    label: string;
    error?: string;
    id: string;
}

export default function TextAreaCustom(props: TextTextareaProps) {
    return (
        <div className={`grid w-full items-center gap-2 ${props.className}`}>
            <Label htmlFor={props.id}>{props.label}</Label>
            <Textarea
                {...props}
                id={props.id}
                className={`${props.error && '!border-red-500'} w-full outline-none`}
            />
            {props.error && <p className={'text-xs text-red-500'}>{props.error}</p>}
        </div>
    );
}
