<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Shared passage / conversation of an Interactive Reading or Interactive Listening activity.
 */
class DuolingoQuestionGroup extends Model
{
    public const INTERACTIVE_READING   = 'interactive_reading';
    public const INTERACTIVE_LISTENING = 'interactive_listening';

    protected $fillable = [
        'task_group',
        'title',
        'passage_text',
        'audio_url',
        'audio_transcript',
        'scenario',
        'time_limit_sec',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(DuolingoQuestion::class, 'duolingo_question_group_id')->orderBy('group_order');
    }
}
