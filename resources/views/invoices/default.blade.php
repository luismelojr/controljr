<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fatura</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #6366F1; }
        .invoice-details { float: right; text-align: right; }
        .client-details { margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background: #f8f9fa; border-bottom: 1px solid #ddd; padding: 10px; text-align: left; }
        .table td { border-bottom: 1px solid #eee; padding: 10px; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 20px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .badge-success { background-color: #10B981; } /* Emerald 500 */
        .badge-pending { background-color: #F59E0B; } /* Amber 500 */
        .badge-danger { background-color: #EF4444; } /* Red 500 */
    </style>
</head>
<body>

<div class="header">
    <div class="logo">MeloSys</div>
    <div class="invoice-details">
        <strong>Fatura #{{ $payment->uuid }}</strong><br>
        Data: {{ $payment->created_at->format('d/m/Y') }}<br>
        Status: 
        @if($payment->status === 'received' || $payment->status === 'confirmed')
            <span class="badge badge-success">PAGO</span>
        @elseif($payment->status === 'pending')
            <span class="badge badge-pending">PENDENTE</span>
        @else
            <span class="badge badge-danger">{{ strtoupper($payment->status) }}</span>
        @endif
    </div>
</div>

<div class="client-details">
    <strong>Faturado para:</strong><br>
    {{ $payment->user->name }}<br>
    {{ $payment->user->email }}<br>
    @if($payment->user->cpf)
        CPF: {{ $payment->user->cpf }}
    @endif
</div>

<table class="table">
    <thead>
        <tr>
            <th>Descrição</th>
            <th style="text-align: right;">Valor</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Assinatura Premium/Family</td>
            <td style="text-align: right;">{{ $payment->amount_formatted }}</td>
        </tr>
    </tbody>
</table>

<div class="total">
    Total: {{ $payment->amount_formatted }}
</div>

<div class="footer">
    MeloSys Tecnologias<br>
    Obrigado por sua assinatura!
</div>

</body>
</html>
