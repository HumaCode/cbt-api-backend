<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['question_id', 'option_text', 'is_correct', 'weight'])]
class QuestionOption extends Model
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
            'weight' => 'decimal:2',
        ];
    }

    /**
     * Relationship with Question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
