<?php

namespace App\Models\Module;

use App\Models\Exam\Exam;
use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    protected $fillable = [
        'exam_id',
        'name',
        'module_type',
        'type', //free, paid
        'price_in_bdt',
        'price_in_usd',
        'duration_minutes',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function timestamps()
    {
        return $this->hasTimestamps();
    }
}
