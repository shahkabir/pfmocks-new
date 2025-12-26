<?php

namespace App\Models\Exam;

use App\Models\Module\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * An exam has many modules (Reading, Writing, etc.)
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }
}
