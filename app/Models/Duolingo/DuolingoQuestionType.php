<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuolingoQuestionType extends Model
{
    public const SCORING_OBJECTIVE = 'objective';
    public const SCORING_RUBRIC    = 'rubric';

    protected $fillable = [
        'duolingo_section_id',
        'name',
        'tag',
        'response_type',
        'stimulus_type',
        'scoring_method',
        'task_group',
        'preparation_time_sec_default',
        'answer_time_sec_default',
        'max_replays_default',
        'turns_default',
        'instructions',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(DuolingoSection::class, 'duolingo_section_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DuolingoQuestion::class, 'duolingo_question_type_id');
    }

    public function isObjective(): bool
    {
        return $this->scoring_method === self::SCORING_OBJECTIVE;
    }
}
