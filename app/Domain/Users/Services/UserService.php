<?php

namespace App\Domain\Users\Services;

use App\Domain\Users\DTO\LoginUserData;
use App\Domain\Users\DTO\RegisterUserData;
use App\Domain\Users\DTO\SocialLoginData;
use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function register(RegisterUserData $data): User
    {
        return DB::transaction(function () use ($data) {
            try {
                $user = User::create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                    'phone' => Helpers::formatStringRemoveCharactersSpecial($data->phone),
                ]);

                return $user;
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }

    public function loginWithEmailAndPassword(LoginUserData $data): User
    {
        $user = User::where('email', $data->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if ($user && !$user->status) {
            throw ValidationException::withMessages([
                'email' => __('auth.not_activated'),
            ]);
        }

        if ($user && !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    public function socialLogin(SocialLoginData $data): User
    {
        return DB::transaction(function () use ($data) {
            // Verifica se o usuário já existe
            $existingUser = User::where($data->provider . '_id', $data->provider_id)->first();

            // Se existe e está desativado, lança erro
            if ($existingUser && !$existingUser->status) {
                throw ValidationException::withMessages([
                    'email' => __('auth.account_disabled'),
                ]);
            }

            $user = User::updateOrCreate(
                [$data->provider . '_id' => $data->provider_id],
                [
                    'name' => $data->name,
                    'email' => $data->email,
                    'status' => $existingUser ? $existingUser->status : true,
                ]
            );

            return $user;
        });
    }
}
