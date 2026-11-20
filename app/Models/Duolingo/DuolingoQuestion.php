<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuolingoQuestion extends Model
{
    protected $fillable = [
        'duolingo_question_type_id',
        'duolingo_question_group_id',
        'group_order',
        'question_code',
        'question_text',
        'audio_transcript',
        'audio_url',
        'image_url',
        'image_alt_text',
        'preparation_time_sec',
        'answer_time_sec',
        'max_replays',
        'min_word_count',
        'max_word_count',
        'config',
        'correct_ans',
        'accepted_variants',
        'correct_ans_explanation',
        'marks',
        'difficulty',
        'topic_tags',
        'source_reference',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'config'            => 'array',
        'accepted_variants' => 'array',
        'topic_tags'        => 'array',
        'marks'             => 'float',
        'is_active'         => 'boolean',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestionType::class, 'duolingo_question_type_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestionGroup::class, 'duolingo_question_group_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(DuolingoQuestionOption::class, 'duolingo_question_id')->orderBy('display_order');
    }

    public function blanks(): HasMany
    {
        return $this->hasMany(DuolingoQuestionBlank::class, 'duolingo_question_id')->orderBy('blank_order');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(DuolingoQuestionHighlight::class, 'duolingo_question_id');
    }

    public function turns(): HasMany
    {
        return $this->hasMany(DuolingoQuestionTurn::class, 'duolingo_question_id')->orderBy('turn_order');
    }

    /** Effective answer time: question override, else the type default. */
    public function answerTimeSec(): ?int
    {
        return $this->answer_time_sec ?? $this->type?->answer_time_sec_default;
    }

    /** Effective replay allowance: question override, else the type default. */
    public function maxReplays(): ?int
    {
        return $this->max_replays ?? $this->type?->max_replays_default;
    }
}
