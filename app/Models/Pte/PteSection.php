<?php

namespace App\Models\Pte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PteSection extends Model
{
    protected $fillable = [
        'name',
        'tag',
        'display_order',
        'time_allowed_minutes',
        'description',
    ];

    public function subTypes(): HasMany
    {
        return $this->hasMany(PteQuestionSubType::class, 'pte_section_id');
    }

    public function pteModules(): HasMany
    {
        return $this->hasMany(PteModule::class, 'pte_section_id');
    }
}
