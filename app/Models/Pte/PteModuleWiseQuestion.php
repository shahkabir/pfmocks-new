<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PteModuleWiseQuestion extends Model
{
    protected $table = 'pte_module_wise_question';

    protected $fillable = [
        'pte_module_id',
        'pte_question_granular_id',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pteModule(): BelongsTo
    {
        return $this->belongsTo(PteModule::class, 'pte_module_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PteQuestionGranular::class, 'pte_question_granular_id');
    }
}
