<?php

namespace App\Models\Duolingo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuolingoSection extends Model
{
    public const TAG_READING   = 'R';
    public const TAG_LISTENING = 'L';
    public const TAG_WRITING   = 'W';
    public const TAG_SPEAKING  = 'S';

    protected $fillable = [
        'name',
        'tag',
        'display_order',
        'time_allowed_minutes',
        'description',
    ];

    public function questionTypes(): HasMany
    {
        return $this->hasMany(DuolingoQuestionType::class, 'duolingo_section_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(DuolingoModule::class, 'duolingo_section_id');
    }

    /** Individual subscore key for this section (reading / writing / listening / speaking). */
    public function skillKey(): ?string
    {
        return config("duolingo.grading.skills.{$this->tag}");
    }
}
