<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PteQuestionGranular extends Model
{
    protected $table = 'pte_question_granular';

    protected $fillable = [
        'pte_sub_type_id',
        'question_granular_id',
        'question_text',
        'audio_transcript',
        'audio_url',
        'image_url',
        'image_alt_text',
        'preparation_time_sec',
        'answer_time_sec',
        'marks',
        'correct_ans',
        'correct_ans_explanation',
        'min_word_count',
        'max_word_count',
        'difficulty',
        'topic_tags',
        'source_reference',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'topic_tags' => 'array',
        'is_active'  => 'boolean',
    ];

    public function subType(): BelongsTo
    {
        return $this->belongsTo(PteQuestionSubType::class, 'pte_sub_type_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PteQuestionOption::class, 'question_granular_id');
    }

    public function blanks(): HasMany
    {
        return $this->hasMany(PteQuestionBlank::class, 'question_granular_id');
    }

    public function segments(): HasMany
    {
        return $this->hasMany(PteQuestionSegment::class, 'question_granular_id');
    }

    public function highlightWords(): HasMany
    {
        return $this->hasMany(PteQuestionHighlightWord::class, 'question_granular_id');
    }
}
