import DashboardLayout from '@/components/layouts/dashboard-layout';
import { AvatarUpload } from '@/components/ui/avatar-upload';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { toast } from 'sonner';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    auth: {
        user: {
            name: string;
            email: string;
            avatar_url?: string;
        };
    };
}

export default function ProfileEdit({ mustVerifyEmail, status, auth }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: auth.user.name,
        email: auth.user.email,
        avatar: null as File | null,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('dashboard.profile.update'), {
            onSuccess: () => {
                toast.success('Perfil atualizado com sucesso!');
                // Reset password/sensitive fields if they were here, but for now just clear file
                setData('avatar', null);
            },
            onError: () => {
                toast.error('Erro ao atualizar perfil. Verifique os dados.');
            },
            forceFormData: true,
        });
    };

    return (
        <DashboardLayout>
            <Head title="Perfil" />

            <div className="mx-auto max-w-2xl space-y-6 py-12">
                <Card>
                    <CardHeader>
                        <CardTitle>Informações do Perfil</CardTitle>
                        <CardDescription>Atualize suas informações de conta e endereço de email.</CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="mb-6 flex justify-center">
                                <AvatarUpload
                                    currentAvatarUrl={auth.user.avatar_url}
                                    onFileSelect={(file) => setData('avatar', file)}
                                    error={errors.avatar}
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="name">Nome</Label>
                                <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} required autoComplete="name" />
                                {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                    autoComplete="username"
                                />
                                {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                            </div>

                            {mustVerifyEmail && auth.user.email_verified_at === null && (
                                <div>
                                    <p className="mt-2 text-sm text-muted-foreground">
                                        Seu endereço de email não foi verificado.
                                        <Button
                                            variant="link"
                                            className="h-auto p-0 font-normal"
                                            onClick={() => {
                                                /* Implement verification logic if needed later */
                                            }}
                                        >
                                            Clique aqui para reenviar o email de verificação.
                                        </Button>
                                    </p>

                                    {status === 'verification-link-sent' && (
                                        <div className="mt-2 text-sm font-medium text-green-600">
                                            Um novo link de verificação foi enviado para seu email.
                                        </div>
                                    )}
                                </div>
                            )}

                            <div className="flex items-center gap-4">
                                <Button disabled={processing} type="submit">
                                    Salvar
                                </Button>

                                {processing && <span className="animate-pulse text-sm text-muted-foreground">Salvando...</span>}
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </DashboardLayout>
    );
}
