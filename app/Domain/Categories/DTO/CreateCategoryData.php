<?php

namespace App\Domain\Categories\DTO;

use Illuminate\Http\Request;

class CreateCategoryData
{
    public function __construct(
        public readonly string $name,
        public readonly bool $status = true,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            status: $request->boolean('status', true),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'status' => $this->status,
            'is_default' => false, // Categorias criadas por usuários nunca são default
        ];
    }
}
