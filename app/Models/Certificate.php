<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'assessment_session_id',
    'user_id',
    'certificate_number',
    'issue_date',
    'file_path',
])]
class Certificate extends Model
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
            'issue_date' => 'datetime',
        ];
    }

    /**
     * Relationship with Assessment Session.
     */
    public function assessmentSession()
    {
        return $this->belongsTo(AssessmentSession::class, 'assessment_session_id');
    }

    /**
     * Relationship with User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
