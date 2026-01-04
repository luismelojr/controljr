<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSavingsGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01', // Input as BRL/Float
            'target_date' => 'nullable|date|after:today',
            'category_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert BRL string/float to attributes if needed, 
        // but typically client sends structured data.
        // Assuming client sends `target_amount` as currency string or float.
        // We will handle conversion to cents in Controller or Service.
    }
}
