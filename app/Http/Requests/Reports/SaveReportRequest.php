<?php

namespace App\Http\Requests\Reports;

class SaveReportRequest extends GenerateReportRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_favorite' => ['nullable', 'boolean'],
            'is_template' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'O nome do relatório é obrigatório.',
            'name.max' => 'O nome do relatório não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        ]);
    }
}
