<?php

namespace App\Http\Requests\Reports;

use App\Enums\ReportTypeEnum;
use App\Enums\VisualizationTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportRequest extends FormRequest
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
            'report_type' => ['required', 'string', Rule::in(array_column(ReportTypeEnum::cases(), 'value'))],
            'visualization_type' => ['nullable', 'string', Rule::in(array_column(VisualizationTypeEnum::cases(), 'value'))],

            // Filters
            'start_date' => ['nullable', 'date', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'period_type' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'wallet_ids' => ['nullable', 'array'],
            'wallet_ids.*' => ['integer', 'exists:wallets,id'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'min:0', 'gte:min_amount'],
            'status' => ['nullable', 'string', Rule::in(['all', 'paid', 'pending'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'report_type.required' => 'O tipo de relatório é obrigatório.',
            'report_type.in' => 'O tipo de relatório selecionado é inválido.',
            'start_date.before_or_equal' => 'A data inicial deve ser anterior ou igual à data final.',
            'end_date.after_or_equal' => 'A data final deve ser posterior ou igual à data inicial.',
            'category_ids.*.exists' => 'Uma ou mais categorias selecionadas não existem.',
            'wallet_ids.*.exists' => 'Uma ou mais carteiras selecionadas não existem.',
            'max_amount.gte' => 'O valor máximo deve ser maior ou igual ao valor mínimo.',
        ];
    }
}
