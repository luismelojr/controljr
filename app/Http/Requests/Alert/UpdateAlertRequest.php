<?php

namespace App\Http\Requests\Alert;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trigger_value' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'trigger_days' => ['nullable', 'array'],
            'trigger_days.*' => ['integer', 'min:1', 'max:365'],
            'notification_channels' => ['nullable', 'array'],
            'notification_channels.*' => ['string', Rule::in(['mail', 'database'])],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'trigger_value' => 'valor de ativação',
            'trigger_days' => 'dias de antecedência',
            'notification_channels' => 'canais de notificação',
            'is_active' => 'status',
        ];
    }
}
