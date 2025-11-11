import AuthLayout from '@/components/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import TextInput from '@/components/ui/text-input';
import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { KeyRound } from 'lucide-react';

interface ResetPasswordProps {
    token: string;
    email: string;
}

export default function ResetPassword({ token, email }: ResetPasswordProps) {
    const { data, setData, post, processing, errors } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('password.update'));
    };

    return (
        <AuthLayout title="Redefinir Senha">
            <div className="space-y-6">
                {/* Header */}
                <div className="space-y-2 text-center">
                    <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                        <KeyRound className="h-6 w-6 text-primary" />
                    </div>
                    <h1 className="text-3xl font-bold tracking-tight">Criar Nova Senha</h1>
                    <p className="text-muted-foreground">
                        Digite sua nova senha abaixo. Certifique-se de que seja forte e segura.
                    </p>
                </div>

                {/* Reset Password Form */}
                <form onSubmit={submit} className="space-y-5">
                    {/* Email Display (read-only) */}
                    <TextInput
                        id="email"
                        type="email"
                        label="E-mail"
                        value={data.email}
                        readOnly
                        disabled
                    />

                    <TextInput
                        id="password"
                        type="password"
                        label="Nova Senha"
                        placeholder="Digite sua nova senha"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        required
                        autoFocus
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        label="Confirmar Nova Senha"
                        placeholder="Digite sua senha novamente"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        error={errors.password_confirmation}
                        required
                    />

                    {/* Info Box */}
                    <div className="rounded-lg bg-muted/50 p-4 text-sm text-muted-foreground">
                        <p className="font-medium text-foreground">Requisitos de segurança:</p>
                        <ul className="mt-2 space-y-1 list-disc list-inside">
                            <li>Mínimo de 8 caracteres</li>
                            <li>Combine letras, números e símbolos</li>
                            <li>Evite senhas óbvias ou comuns</li>
                        </ul>
                    </div>

                    {/* Submit Button */}
                    <Button type="submit" className="w-full" loading={processing}>
                        Redefinir Senha
                    </Button>
                </form>

                {/* Security Note */}
                <p className="text-center text-xs text-muted-foreground">
                    Após redefinir sua senha, você será redirecionado para fazer login com suas novas credenciais.
                </p>
            </div>
        </AuthLayout>
    );
}
