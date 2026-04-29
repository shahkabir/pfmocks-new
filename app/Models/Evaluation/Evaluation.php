<?php

namespace App\Models\Evaluation;

use App\Models\Exam\ExamAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    public const STATUS_ASSIGNED    = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    /** Module types that can currently be evaluated. Extend as needed. */
    public const EVALUATABLE_MODULE_TYPES = ['writing', 'speaking'];

    protected $fillable = [
        'exam_attempt_id',
        'assigned_to',
        'assigned_by',
        'module_type',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
        'band_scores',
        'feedback_text',
        'feedback_audio_path',
    ];

    protected $casts = [
        'band_scores'  => 'array',
        'assigned_at'  => 'datetime',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /** Computes overall band as the rounded average of the criteria scores. */
    public function overallBand(): ?float
    {
        if (empty($this->band_scores)) return null;

        // If overall was explicitly set by the evaluator, prefer it
        if (isset($this->band_scores['overall']) && is_numeric($this->band_scores['overall'])) {
            return round((float) $this->band_scores['overall'], 1);
        }

        $criteria = collect($this->band_scores)
            ->only(['task_achievement', 'coherence_cohesion', 'lexical_resource',
                    'grammatical_range', 'fluency_coherence', 'pronunciation'])
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (float) $v);

        if ($criteria->isEmpty()) return null;

        // IELTS rounding: average rounded to nearest 0.5
        $avg = $criteria->avg();
        return round($avg * 2) / 2;
    }
}
