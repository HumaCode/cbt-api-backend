<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'required', 'string', 'exists:categories,id'],
            'type' => ['sometimes', 'required', 'in:pg,essay,likert'],
            'difficulty' => ['sometimes', 'required', 'in:easy,medium,hard'],
            'content_text' => ['sometimes', 'required', 'string'],
            
            // Opsi PG validation
            'options' => ['sometimes', 'array'],
            'options.*.option_text' => ['required_with:options', 'string'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
            'options.*.weight' => ['sometimes', 'numeric', 'min:0'],

            // Media attachment validation
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => ['file', 'max:5120'], // Max 5MB per file
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'type.in' => 'Tipe soal harus pg, essay, atau likert.',
            'difficulty.in' => 'Tingkat kesulitan harus easy, medium, atau hard.',
            'options.*.option_text.required_with' => 'Teks opsi jawaban wajib diisi.',
        ];
    }
}
