<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PteQuestionSegment extends Model
{
    protected $table = 'pte_question_segments';

    public $timestamps = false;

    protected $fillable = [
        'question_granular_id',
        'segment_text',
        'correct_order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PteQuestionGranular::class, 'question_granular_id');
    }
}
