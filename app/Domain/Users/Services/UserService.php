<?php

namespace App\Domain\Users\Services;

use App\Domain\Users\DTO\RegisterUserData;
use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
}
