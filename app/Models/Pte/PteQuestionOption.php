<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PteQuestionOption extends Model
{
    protected $table = 'pte_question_options';

    public $timestamps = false;

    protected $fillable = [
        'question_granular_id',
        'option_text',
        'is_correct',
        'display_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PteQuestionGranular::class, 'question_granular_id');
    }
}
