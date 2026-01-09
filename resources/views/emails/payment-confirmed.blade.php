@extends('emails.layouts.default')

@section('content')
    <h2>Pagamento Confirmado! 🎉</h2>
    
    <p>Olá, {{ $user->name }}!</p>
    
    <p>Confirmamos o recebimento do seu pagamento referente à assinatura do <strong>MeloSys</strong>.</p>
    
    <div class="info-box">
        <p style="margin: 0; font-weight: bold;">Detalhes do Pagamento:</p>
        <p style="margin: 5px 0 0 0;">Valor: {{ $payment->value_formatted }}</p>
        <p style="margin: 5px 0 0 0;">Data: {{ $payment->payment_date?->format('d/m/Y') }}</p>
        <p style="margin: 5px 0 0 0;">Método: {{ ucfirst($payment->billing_type) }}</p>
    </div>

    <p>Sua assinatura continua ativa e você tem acesso total a todos os recursos do seu plano.</p>
    
    <a href="{{ route('dashboard.subscription.index') }}" class="button">Ver Minha Assinatura</a>
    
    <p style="font-size: 14px; color: #666;">Obrigado por confiar no MeloSys para organizar suas finanças!</p>
@endsection
