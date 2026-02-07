<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionGroupBlock extends Model
{
    protected $table = 'question_group_blocks';

    protected $fillable = [
        'question_group_id',
        'instruction_text',
        'question_option_ids',
        'sort_order',
    ];

    protected $casts = [
        'question_option_ids' => 'array',
    ];

    /**
     * Block belongs to a question group
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(QuestionGroup::class, 'question_group_id');
    }
}

