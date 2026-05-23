<?php

namespace App\Models\Scholarship;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Scholarship extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'country_code',
        'country_name',
        'funding_type',
        'program_level',
        'deadline',
        'short_description',
        'eligibility_criteria',
        'benefits',
        'required_documents',
        'official_link_1',
        'official_link_2',
        'is_active',
        'is_featured',
        'created_by',
    ];

    protected $casts = [
        'deadline'    => 'date',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    /** Auto-generate a slug from the title if not supplied. */
    protected static function booted(): void
    {
        static::saving(function (Scholarship $s) {
            if (empty($s->slug) && !empty($s->title)) {
                $base = Str::slug($s->title);
                $slug = $base;
                $i = 2;
                while (
                    static::where('slug', $slug)
                          ->where('id', '!=', $s->id ?? 0)
                          ->exists()
                ) {
                    $slug = $base . '-' . $i++;
                }
                $s->slug = $slug;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Days remaining until the deadline (negative if past). */
    public function daysToDeadline(): ?int
    {
        if (!$this->deadline) return null;
        return (int) now()->startOfDay()->diffInDays($this->deadline->startOfDay(), false);
    }

    public function isExpired(): bool
    {
        $d = $this->daysToDeadline();
        return $d !== null && $d < 0;
    }

    /** URL for a country flag (flagcdn.com — public-domain). */
    public function flagUrl(int $width = 80): ?string
    {
        if (!$this->country_code) return null;
        return 'https://flagcdn.com/w' . $width . '/' . strtolower($this->country_code) . '.png';
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeUpcoming($q)
    {
        return $q->whereNull('deadline')->orWhere('deadline', '>=', today());
    }
}
