<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSavingsGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'sometimes|numeric|min:0.01',
            'target_date' => 'nullable|date|after:today',
            'category_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ];
    }
}
