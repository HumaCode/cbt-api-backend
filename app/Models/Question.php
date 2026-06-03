<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['category_id', 'type', 'difficulty', 'content_text', 'created_by'])]
class Question extends Model implements HasMedia
{
    use HasUlids, InteractsWithMedia;

    /**
     * Relationship with Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship with Creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with Options.
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Relationship with Assessments.
     */
    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'assessment_question', 'question_id', 'assessment_id')
                    ->withPivot('order_no');
    }
}

