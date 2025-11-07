<?php

namespace App\Domain\Users\DTO;

readonly class LoginUserData
{
    public function __construct(
        public string $email,
        public string $password,
    ){}
}
