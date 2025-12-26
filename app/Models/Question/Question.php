<?php

namespace App\Models\Question;

use App\Models\Module\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'module_id',
        'type',
        'question_text',
        'passage',
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
}
