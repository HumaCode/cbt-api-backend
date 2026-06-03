<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'assessment_id',
    'user_id',
    'start_time',
    'end_time',
    'is_timer_started',
    'status',
    'total_score',
    'is_certificate_released',
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
            'is_certificate_released' => 'boolean',
        ];
    }

    /**
     * Relationship with Assessment.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Relationship with User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Answers.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(SessionAnswer::class, 'session_id');
    }

    /**
     * Relationship with Proctoring Logs.
     */
    public function proctoringLogs(): HasMany
    {
        return $this->hasMany(AssessmentProctoringLog::class, 'session_id');
    }

    /**
     * Relationship with Certificate.
     */
    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class, 'assessment_session_id');
    }
}

