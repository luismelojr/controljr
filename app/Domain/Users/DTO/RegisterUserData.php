<?php

namespace App\Domain\Users\DTO;

readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $phone,
    ){}
}
