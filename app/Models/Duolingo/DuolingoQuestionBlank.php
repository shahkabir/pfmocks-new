<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuolingoQuestionBlank extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'duolingo_question_id',
        'blank_order',
        'visible_prefix',
        'correct_answer',
        'accepted_variants',
    ];

    protected $casts = [
        'accepted_variants' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestion::class, 'duolingo_question_id');
    }
}
