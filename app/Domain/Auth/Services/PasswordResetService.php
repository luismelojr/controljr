<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTO\ForgotPasswordData;
use App\Domain\Auth\DTO\ResetPasswordData;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Envia link de recuperação de senha por email
     */
    public function sendResetLink(ForgotPasswordData $data): bool
    {
        // Busca o usuário pelo email
        $user = User::where('email', $data->email)->first();

        if (!$user) {
            return false;
        }

        // Gera token único
        $token = Str::random(64);

        // Deleta tokens anteriores do usuário
        DB::table('password_reset_tokens')
            ->where('email', $data->email)
            ->delete();

        // Salva novo token no banco
        DB::table('password_reset_tokens')->insert([
            'email' => $data->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Gera URL de reset
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $data->email,
        ]);

        // Envia email via queue
        Mail::to($user->email)->queue(new PasswordResetMail($user, $resetUrl));

        return true;
    }

    /**
     * Valida token e reseta a senha
     */
    public function resetPassword(ResetPasswordData $data): bool
    {
        // Busca o token no banco
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $data->email)
            ->first();

        // Valida se token existe
        if (!$tokenRecord) {
            return false;
        }

        // Valida se token não expirou (60 minutos)
        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            // Deleta token expirado
            DB::table('password_reset_tokens')
                ->where('email', $data->email)
                ->delete();

            return false;
        }

        // Valida se o hash do token bate
        if (!Hash::check($data->token, $tokenRecord->token)) {
            return false;
        }

        // Busca o usuário
        $user = User::where('email', $data->email)->first();

        if (!$user) {
            return false;
        }

        // Atualiza a senha
        $user->password = Hash::make($data->password);
        $user->save();

        // Deleta o token usado
        DB::table('password_reset_tokens')
            ->where('email', $data->email)
            ->delete();

        return true;
    }

    /**
     * Valida se o token é válido antes de mostrar o formulário
     */
    public function validateToken(string $token, string $email): bool
    {
        // Busca o token no banco
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        // Valida se token existe
        if (!$tokenRecord) {
            return false;
        }

        // Valida se token não expirou (60 minutos)
        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            // Deleta token expirado
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            return false;
        }

        // Valida se o hash do token bate
        if (!Hash::check($token, $tokenRecord->token)) {
            return false;
        }

        return true;
    }
}
