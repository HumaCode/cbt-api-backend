<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'max_attempts' => ['sometimes', 'integer', 'min:1'],
            'randomize_questions' => ['sometimes', 'boolean'],
            'randomize_options' => ['sometimes', 'boolean'],
            'passing_grade' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'passing_grade_type' => ['sometimes', 'in:overall,per_category'],
            'certificate_release_mode' => ['sometimes', 'in:auto,manual'],
            'certificate_template' => ['sometimes', 'nullable', 'string'],

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
            'duration_minutes.integer' => 'Durasi harus berupa angka.',
            'group_ids.*.exists' => 'Group/kelompok tidak valid.',
            'questions.*.exists' => 'Soal tidak valid.',
        ];
    }
}
