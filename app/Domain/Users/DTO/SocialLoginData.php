<?php

namespace App\Domain\Users\DTO;

readonly class SocialLoginData
{
    public function __construct(
        public string $provider,
        public string $provider_id,
        public string $name,
        public string $email,
        public ?string $avatar = null,
    ){}
}
