@extends('emails.layouts.default')

@section('content')
    <h2>Bem-vindo ao MeloSys Premium! 🚀</h2>
    
    <p>Olá, {{ $user->name }}!</p>
    
    <p>Sua assinatura do plano <strong>{{ $subscription->plan->name }}</strong> foi ativada com sucesso.</p>
    
    <p>Agora você tem acesso a ferramentas poderosas para dominar suas finanças:</p>
    
    <ul>
        <li>Dashboard avançado</li>
        <li>Relatórios detalhados</li>
        <li>Categorias e carteiras ilimitadas</li>
        <li>Alertas inteligentes</li>
    </ul>

    <a href="{{ route('dashboard.home') }}" class="button">Acessar Dashboard</a>
    
    <p>Se tiver qualquer dúvida, nossa equipe está pronta para ajudar.</p>
@endsection
