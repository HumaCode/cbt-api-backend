<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'description'])]
class Group extends Model
{
    use HasUlids;

    /**
     * Relationship with Users.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
    }

    /**
     * Relationship with Assessments.
     */
    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'assessment_group', 'group_id', 'assessment_id');
    }
}

