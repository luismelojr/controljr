<?php

namespace App\Domain\Categories\DTO;

use Illuminate\Http\Request;

class UpdateCategoryData
{
    public function __construct(
        public readonly string $name,
        public readonly bool $status,
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
        ];
    }
}
