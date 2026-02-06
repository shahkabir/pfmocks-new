<?php

namespace App\Models\Question;

use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOptions extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'question_options';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'question_id',
        'actual_question', //questions for all type - Reading, Listening, Writing, Speaking
        'option_text',
        'is_correct',
        'correct_answer_explanation',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * A question option belongs to a question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Scope: order options inside a question
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
