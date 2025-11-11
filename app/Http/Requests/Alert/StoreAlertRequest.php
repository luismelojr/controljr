<?php

namespace App\Http\Requests\Alert;

use App\Enums\AlertTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlertRequest extends FormRequest
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
            'type' => ['required', Rule::enum(AlertTypeEnum::class)],
            'alertable_type' => ['nullable', 'string'],
            'alertable_id' => ['nullable', 'integer'],
            'trigger_value' => [
                'nullable',
                'required_if:type,credit_card_usage,account_balance',
                'numeric',
                'min:0',
                'max:100',
            ],
            'trigger_days' => [
                'nullable',
                'required_if:type,bill_due_date',
                'array',
            ],
            'trigger_days.*' => ['integer', 'min:1', 'max:365'],
            'notification_channels' => ['required', 'array'],
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
            'type' => 'tipo de alerta',
            'alertable_type' => 'tipo de recurso',
            'alertable_id' => 'recurso',
            'trigger_value' => 'valor de ativação',
            'trigger_days' => 'dias de antecedência',
            'notification_channels' => 'canais de notificação',
            'is_active' => 'status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'trigger_value.required_if' => 'O valor de ativação é obrigatório para este tipo de alerta.',
            'trigger_days.required_if' => 'Os dias de antecedência são obrigatórios para alertas de vencimento.',
            'notification_channels.required' => 'Selecione pelo menos um canal de notificação.',
        ];
    }
}
