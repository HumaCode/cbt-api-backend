<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['session_id', 'event_type', 'event_details'])]
class AssessmentProctoringLog extends Model
{
    use HasUlids;

    /**
     * Disable default model timestamps.
     */
    public $timestamps = false;

    /**
     * Relationship with Session.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }
}

