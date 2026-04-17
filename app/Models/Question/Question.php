<?php

namespace App\Models\Question;

use App\Models\Answer\Answer;
use App\Models\Module\Module;
use App\Models\Question\QuestionGroup;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question\QuestionOptions;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'module_id',
        'type',
        'question_header',
        'passage', //contains reading/listening passage
        'passage_instruction',
        'audio_url',
        'image_url',
        'marks',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * A question belongs to a module
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * A question has many options
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOptions::class);
    }

    /**
     * A question can have many answers (one per attempt)
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Scope: order questions inside a module
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * A question has one UI group mapping
     */
    public function group(): HasOne
    {
        return $this->hasOne(QuestionGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('options', function ($q) {
            $q->where('is_active', 1);
        });
    }
}
