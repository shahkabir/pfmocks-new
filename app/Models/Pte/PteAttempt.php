<?php

namespace App\Models\Pte;

use App\Models\Module\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PteAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    protected $fillable = [
        'user_id',
        'module_id',
        'status',
        'started_at',
        'ended_at',
        'total_marks',
        'intro_audio_url',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'ended_at'    => 'datetime',
        'total_marks' => 'float',
    ];

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function module(): BelongsTo { return $this->belongsTo(Module::class); }
    public function answers(): HasMany  { return $this->hasMany(PteAnswer::class, 'pte_attempt_id'); }
}
