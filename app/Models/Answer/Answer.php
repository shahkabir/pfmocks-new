<?php

namespace App\Models\Answer;

use App\Models\Exam\ExamAttempt;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'answer',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Answer belongs to an exam attempt
     */
    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    /**
     * Answer belongs to a question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
