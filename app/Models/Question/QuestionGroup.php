<?php

namespace App\Models\Question;

use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionGroup extends Model
{
    protected $table = 'question_groups';

    protected $fillable = [
        'question_id',
        'question_options_group_ids',
        'part_number',
    ];

    protected $casts = [
        'question_options_group_ids' => 'array',
        'part_number' => 'integer',
    ];

    /**
     * 1:1 mapping with Question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(QuestionGroupBlock::class)
                    ->orderBy('sort_order');
    }
}
