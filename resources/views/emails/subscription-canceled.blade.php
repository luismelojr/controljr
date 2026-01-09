@extends('emails.layouts.default')

@section('content')
    <h2>Assinatura Cancelada 😢</h2>
    
    <p>Olá, {{ $user->name }}!</p>
    
    <p>Confirmamos o cancelamento da sua assinatura <strong>{{ $subscription->plan->name }}</strong>.</p>
    
    <div class="info-box">
        <p>Você continuará tendo acesso aos recursos premium até o final do seu período atual:</p>
        <p><strong>Válido até: {{ $subscription->ends_at?->format('d/m/Y') }}</strong></p>
    </div>

    <p>Lamentamos ver você partir! Se houver algo que possamos fazer para melhorar sua experiência, adoraríamos saber.</p>
    
    <p>Você pode reativar sua assinatura a qualquer momento clicando abaixo:</p>
    
    <a href="{{ route('dashboard.subscription.index') }}" class="button">Retomar Assinatura</a>
@endsection
