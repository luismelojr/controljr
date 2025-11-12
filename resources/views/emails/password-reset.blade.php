@extends('emails.layout')

@section('title', 'Recuperação de Senha')

@section('content')
    <p class="greeting">Olá, {{ $user->name }}!</p>

    <div class="content">
        <p>
            Você está recebendo este email porque recebemos uma solicitação de recuperação de senha para sua conta.
        </p>

        <p>
            Clique no botão abaixo para redefinir sua senha:
        </p>
    </div>

    <div class="button-wrapper">
        <a href="{{ $resetUrl }}" class="button">
            Redefinir Senha
        </a>
    </div>

    <div class="info-box">
        <p>
            <strong>⏱️ Este link expira em 60 minutos</strong>
            <br>
            Por questões de segurança, este link de recuperação só pode ser usado uma vez e expira em 1 hora.
        </p>
    </div>

    <hr class="divider">

    <div class="content">
        <p style="font-size: 14px; color: #64748b;">
            Se você não solicitou a recuperação de senha, nenhuma ação é necessária. Sua senha permanecerá inalterada.
        </p>

        <p style="font-size: 13px; color: #94a3b8; margin-top: 20px;">
            <strong>Dica de segurança:</strong> Se você não reconhece esta solicitação, recomendamos alterar sua senha imediatamente após fazer login.
        </p>
    </div>

    <hr class="divider">

    <div class="content" style="font-size: 12px; color: #94a3b8;">
        <p>
            <strong>Problemas ao clicar no botão?</strong>
            <br>
            Copie e cole o seguinte link no seu navegador:
        </p>
        <p style="word-break: break-all; color: #22c55e;">
            {{ $resetUrl }}
        </p>
    </div>
@endsection
