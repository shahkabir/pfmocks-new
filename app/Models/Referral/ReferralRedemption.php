<?php

namespace App\Models\Referral;

use App\Models\Payment\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralRedemption extends Model
{
    protected $fillable = [
        'referral_invitation_id',
        'payment_id',
        'referee_user_id',
        'discount_amount',
        'reward_amount',
        'redeemed_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'reward_amount'   => 'decimal:2',
        'redeemed_at'     => 'datetime',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(ReferralInvitation::class, 'referral_invitation_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_user_id');
    }
}
