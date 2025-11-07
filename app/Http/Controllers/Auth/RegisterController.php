<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Users\DTO\RegisterUserData;
use App\Domain\Users\Services\UserService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function create()
    {
        return Inertia::render('auth/register');
    }

    public function store(RegisterRequest $request, UserService $service)
    {
        try {
            $values = $request->validated();
            $data = new RegisterUserData(
                name: $values['name'],
                email: $values['email'],
                password: $values['password'],
                phone: $values['phone']
            );
            $user = $service->register($data);

            Auth::login($user);

            Toast::create('Conta criada com sucesso')
                ->title('Bem-vindo(a) à nossa plataforma!')
                ->success()
                ->flash();

            return redirect()->route('dashboard.home');

        } catch (\Exception $exception) {
            Toast::create('Error ao criar usuário')
                ->title('Erro')
                ->description('Ocorreu um erro ao criar sua conta, tente novamente mais tarde.')
                ->error()
                ->flash();

            return redirect()->back();
        }
    }
}
