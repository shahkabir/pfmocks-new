<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuolingoQuestionOption extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'duolingo_question_id',
        'option_text',
        'is_correct',
        'display_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestion::class, 'duolingo_question_id');
    }
}
