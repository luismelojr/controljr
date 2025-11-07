<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Users\DTO\LoginUserData;
use App\Domain\Users\Services\UserService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('auth/login');
    }

    public function store(LoginRequest $request, UserService $service)
    {
        $values = $request->validated();
        $data = new LoginUserData(
            email: $values['email'],
            password: $values['password']
        );

        $user = $service->loginWithEmailAndPassword($data);

        Auth::login($user);

        Toast::create('Login realizado com sucesso')
            ->title('Bem-vindo(a) à nossa plataforma!')
            ->success()
            ->flash();

        return redirect()->route('dashboard.home');
    }
}
