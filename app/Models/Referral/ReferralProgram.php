<?php

namespace App\Models\Referral;

use Illuminate\Database\Eloquent\Model;

class ReferralProgram extends Model
{
    protected $fillable = [
        'name',
        'referee_discount_type',
        'referee_discount_value',
        'referrer_reward_type',
        'referrer_reward_value',
        'min_first_purchase_amount',
        'max_discount_amount',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'referee_discount_value'    => 'decimal:2',
        'referrer_reward_value'     => 'decimal:2',
        'min_first_purchase_amount' => 'decimal:2',
        'max_discount_amount'       => 'decimal:2',
        'is_active'                 => 'boolean',
        'starts_at'                 => 'datetime',
        'ends_at'                   => 'datetime',
    ];

    /** Whether the program is currently usable (active + within window). */
    public function isLive(): bool
    {
        if (!$this->is_active) return false;

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at   && $now->gt($this->ends_at))   return false;

        return true;
    }

    /** Compute discount for a given gross amount, capped by max_discount_amount. */
    public function discountFor(float $gross): float
    {
        $disc = $this->referee_discount_type === 'percent'
            ? ($gross * (float) $this->referee_discount_value / 100)
            : (float) $this->referee_discount_value;

        if ($this->max_discount_amount !== null) {
            $disc = min($disc, (float) $this->max_discount_amount);
        }
        return min($disc, $gross);
    }

    /** Compute reward (for referrer) on a given gross amount. */
    public function rewardFor(float $gross): ?float
    {
        if (!$this->referrer_reward_type) return null;

        return $this->referrer_reward_type === 'percent'
            ? round($gross * (float) $this->referrer_reward_value / 100, 2)
            : (float) $this->referrer_reward_value;
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
