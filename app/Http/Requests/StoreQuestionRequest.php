<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'string', 'exists:categories,id'],
            'type' => ['required', 'in:pg,essay,likert'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'content_text' => ['required', 'string'],
            
            // Opsi PG validation
            'options' => ['required_if:type,pg', 'array'],
            'options.*.id' => ['sometimes', 'string', 'exists:question_options,id'],
            'options.*.option_text' => ['required_with:options', 'string'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
            'options.*.weight' => ['sometimes', 'numeric', 'min:0'],

            // Media attachment validation
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => ['file', 'max:5120'], // Max 5MB per file

            // Option attachments validation
            'option_attachments' => ['sometimes', 'array'],
            'option_attachments.*' => ['file', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori soal wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'type.required' => 'Tipe soal wajib dipilih.',
            'type.in' => 'Tipe soal harus pg, essay, atau likert.',
            'difficulty.required' => 'Tingkat kesulitan wajib dipilih.',
            'difficulty.in' => 'Tingkat kesulitan harus easy, medium, atau hard.',
            'content_text.required' => 'Konten soal wajib diisi.',
            'options.required_if' => 'Pilihan jawaban wajib diisi untuk soal Pilihan Ganda (pg).',
            'options.*.option_text.required_with' => 'Teks opsi jawaban wajib diisi.',
        ];
    }
}
