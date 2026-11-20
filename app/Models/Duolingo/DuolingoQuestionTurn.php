<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuolingoQuestionTurn extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'duolingo_question_id',
        'turn_order',
        'speaker',
        'prompt_text',
        'audio_url',
        'response_time_sec',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestion::class, 'duolingo_question_id');
    }
}
