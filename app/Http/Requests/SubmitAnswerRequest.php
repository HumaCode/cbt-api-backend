<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'string', 'exists:questions,id'],
            'selected_option_id' => ['nullable', 'string', 'exists:question_options,id'],
            'answer_text' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'question_id.required' => 'ID Soal wajib disertakan.',
            'question_id.exists' => 'Soal tidak valid.',
            'selected_option_id.exists' => 'Pilihan jawaban tidak valid.',
        ];
    }
}
