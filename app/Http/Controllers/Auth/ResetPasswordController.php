<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\DTO\ResetPasswordData;
use App\Domain\Auth\Services\PasswordResetService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResetPasswordController extends Controller
{
    public function __construct(
        private PasswordResetService $passwordResetService
    ) {}

    /**
     * Exibe o formulário de redefinir senha
     */
    public function create(Request $request): Response|RedirectResponse
    {
        $token = $request->query('token');
        $email = $request->query('email');

        // Valida se token e email foram fornecidos
        if (! $token || ! $email) {
            Toast::error('Link de recuperação inválido');

            return redirect()->route('login');
        }

        // Valida se o token é válido
        if (! $this->passwordResetService->validateToken($token, $email)) {
            Toast::error('Link de recuperação inválido ou expirado', ['persistent' => true, 'duration' => 0]);

            return redirect()->route('password.request');
        }

        return Inertia::render('auth/reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Processa a redefinição de senha
     */
    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $data = ResetPasswordData::fromRequest($request);

        $success = $this->passwordResetService->resetPassword($data);

        if (! $success) {
            Toast::error('Link de recuperação inválido ou expirado', ['persistent' => true, 'duration' => 0]);

            return redirect()->route('password.request');
        }

        Toast::success('Senha redefinida com sucesso! Faça login com sua nova senha.', ['persistent' => true, 'duration' => 0]);

        return redirect()->route('login');
    }
}
