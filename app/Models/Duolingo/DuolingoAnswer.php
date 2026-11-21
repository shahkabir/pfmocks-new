<?php

namespace App\Models\Duolingo;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuolingoAnswer extends Model
{
    protected $fillable = [
        'duolingo_attempt_id',
        'duolingo_module_wise_question_id',
        'duolingo_question_id',
        'response_text',
        'response_texts',
        'response_audio_url',
        'response_audio_urls',
        'selected_option_ids',
        'response_blanks',
        'response_highlight',
        'raw_score',
        'max_score',
        'is_correct',
        'score_detail',
        'rubric_scores',
        'feedback',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'response_texts'      => 'array',
        'response_audio_urls' => 'array',
        'selected_option_ids' => 'array',
        'response_blanks'     => 'array',
        'response_highlight'  => 'array',
        'rubric_scores'       => 'array',
        'score_detail'        => 'array',
        'raw_score'           => 'float',
        'max_score'           => 'float',
        'is_correct'          => 'boolean',
        'evaluated_at'        => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(DuolingoAttempt::class, 'duolingo_attempt_id');
    }

    public function mapping(): BelongsTo
    {
        return $this->belongsTo(DuolingoModuleWiseQuestion::class, 'duolingo_module_wise_question_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestion::class, 'duolingo_question_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function isEvaluated(): bool
    {
        return $this->evaluated_at !== null;
    }
}
