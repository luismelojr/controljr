import AuthLayout from '@/components/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import TextInput from '@/components/ui/text-input';
import TextMask from '@/components/ui/text-mask';
import { Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        phone: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register.store'));
    };

    return (
        <AuthLayout title="Cadastro">
            <div className="space-y-6">
                {/* Header */}
                <div className="space-y-2 text-center">
                    <h1 className="text-3xl font-bold tracking-tight">Criar uma Conta</h1>
                    <p className="text-muted-foreground">Comece com sua conta gratuita</p>
                </div>

                {/* Social Login Buttons */}
                <div className="w-full">
                    <Button variant="outline" className="w-full" type="button">
                        <svg className="mr-2 h-4 w-4" viewBox="0 0 24 24">
                            <path
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                fill="#4285F4"
                            />
                            <path
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                fill="#34A853"
                            />
                            <path
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                fill="#FBBC05"
                            />
                            <path
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                fill="#EA4335"
                            />
                        </svg>
                        Google
                    </Button>
                </div>

                {/* Divider */}
                <div className="relative">
                    <div className="absolute inset-0 flex items-center">
                        <span className="w-full border-t" />
                    </div>
                    <div className="relative flex justify-center text-xs uppercase">
                        <span className="bg-background px-2 text-muted-foreground">ou cadastre-se com e-mail</span>
                    </div>
                </div>

                {/* Register Form */}
                <form onSubmit={submit} className="space-y-5">
                    <TextInput
                        id="name"
                        type="text"
                        label="Nome Completo"
                        placeholder="Digite seu nome completo"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        error={errors.name}
                        required
                    />

                    <TextInput
                        id="email"
                        type="email"
                        label="E-mail"
                        placeholder="Digite seu e-mail"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        required
                    />

                    <TextMask
                        label="Telefone"
                        id="phone"
                        mask="(00) 00000-0000"
                        placeholder="(00) 00000-0000"
                        value={data.phone}
                        onChange={(value) => setData('phone', value)}
                        error={errors.phone}
                        required
                    />

                    <TextInput
                        id="password"
                        type="password"
                        label="Senha"
                        placeholder="Crie uma senha"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        required
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        label="Confirmar Senha"
                        placeholder="Confirme sua senha"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        error={errors.password_confirmation}
                        required
                    />

                    {/* Submit Button */}
                    <Button type="submit" className="w-full" loading={processing}>
                        Criar Conta
                    </Button>
                </form>

                {/* Login Link */}
                <p className="text-center text-sm text-muted-foreground">
                    Já tem uma conta?{' '}
                    <Link href={route('login')} className="font-medium text-primary hover:text-primary/80">
                        Entrar
                    </Link>
                </p>
            </div>
        </AuthLayout>
    );
}
