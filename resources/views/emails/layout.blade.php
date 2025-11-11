<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc;">
    <!-- Wrapper Table -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <!-- Main Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #1e293b; padding: 40px 30px;">
                            <!-- Logo -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <svg
                                            viewBox="0 0 100 100"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="64"
                                            height="64"
                                        >
                                            <circle cx="50" cy="50" r="48" fill="#000000" />
                                            <path
                                                d="M25 65V38L35 48L45 35L55 48L65 35L75 48V65M35 48V65M55 48V65"
                                                stroke="#ffffff"
                                                stroke-width="4"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                            <circle cx="35" cy="50" r="2" fill="#ffffff" opacity="0.8" />
                                            <circle cx="45" cy="43" r="2" fill="#ffffff" opacity="0.8" />
                                            <circle cx="55" cy="50" r="2" fill="#ffffff" opacity="0.8" />
                                            <circle cx="65" cy="43" r="2" fill="#ffffff" opacity="0.8" />
                                        </svg>

                                        <p
                                            style="
                                              margin: 8px 0 0 0;
                                              font-size: 20px;
                                              font-weight: 600;
                                              color: #ffffff;
                                            "
                                        >
                                            MeloSys
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h2
                                            style="
                                              margin: 0;
                                              font-size: 24px;
                                              font-weight: 600;
                                              color: #ffffff;
                                            "
                                        >
                                            @yield('title')
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px; border-top: 1px solid #e2e8f0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                                            <strong style="color: #1e293b;">{{ config('app.name') }}</strong> - Seu Sistema de Gestão Financeira
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 16px 0;">
                                        <a href="{{ config('app.url') }}" style="color: #FAAD33FF; text-decoration: none; margin: 0 12px; font-size: 13px;">Acessar Plataforma</a>
                                        <span style="color: #cbd5e1; margin: 0 4px;">|</span>
                                        <a href="{{ config('app.url') }}/ajuda" style="color: #FAAD33FF; text-decoration: none; margin: 0 12px; font-size: 13px;">Central de Ajuda</a>
                                        <span style="color: #cbd5e1; margin: 0 4px;">|</span>
                                        <a href="{{ config('app.url') }}/contato" style="color: #FAAD33FF; text-decoration: none; margin: 0 12px; font-size: 13px;">Contato</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="border-top: 1px solid #e2e8f0;"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <p style="margin: 0; color: #64748b; font-size: 12px; line-height: 1.6;">
                                            Este email foi enviado para <strong style="color: #1e293b;">{{ $user->email ?? 'você' }}</strong>.<br>
                                            Se você não solicitou esta ação, por favor ignore este email.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 16px;">
                                        <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                                            &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
