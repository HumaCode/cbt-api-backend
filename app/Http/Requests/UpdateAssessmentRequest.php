<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'duration_minutes' => ['sometimes', 'required', 'integer', 'min:1'],
            'max_attempts' => ['sometimes', 'integer', 'min:1'],
            'randomize_questions' => ['sometimes', 'boolean'],
            'randomize_options' => ['sometimes', 'boolean'],
            'passing_grade' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            
            // Pivot syncing validation
            'group_ids' => ['sometimes', 'array'],
            'group_ids.*' => ['string', 'exists:groups,id'],
            'questions' => ['sometimes', 'array'],
            'questions.*' => ['string', 'exists:questions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul ujian wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib ditentukan.',
            'end_date.required' => 'Tanggal selesai wajib ditentukan.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'duration_minutes.required' => 'Durasi ujian wajib ditentukan.',
            'group_ids.*.exists' => 'Group/kelompok tidak valid.',
            'questions.*.exists' => 'Soal tidak valid.',
        ];
    }
}
