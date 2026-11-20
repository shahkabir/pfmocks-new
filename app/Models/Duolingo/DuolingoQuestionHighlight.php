<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuolingoQuestionHighlight extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'duolingo_question_id',
        'start_offset',
        'end_offset',
        'answer_text',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestion::class, 'duolingo_question_id');
    }
}
