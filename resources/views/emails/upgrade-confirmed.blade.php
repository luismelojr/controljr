@extends('emails.layouts.default')

@section('content')
    <h2>Upgrade Confirmado! 🌟</h2>
    
    <p>Olá, {{ $user->name }}!</p>
    
    <p>Parabéns! Você fez um upgrade para o plano <strong>{{ $subscription->plan->name }}</strong>.</p>
    
    <p>Sua conta foi atualizada e você já pode aproveitar todos os novos benefícios.</p>
    
    <div class="info-box">
        <p>A cobrança proporcional foi processada e sua próxima fatura virá com o valor do novo plano.</p>
    </div>

    <a href="{{ route('dashboard.subscription.index') }}" class="button">Ver Meus Benefícios</a>
    
    <p>Experimente as novas funcionalidades agora mesmo!</p>
@endsection
