<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'max:5120', // 5MB
                'mimes:pdf,jpg,jpeg,png,gif,webp,xls,xlsx,doc,docx,txt,csv',
            ],
            'attachable_type' => 'required|string|in:App\Models\Transaction,App\Models\Income,App\Models\IncomeTransaction',
            'attachable_id' => 'required|integer|exists:' . $this->getTableName() . ',id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Por favor, selecione um arquivo.',
            'file.file' => 'O arquivo enviado não é válido.',
            'file.max' => 'O arquivo não pode ser maior que 5MB.',
            'file.mimes' => 'O arquivo deve ser do tipo: PDF, JPG, PNG, GIF, WEBP, Excel, Word, TXT ou CSV.',
            'attachable_type.required' => 'O tipo de anexo é obrigatório.',
            'attachable_type.in' => 'O tipo de anexo é inválido.',
            'attachable_id.required' => 'O ID do anexo é obrigatório.',
            'attachable_id.exists' => 'O item não foi encontrado.',
        ];
    }

    /**
     * Get the table name based on attachable_type
     */
    protected function getTableName(): string
    {
        $typeMap = [
            'App\Models\Transaction' => 'transactions',
            'App\Models\Income' => 'incomes',
            'App\Models\IncomeTransaction' => 'income_transactions',
        ];

        return $typeMap[$this->input('attachable_type')] ?? 'transactions';
    }
}
