import AuthLayout from '@/components/layouts/auth-layout';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import TextInput from '@/components/ui/text-input';
import { GoogleLoginButton } from '@/components/ui/google-login-button';
import { Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <AuthLayout title="Login">
            <div className="space-y-6">
                {/* Header */}
                <div className="space-y-2 text-center">
                    <h1 className="text-3xl font-bold tracking-tight">Acesse sua Conta</h1>
                    <p className="text-muted-foreground">Selecione o método de login:</p>
                </div>

                {/* Social Login Buttons */}
                <div className="w-full">
                    <GoogleLoginButton className="w-full" />
                </div>

                {/* Divider */}
                <div className="relative">
                    <div className="absolute inset-0 flex items-center">
                        <span className="w-full border-t" />
                    </div>
                    <div className="relative flex justify-center text-xs uppercase">
                        <span className="bg-background px-2 text-muted-foreground">ou continue com e-mail</span>
                    </div>
                </div>

                {/* Login Form */}
                <form onSubmit={submit} className="space-y-5">
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

                    <TextInput
                        id="password"
                        type="password"
                        label="Senha"
                        placeholder="Digite sua senha"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        required
                    />

                    {/* Remember Me & Forgot Password */}
                    <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <Checkbox id="remember" checked={data.remember} onCheckedChange={(checked) => setData('remember', checked as boolean)} />
                            <Label htmlFor="remember" className="cursor-pointer text-sm font-normal">
                                Lembrar-me
                            </Label>
                        </div>
                        <Link href={route('login')} className="text-sm font-medium text-primary hover:text-primary/80">
                            Esqueceu a senha?
                        </Link>
                    </div>

                    {/* Submit Button */}
                    <Button type="submit" className="w-full" disabled={processing}>
                        {processing ? 'Entrando...' : 'Entrar'}
                    </Button>
                </form>

                {/* Sign Up Link */}
                <p className="text-center text-sm text-muted-foreground">
                    Ainda não tem uma conta?{' '}
                    <Link href={route('register')} className="font-medium text-primary hover:text-primary/80">
                        Criar conta
                    </Link>
                </p>
            </div>
        </AuthLayout>
    );
}
