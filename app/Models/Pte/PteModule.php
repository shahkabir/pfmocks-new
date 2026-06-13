<?php

namespace App\Models\Pte;

use App\Models\Module\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PteModule extends Model
{
    protected $table = 'pte_module';

    protected $fillable = [
        'module_id',
        'pte_section_id',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PteSection::class, 'pte_section_id');
    }

    public function moduleQuestions(): HasMany
    {
        return $this->hasMany(PteModuleWiseQuestion::class, 'pte_module_id');
    }
}
