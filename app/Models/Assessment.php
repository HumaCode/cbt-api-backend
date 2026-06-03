<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'title',
    'start_date',
    'end_date',
    'duration_minutes',
    'max_attempts',
    'randomize_questions',
    'randomize_options',
    'passing_grade',
    'passing_grade_type',
])]
class Assessment extends Model
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
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'passing_grade' => 'decimal:2',
            'passing_grade_type' => 'string',
        ];
    }

    /**
     * Relationship with Groups.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'assessment_group', 'assessment_id', 'group_id');
    }

    /**
     * Relationship with Questions.
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'assessment_question', 'assessment_id', 'question_id')
                    ->withPivot('order_no');
    }

    /**
     * Relationship with Assessment Sessions.
     */
    public function sessions()
    {
        return $this->hasMany(AssessmentSession::class);
    }
}
