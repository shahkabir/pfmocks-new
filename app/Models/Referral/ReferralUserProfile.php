<?php

namespace App\Models\Referral;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralUserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'referral_code',
        'is_active',
        'generated_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
