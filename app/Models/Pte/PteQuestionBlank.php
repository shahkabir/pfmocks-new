<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PteQuestionBlank extends Model
{
    protected $table = 'pte_question_blanks';

    public $timestamps = false;

    protected $fillable = [
        'question_granular_id',
        'blank_order',
        'correct_answer',
        'accepted_variants',
        'dropdown_options',
    ];

    protected $casts = [
        'accepted_variants' => 'array',
        'dropdown_options'  => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PteQuestionGranular::class, 'question_granular_id');
    }
}
