<?php

namespace App\Models\Duolingo;

use App\Models\Module\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuolingoAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    protected $fillable = [
        'user_id',
        'module_id',
        'status',
        'started_at',
        'ended_at',
        'raw_scores',
        'reading_score',
        'writing_score',
        'listening_score',
        'speaking_score',
        'literacy_score',
        'comprehension_score',
        'conversation_score',
        'production_score',
        'overall_score',
    ];

    protected $casts = [
        'started_at'          => 'datetime',
        'ended_at'            => 'datetime',
        'raw_scores'          => 'array',
        'literacy_score'      => 'float',
        'comprehension_score' => 'float',
        'conversation_score'  => 'float',
        'production_score'    => 'float',
    ];

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function module(): BelongsTo { return $this->belongsTo(Module::class); }
    public function answers(): HasMany  { return $this->hasMany(DuolingoAnswer::class, 'duolingo_attempt_id'); }
}
