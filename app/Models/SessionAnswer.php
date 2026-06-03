<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'session_id',
    'question_id',
    'selected_option_id',
    'answer_text',
    'is_correct',
    'score_earned',
])]
class SessionAnswer extends Model
{
    use HasUlids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'score_earned' => 'decimal:2',
        ];
    }

    /**
     * Relationship with Session.
     */
    public function session()
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }

    /**
     * Relationship with Question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Relationship with Selected Option.
     */
    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
