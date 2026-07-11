<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PteAnswer extends Model
{
    protected $fillable = [
        'pte_attempt_id',
        'pte_module_wise_question_id',
        'pte_question_granular_id',
        'response_text',
        'response_audio_url',
        'selected_option_ids',
        'response_blanks',
        'response_segments_order',
        'response_highlight_word_ids',
        'score',
    ];

    protected $casts = [
        'selected_option_ids'         => 'array',
        'response_blanks'             => 'array',
        'response_segments_order'     => 'array',
        'response_highlight_word_ids' => 'array',
        'score'                       => 'float',
    ];

    public function attempt(): BelongsTo  { return $this->belongsTo(PteAttempt::class, 'pte_attempt_id'); }
    public function question(): BelongsTo { return $this->belongsTo(PteQuestionGranular::class, 'pte_question_granular_id'); }
    public function mapping(): BelongsTo  { return $this->belongsTo(PteModuleWiseQuestion::class, 'pte_module_wise_question_id'); }
}
