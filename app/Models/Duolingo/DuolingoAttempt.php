<?php

namespace App\Models\Duolingo;

use App\Models\Module\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DuolingoAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    /** Submitted; writing and speaking are waiting for an evaluator. */
    public const RESULT_PENDING_EVALUATION = 'pending_evaluation';
    /** Fully scored and visible to the student. */
    public const RESULT_PUBLISHED = 'published';

    protected $fillable = [
        'user_id',
        'module_id',
        'status',
        'result_status',
        'started_at',
        'ended_at',
        'state',
        'last_activity_at',
        'raw_scores',
        'reading_score',
        'writing_score',
        'listening_score',
        'speaking_score',
        'literacy_score',
        'comprehension_score',
        'conversation_score',
        'production_score',
        'overall_score',
        'published_at',
    ];

    protected $casts = [
        'started_at'          => 'datetime',
        'ended_at'            => 'datetime',
        'last_activity_at'    => 'datetime',
        'published_at'        => 'datetime',
        'state'               => 'array',
        'raw_scores'          => 'array',
        'literacy_score'      => 'float',
        'comprehension_score' => 'float',
        'conversation_score'  => 'float',
        'production_score'    => 'float',
    ];

    public function user(): BelongsTo         { return $this->belongsTo(User::class); }
    public function module(): BelongsTo       { return $this->belongsTo(Module::class); }
    public function answers(): HasMany        { return $this->hasMany(DuolingoAnswer::class, 'duolingo_attempt_id'); }
    public function evaluation(): HasOne      { return $this->hasOne(DuolingoEvaluation::class, 'duolingo_attempt_id'); }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isPublished(): bool
    {
        return $this->result_status === self::RESULT_PUBLISHED;
    }
}
