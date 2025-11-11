<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao MeloSys</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bem-vindo ao MeloSys!</h1>
    </div>

    <div class="content">
        <p>Olá, <strong>{{ $user->name }}</strong>!</p>

        <p>É um prazer tê-lo conosco no <strong>MeloSys</strong>, sua nova ferramenta de controle financeiro pessoal.</p>

        <p>Com o MeloSys, você poderá:</p>
        <ul>
            <li>Gerenciar suas carteiras e contas bancárias</li>
            <li>Controlar suas despesas e receitas</li>
            <li>Acompanhar transações em tempo real</li>
            <li>Organizar suas finanças por categorias</li>
            <li>Visualizar relatórios detalhados</li>
        </ul>

        <p>Estamos aqui para ajudá-lo a ter um controle financeiro mais eficiente e organizado.</p>

        <p style="text-align: center;">
            <a href="{{ config('app.url') }}/dashboard" class="button">Acessar Meu Dashboard</a>
        </p>

        <p>Se você tiver alguma dúvida ou precisar de ajuda, não hesite em nos contatar.</p>

        <p>Boas-vindas e bom controle financeiro!</p>

        <p>Equipe MeloSys</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} MeloSys. Todos os direitos reservados.</p>
        <p>Este email foi enviado para {{ $user->email }}</p>
    </div>
</body>
</html>
