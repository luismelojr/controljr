@extends('emails.layout')

@section('title', 'Pagamento Confirmado!')

@section('content')
    <!-- Greeting -->
    <p style="margin: 0 0 24px 0; font-size: 16px; line-height: 1.6; color: #1e293b;">
        Olá, <strong>{{ $user->name }}</strong>!
    </p>

    <!-- Success Icon -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 24px;">
        <tr>
            <td align="center">
                <div style="display: inline-block; background-color: #10b981; border-radius: 50%; width: 64px; height: 64px; text-align: center; line-height: 64px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Message -->
    <p style="margin: 0 0 24px 0; font-size: 16px; line-height: 1.6; color: #1e293b;">
        Recebemos seu pagamento com sucesso! 🎉
    </p>

    <!-- Payment Details Card -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 24px; background-color: #f8fafc; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="padding: 20px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 14px; color: #64748b;">Detalhes do Pagamento</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding: 6px 0;">
                                        <span style="font-size: 14px; color: #64748b;">Plano:</span>
                                    </td>
                                    <td align="right" style="padding: 6px 0;">
                                        <strong style="font-size: 14px; color: #1e293b;">{{ $payment->subscription->plan->name }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0;">
                                        <span style="font-size: 14px; color: #64748b;">Valor:</span>
                                    </td>
                                    <td align="right" style="padding: 6px 0;">
                                        <strong style="font-size: 14px; color: #1e293b;">{{ $payment->amount_formatted }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0;">
                                        <span style="font-size: 14px; color: #64748b;">Método:</span>
                                    </td>
                                    <td align="right" style="padding: 6px 0;">
                                        <strong style="font-size: 14px; color: #1e293b;">
                                            @if($payment->payment_method === 'pix')
                                                PIX
                                            @elseif($payment->payment_method === 'boleto')
                                                Boleto Bancário
                                            @else
                                                Cartão de Crédito
                                            @endif
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0;">
                                        <span style="font-size: 14px; color: #64748b;">Data:</span>
                                    </td>
                                    <td align="right" style="padding: 6px 0;">
                                        <strong style="font-size: 14px; color: #1e293b;">{{ $payment->created_at->format('d/m/Y H:i') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Additional Info -->
    <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.6; color: #64748b;">
        Sua assinatura está ativa e você já pode aproveitar todos os recursos do plano <strong>{{ $payment->subscription->plan->name }}</strong>.
    </p>

    <!-- CTA Button -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 24px;">
        <tr>
            <td align="center">
                <a href="{{ config('app.url') }}/dashboard" style="display: inline-block; padding: 14px 32px; background-color: #FAAD33FF; color: #000000; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">
                    Acessar Dashboard
                </a>
            </td>
        </tr>
    </table>

    <!-- Thank You -->
    <p style="margin: 24px 0 0 0; font-size: 14px; line-height: 1.6; color: #64748b;">
        Obrigado por escolher o MeloSys!<br>
        Equipe MeloSys
    </p>
@endsection
