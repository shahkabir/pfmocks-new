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
        'question_type', //'generic_question','mcq_single','mcq_multiple','fill_in_blanks''writing',
                         // 'essay','audio','speaking', 'highlighting','reordering', 
                         // 'no_question' (for display only question like in IELTS Listening)
                         // 'ielts_speaking' (for IELTS Speaking question which has no question text but only question image and audio)
        'option_text',
        'is_correct',
        'correct_answer_explanation',
        'correct_answer_fib', //for fill in the blanks question correct answer text
        'sort_order',
        'is_active',
        //'ielts_listening_question_line', //Question that has no marks but for display only for IELTS Listening
        'question_image_path',
        'question_audio_path'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_correct' => 'boolean',
        'is_active' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
