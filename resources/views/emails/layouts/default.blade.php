<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f7;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
        }
        .header h1 {
            color: #101010;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .content {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #000000;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #888888;
        }
        .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #000000;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background-color: #fff4e5;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            color: #663c00;
        }
        h2 {
            font-size: 20px;
            margin-top: 0;
            color: #111827;
        }
        p {
            margin-bottom: 20px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
            <p>Se você não solicitou este e-mail, por favor ignore-o.</p>
        </div>
    </div>
</body>
</html>
