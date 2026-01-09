@extends('emails.layouts.default')

@section('content')
    <h2 style="color: #ff9800;">Sua assinatura expira em breve! ⏳</h2>
    
    <p>Olá, {{ $user->name }}!</p>
    
    <p>Gostaríamos de lembrar que sua assinatura do plano <strong>{{ $subscription->plan->name }}</strong> vai encerrar em <strong>{{ $daysUntilExpiration }} dias</strong>.</p>
    
    <div class="warning-box">
        <p style="margin: 0; font-weight: bold;">Data de Término: {{ $subscription->ends_at?->format('d/m/Y') }}</p>
        <p style="margin: 10px 0;">Após esta data, você perderá acesso aos recursos premium e seus dados históricos podem ficar limitados.</p>
    </div>

    <p>Não deixe para a última hora! Retome sua assinatura agora e mantenha seu controle financeiro em dia.</p>
    
    <a href="{{ route('dashboard.subscription.index') }}" class="button">Retomar Assinatura</a>
@endsection
