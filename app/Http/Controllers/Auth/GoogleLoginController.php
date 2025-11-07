<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Users\DTO\SocialLoginData;
use App\Domain\Users\Services\UserService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(UserService $service)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $data = new SocialLoginData(
                provider: 'google',
                provider_id: $googleUser->getId(),
                name: $googleUser->getName(),
                email: $googleUser->getEmail(),
                avatar: $googleUser->getAvatar(),
            );

            $user = $service->socialLogin($data);

            Auth::login($user);

            Toast::create('Login realizado com sucesso')
                ->title('Bem-vindo(a) à nossa plataforma!')
                ->success()
                ->flash();

            return redirect()->route('dashboard.home');
        } catch (ValidationException $e) {
            // Captura erros de validação (ex: conta desativada)
            $message = $e->validator->errors()->first();

            Toast::create($message)
                ->error()
                ->flash();

            return redirect()->route('login');
        } catch (\Exception $e) {
            Toast::create('Erro ao fazer login com o Google')
                ->description($e->getMessage())
                ->error()
                ->flash();

            return redirect()->route('login');
        }
    }
}
