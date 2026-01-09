@extends('emails.layouts.default')

@section('content')
    <h2 style="color: #d32f2f;">Ação Necessária: Falha no Pagamento ⚠️</h2>
    
    <p>Olá, {{ $user->name }}!</p>
    
    <p>Não conseguimos processar a renovação da sua assinatura do <strong>MeloSys</strong>.</p>
    
    <div class="warning-box">
        <p style="margin: 0; font-weight: bold;">O que isso significa?</p>
        <p style="margin: 10px 0;">Você entrou no período de carência (Grace Period). Seus recursos premium continuam ativos por mais alguns dias, mas precisamos que regularize o pagamento para evitar o bloqueio.</p>
    </div>

    <p>Por favor, verifique seu cartão de crédito ou atualize seu método de pagamento.</p>
    
    <a href="{{ route('dashboard.payment.index') }}" class="button">Resolver Pendência</a>
    
    <p>Se você já realizou o pagamento, por favor desconsidere este e-mail.</p>
@endsection
