<?php

namespace App\Domain\Auth\DTO;

use App\Http\Requests\Auth\ForgotPasswordRequest;

readonly class ForgotPasswordData
{
    public function __construct(
        public string $email,
    ) {}

    public static function fromRequest(ForgotPasswordRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
        );
    }
}
