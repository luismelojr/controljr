@extends('emails.layout')

@section('title', $alertNotification->title)

@section('content')
    <p class="greeting">Olá, {{ $user->name }}!</p>

    {{-- Alert Type Badge --}}
    <div style="margin: 20px 0;">
        @php
            $typeColors = [
                'info' => ['bg' => '#3b82f6', 'text' => 'Informação', 'icon' => 'ℹ️'],
                'warning' => ['bg' => '#f59e0b', 'text' => 'Atenção', 'icon' => '⚠️'],
                'danger' => ['bg' => '#ef4444', 'text' => 'Urgente', 'icon' => '🚨'],
                'success' => ['bg' => '#22c55e', 'text' => 'Sucesso', 'icon' => '✅'],
            ];
            $typeConfig = $typeColors[$alertNotification->type] ?? $typeColors['info'];
        @endphp

        <div style="display: inline-block; background-color: {{ $typeConfig['bg'] }}; color: #ffffff; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 600;">
            {{ $typeConfig['icon'] }} {{ $typeConfig['text'] }}
        </div>
    </div>

    {{-- Alert Message --}}
    <div class="content">
        <p style="font-size: 16px; color: #1e293b; line-height: 1.6;">
            {!! nl2br(e($alertNotification->message)) !!}
        </p>
    </div>

    {{-- Additional Alert Data --}}
    @if(isset($alertNotification->data) && is_array($alertNotification->data))
        <div class="info-box" style="background-color: #f8fafc; border-left: 4px solid {{ $typeConfig['bg'] }}; padding: 16px; margin: 20px 0; border-radius: 4px;">
            @if(isset($alertNotification->data['usage_percent']))
                <p style="margin: 0 0 8px 0; color: #475569;">
                    <strong>Uso atual:</strong> {{ number_format($alertNotification->data['usage_percent'], 2) }}%
                </p>
            @endif

            @if(isset($alertNotification->data['card_limit_used']) && isset($alertNotification->data['card_limit']))
                <p style="margin: 0 0 8px 0; color: #475569;">
                    <strong>Limite utilizado:</strong> R$ {{ number_format($alertNotification->data['card_limit_used'], 2, ',', '.') }} de R$ {{ number_format($alertNotification->data['card_limit'], 2, ',', '.') }}
                </p>
            @endif

            @if(isset($alertNotification->data['balance']))
                <p style="margin: 0 0 8px 0; color: #475569;">
                    <strong>Saldo atual:</strong> R$ {{ number_format($alertNotification->data['balance'], 2, ',', '.') }}
                </p>
            @endif

            @if(isset($alertNotification->data['trigger_value']))
                <p style="margin: 0 0 8px 0; color: #475569;">
                    <strong>Limite configurado:</strong> R$ {{ number_format($alertNotification->data['trigger_value'], 2, ',', '.') }}
                </p>
            @endif

            @if(isset($alertNotification->data['due_date']))
                <p style="margin: 0 0 8px 0; color: #475569;">
                    <strong>Data de vencimento:</strong> {{ \Carbon\Carbon::parse($alertNotification->data['due_date'])->format('d/m/Y') }}
                </p>
            @endif

            @if(isset($alertNotification->data['amount']))
                <p style="margin: 0; color: #475569;">
                    <strong>Valor:</strong> R$ {{ number_format($alertNotification->data['amount'], 2, ',', '.') }}
                </p>
            @endif
        </div>
    @endif

    {{-- Action Button --}}
    <div class="button-wrapper" style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard/notifications') }}" style="display: inline-block; background-color: {{ $typeConfig['bg'] }}; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 15px;">
            Ver no Painel
        </a>
    </div>

    <hr class="divider" style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

    {{-- Footer Info --}}
    <div class="content" style="font-size: 14px; color: #64748b;">
        <p>
            Você recebeu este alerta porque configurou notificações automáticas no MeloSys.
        </p>

        <p style="font-size: 13px; color: #94a3b8; margin-top: 16px;">
            <strong>💡 Dica:</strong> Você pode gerenciar suas preferências de alertas a qualquer momento no painel de controle.
        </p>
    </div>

    <hr class="divider" style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

    <div class="content" style="font-size: 12px; color: #94a3b8;">
        <p>
            <strong>Problemas ao clicar no botão?</strong>
            <br>
            Copie e cole o seguinte link no seu navegador:
        </p>
        <p style="word-break: break-all; color: #22c55e;">
            {{ url('/dashboard/notifications') }}
        </p>
    </div>
@endsection

<style>
    .greeting {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 20px 0;
    }

    .content {
        margin: 16px 0;
    }

    .content p {
        margin: 0 0 12px 0;
        color: #475569;
        line-height: 1.6;
    }

    .button-wrapper {
        text-align: center;
        margin: 30px 0;
    }

    .info-box {
        background-color: #f8fafc;
        padding: 16px;
        margin: 20px 0;
        border-radius: 4px;
    }

    .info-box p {
        margin: 0 0 8px 0;
    }

    .info-box p:last-child {
        margin-bottom: 0;
    }

    .divider {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 24px 0;
    }
</style>
