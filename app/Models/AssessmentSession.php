<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'assessment_id',
    'user_id',
    'start_time',
    'end_time',
    'is_timer_started',
    'status',
    'total_score',
])]
class AssessmentSession extends Model
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
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_timer_started' => 'boolean',
            'total_score' => 'decimal:2',
        ];
    }

    /**
     * Relationship with Assessment.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Relationship with User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Answers.
     */
    public function answers()
    {
        return $this->hasMany(SessionAnswer::class, 'session_id');
    }

    /**
     * Relationship with Proctoring Logs.
     */
    public function proctoringLogs()
    {
        return $this->hasMany(AssessmentProctoringLog::class, 'session_id');
    }

    /**
     * Relationship with Certificate.
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class, 'assessment_session_id');
    }
}
