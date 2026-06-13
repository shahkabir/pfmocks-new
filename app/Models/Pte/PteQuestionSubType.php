<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PteQuestionSubType extends Model
{
    protected $fillable = [
        'pte_section_id',
        'name',
        'tag',
        'response_type',
        'stimulus_type',
        'preparation_time_sec_default',
        'answer_time_sec_default',
        'marks_default',
        'typical_count_in_exam',
        'instructions',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PteSection::class, 'pte_section_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PteQuestionGranular::class, 'pte_sub_type_id');
    }
}
