<?php

namespace App\Models\Duolingo;

use App\Models\Module\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuolingoModule extends Model
{
    /** modules.module_type value that routes a module to the Duolingo player. */
    public const MODULE_TYPE = 'duolingo';

    protected $table = 'duolingo_module';

    protected $fillable = [
        'module_id',
        'duolingo_section_id',
        'time_allowed_minutes',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(DuolingoSection::class, 'duolingo_section_id');
    }

    public function moduleQuestions(): HasMany
    {
        return $this->hasMany(DuolingoModuleWiseQuestion::class, 'duolingo_module_id');
    }
}
