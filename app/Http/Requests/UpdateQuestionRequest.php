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

            // Specific media deletion validation
            'deleted_media_ids' => ['sometimes', 'array'],
            'deleted_media_ids.*' => ['string', 'exists:media,id'],
            'options.*.clear_image' => ['sometimes', 'boolean'],
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
