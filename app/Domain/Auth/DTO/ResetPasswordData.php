<?php

namespace App\Domain\Auth\DTO;

use App\Http\Requests\Auth\ResetPasswordRequest;

readonly class ResetPasswordData
{
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
    ) {}

    public static function fromRequest(ResetPasswordRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            token: $request->validated('token'),
            password: $request->validated('password'),
        );
    }
}
