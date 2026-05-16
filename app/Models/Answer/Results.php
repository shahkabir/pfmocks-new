<?php

namespace App\Models\Answer;

use App\Models\Exam\ExamAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Results extends Model
{
    protected $fillable = [
        'user_id',
        'exam_attempt_id',
        'exam_name',
        'evaluated_by',
        'status', //'pending, completed, evaluated, manual_review'
        'module_name',
        'achieved_score',
        'total_score', //not applicable for IELTS writing, speaking as they are manually evaluated, but can be used for other exams
        'score_percentage',
        'band_score',
        'time_taken_seconds',
        'evaluator_feedback',
        'admin_feedback',
        'breakdown',
    ];

    protected $casts = [
        'breakdown' => 'array',
        'score_percentage' => 'float',
        'band_score' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function calculatePercentage(): float
    {
        if ($this->total_score == 0) {
            return 0;
        }

        return round(($this->achieved_score / $this->total_score) * 100, 2);
    }

    public function formattedTime(): string
    {
        return gmdate('H:i:s', $this->time_taken_seconds);
    }

    public function passed(int $passMark = 50): bool
    {
        return $this->score_percentage >= $passMark;
    }

    public function formatIELTSBand(): string
    {
        return number_format(ceil($this->band_score), 1); //Ceil the IELTS band score to the nearest 0.1
    }
}
