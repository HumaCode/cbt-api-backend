<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'parent_id', 'passing_grade'])]
class Category extends Model
{
    use HasUlids;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'passing_grade' => 'decimal:2',
        ];
    }

    /**
     * Relationship with Parent Category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relationship with Child Categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relationship with Questions.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}

