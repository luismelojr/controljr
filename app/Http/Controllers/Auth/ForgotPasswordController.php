<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\DTO\ForgotPasswordData;
use App\Domain\Auth\Services\PasswordResetService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Inertia\Response;

class ForgotPasswordController extends Controller
{
    public function __construct(
        private PasswordResetService $passwordResetService
    ) {}

    /**
     * Exibe o formulário de esqueci minha senha
     */
    public function create(): Response
    {
        return Inertia::render('auth/forgot-password');
    }

    /**
     * Envia o link de recuperação de senha
     */
    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        // Rate limiting - 5 tentativas por hora
//        $key = 'password-reset:'.$request->ip();
//
//        if (RateLimiter::tooManyAttempts($key, 5)) {
//            Toast::error('Muitas tentativas. Tente novamente em 1 hora.', ['persistent' => true, 'duration' => 0]);
//
//            return redirect()->back();
//        }
//
//        RateLimiter::hit($key, 3600); // 1 hora

        // Processa o envio do link
        $data = ForgotPasswordData::fromRequest($request);

        $success = $this->passwordResetService->sendResetLink($data);

        if (!$success) {
            Toast::error('Não foi possível enviar o link de recuperação.');

            return redirect()->back();
        }

        Toast::success('Link de recuperação enviado para seu email!', ['persistent' => true, 'duration' => 0]);

        return redirect()->back();
    }
}
