<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PteQuestionHighlightWord extends Model
{
    protected $table = 'pte_question_highlight_words';

    public $timestamps = false;

    protected $fillable = [
        'question_granular_id',
        'word_text',
        'word_order',
        'is_incorrect',
    ];

    protected $casts = [
        'is_incorrect' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PteQuestionGranular::class, 'question_granular_id');
    }
}
