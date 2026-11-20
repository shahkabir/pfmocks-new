<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuolingoModuleWiseQuestion extends Model
{
    protected $table = 'duolingo_module_wise_question';

    protected $fillable = [
        'duolingo_module_id',
        'duolingo_question_id',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function duolingoModule(): BelongsTo
    {
        return $this->belongsTo(DuolingoModule::class, 'duolingo_module_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(DuolingoQuestion::class, 'duolingo_question_id');
    }
}
