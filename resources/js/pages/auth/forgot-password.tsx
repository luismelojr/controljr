import AuthLayout from '@/components/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import TextInput from '@/components/ui/text-input';
import { Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { ArrowLeft, Mail } from 'lucide-react';

export default function ForgotPassword() {
    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('password.email'));
    };

    return (
        <AuthLayout title="Recuperar Senha">
            <div className="space-y-6">
                {/* Header */}
                <div className="space-y-2 text-center">
                    <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                        <Mail className="h-6 w-6 text-primary" />
                    </div>
                    <h1 className="text-3xl font-bold tracking-tight">Esqueceu sua senha?</h1>
                    <p className="text-muted-foreground">
                        Sem problemas! Enviaremos um link de recuperação para seu e-mail.
                    </p>
                </div>

                {/* Success Message */}
                {recentlySuccessful && (
                    <div className="rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-400">
                        <p className="font-medium">Link enviado com sucesso!</p>
                        <p className="mt-1">Verifique sua caixa de entrada e siga as instruções no e-mail.</p>
                    </div>
                )}

                {/* Forgot Password Form */}
                <form onSubmit={submit} className="space-y-5">
                    <TextInput
                        id="email"
                        type="email"
                        label="E-mail"
                        placeholder="Digite seu e-mail cadastrado"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        required
                        autoFocus
                    />

                    {/* Info Box */}
                    <div className="rounded-lg bg-muted/50 p-4 text-sm text-muted-foreground">
                        <p>
                            <strong className="text-foreground">⏱️ O link expira em 60 minutos</strong>
                        </p>
                        <p className="mt-1">
                            Por questões de segurança, você terá 1 hora para redefinir sua senha após receber o e-mail.
                        </p>
                    </div>

                    {/* Submit Button */}
                    <Button type="submit" className="w-full" loading={processing}>
                        Enviar Link de Recuperação
                    </Button>
                </form>

                {/* Back to Login */}
                <div className="flex items-center justify-center">
                    <Link
                        href={route('login')}
                        className="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Voltar para o login
                    </Link>
                </div>
            </div>
        </AuthLayout>
    );
}
