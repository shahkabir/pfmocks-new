<?php

namespace App\Models\Duolingo;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Evaluator review of the writing and speaking responses in one Duolingo attempt. */
class DuolingoEvaluation extends Model
{
    public const STATUS_PENDING     = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    protected $fillable = [
        'duolingo_attempt_id',
        'assigned_to',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
        'feedback_text',
    ];

    protected $casts = [
        'assigned_at'  => 'datetime',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(DuolingoAttempt::class, 'duolingo_attempt_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
