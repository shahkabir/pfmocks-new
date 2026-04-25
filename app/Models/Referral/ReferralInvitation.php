<?php

namespace App\Models\Referral;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReferralInvitation extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_QUALIFIED = 'qualified';
    public const STATUS_REDEEMED  = 'redeemed';
    public const STATUS_EXPIRED   = 'expired';
    public const STATUS_REJECTED  = 'rejected';

    protected $fillable = [
        'referral_program_id',
        'referrer_user_id',
        'referred_user_id',
        'referral_code_used',
        'status',
        'source_channel',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(ReferralProgram::class, 'referral_program_id');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function redemption(): HasOne
    {
        return $this->hasOne(ReferralRedemption::class);
    }

    public function scopePending($q)
    {
        return $q->where('status', self::STATUS_PENDING);
    }
}
