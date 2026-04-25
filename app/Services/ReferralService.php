<?php

namespace App\Services;

use App\Models\Payment\Payment;
use App\Models\Referral\ReferralInvitation;
use App\Models\Referral\ReferralProgram;
use App\Models\Referral\ReferralRedemption;
use App\Models\Referral\ReferralUserProfile;
use App\Repositories\Interfaces\ReferralProgramRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    public function __construct(private ReferralProgramRepositoryInterface $programs) {}

    /* ========================================================================
     | Code generation
     |========================================================================*/

    /** Generate or fetch the user's referral code (8 alphanumeric chars after PM-). */
    public function ensureCodeForUser(int $userId): ReferralUserProfile
    {
        $profile = ReferralUserProfile::firstWhere('user_id', $userId);
        if ($profile) return $profile;

        return ReferralUserProfile::create([
            'user_id'       => $userId,
            'referral_code' => $this->generateUniqueCode(),
            'is_active'     => true,
            'generated_at'  => now(),
        ]);
    }

    private function generateUniqueCode(): string
    {
        // Avoid ambiguous chars (0/O, 1/I)
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $body = '';
            for ($j = 0; $j < 8; $j++) {
                $body .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $code = 'PM-' . $body;

            if (!ReferralUserProfile::where('referral_code', $code)->exists()) {
                return $code;
            }
        }
        throw new \RuntimeException('Could not generate unique referral code');
    }

    /* ========================================================================
     | Code lookup / validation
     |========================================================================*/

    public function findReferrerByCode(string $code): ?ReferralUserProfile
    {
        return ReferralUserProfile::where('referral_code', $code)
            ->where('is_active', true)
            ->first();
    }

    /* ========================================================================
     | Sign-up flow: create pending invitation
     |========================================================================*/

    /**
     * Called from RegisterController::register() right after the new user is created.
     * Saves a pending claim — does NOT apply any discount yet.
     *
     * @return ReferralInvitation|null  null if the code is invalid / self-refer / no live program
     */
    public function createPendingInvitation(
        int    $newUserId,
        string $referralCode,
        ?string $sourceChannel = null
    ): ?ReferralInvitation {
        $referrer = $this->findReferrerByCode($referralCode);
        if (!$referrer) return null;

        // Cannot self-refer
        if ($referrer->user_id === $newUserId) return null;

        // Need at least one live program to attach the claim to
        $program = $this->programs->activeOne();
        if (!$program) return null;

        // One referral per new user (enforced by unique on referred_user_id too)
        if (ReferralInvitation::where('referred_user_id', $newUserId)->exists()) return null;

        return ReferralInvitation::create([
            'referral_program_id' => $program->id,
            'referrer_user_id'    => $referrer->user_id,
            'referred_user_id'    => $newUserId,
            'referral_code_used'  => Str::upper($referralCode),
            'status'              => ReferralInvitation::STATUS_PENDING,
            'source_channel'      => $sourceChannel,
        ]);
    }

    /* ========================================================================
     | Checkout / discount preview
     |========================================================================*/

    /**
     * If the user has a pending invitation + this would be their first paid order,
     * returns [program, invitation, discount, finalAmount]. Otherwise returns null.
     */
    public function previewDiscount(int $userId, float $grossAmount): ?array
    {
        $invitation = ReferralInvitation::with('program')
            ->where('referred_user_id', $userId)
            ->pending()
            ->first();

        if (!$invitation) return null;

        $program = $invitation->program;
        if (!$program || !$program->isLive()) return null;

        if ($program->min_first_purchase_amount !== null
            && $grossAmount < (float) $program->min_first_purchase_amount) {
            return null;
        }

        // First paid order check — no other approved payments exist
        $hasApprovedPayment = Payment::where('user_id', $userId)
            ->where('status', Payment::STATUS_APPROVED)
            ->exists();
        if ($hasApprovedPayment) return null;

        $discount = $program->discountFor($grossAmount);
        $final    = max(0, round($grossAmount - $discount, 2));

        return [
            'program'     => $program,
            'invitation'  => $invitation,
            'gross'       => round($grossAmount, 2),
            'discount'    => round($discount, 2),
            'final'       => $final,
            'reward'      => $program->rewardFor($grossAmount),
        ];
    }

    /* ========================================================================
     | First-paid-order: redeem the invitation
     |========================================================================*/

    /**
     * Called from PaymentService::approve() when admin approves a payment.
     * Verifies this is the first paid order for this user, locks the invitation
     * as redeemed, creates the redemption row.
     */
    public function redeemForPayment(Payment $payment): ?ReferralRedemption
    {
        // Only run once per payment
        if (ReferralRedemption::where('payment_id', $payment->id)->exists()) {
            return ReferralRedemption::where('payment_id', $payment->id)->first();
        }

        $invitation = ReferralInvitation::with('program')
            ->where('referred_user_id', $payment->user_id)
            ->pending()
            ->first();

        if (!$invitation || !$invitation->program) return null;

        $program = $invitation->program;

        // First paid check — count *other* approved payments before this one
        $otherApproved = Payment::where('user_id', $payment->user_id)
            ->where('status', Payment::STATUS_APPROVED)
            ->where('id', '!=', $payment->id)
            ->exists();
        if ($otherApproved) {
            // Not the first paid order — mark invitation as expired (cannot redeem)
            $invitation->update(['status' => ReferralInvitation::STATUS_EXPIRED]);
            return null;
        }

        // Calculate discount on the original gross price (the module price).
        // Payment.amount is the already-discounted figure the user actually paid.
        $module   = $payment->module()->first();
        $gross    = $module
            ? (float) $module->price_in_bdt
            : ((float) $payment->amount + 0); // fallback if module is gone
        $discount = $program->discountFor($gross);
        $reward   = $program->rewardFor($gross);

        return DB::transaction(function () use ($invitation, $payment, $discount, $reward) {

            $invitation->update(['status' => ReferralInvitation::STATUS_REDEEMED]);

            return ReferralRedemption::create([
                'referral_invitation_id' => $invitation->id,
                'payment_id'             => $payment->id,
                'referee_user_id'        => $payment->user_id,
                'discount_amount'        => $discount,
                'reward_amount'          => $reward,
                'redeemed_at'            => now(),
            ]);
        });

        // *Note: if you want the redemption to use the pre-discount price,
        // store `gross_amount` on the Payment row at submit time.
    }
}
