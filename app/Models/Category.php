<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'parent_id'])]
class Category extends Model
{
    use HasUlids;

    /**
     * Relationship with Parent Category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relationship with Child Categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relationship with Questions.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
