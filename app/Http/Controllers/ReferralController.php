<?php

namespace App\Http\Controllers;

use App\Mail\ReferralInviteMail;
use App\Services\Mail\Mailer;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __construct(
        private ReferralService $referrals,
        private Mailer          $mailer
    ) {}

    /** Referral share page for the logged-in user. */
    public function index()
    {
        $user    = auth()->user();
        $profile = $this->referrals->ensureCodeForUser($user->id);

        $code = $profile->referral_code;
        $link = url('/register?ref=' . $code);

        return view('referral.index', [
            'user' => $user,
            'code' => $code,
            'link' => $link,
        ]);
    }

    /** AJAX: send the referral invite to one recipient by email. */
    public function sendEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_email'  => 'required|email|max:120',
            'personal_message' => 'nullable|string|max:500',
        ]);

        $user    = auth()->user();
        $profile = $this->referrals->ensureCodeForUser($user->id);

        $code = $profile->referral_code;
        $link = url('/register?ref=' . $code);

        $mail = new ReferralInviteMail(
            referrerName:    $user->name,
            referralCode:    $code,
            referralLink:    $link,
            personalMessage: $validated['personal_message'] ?? null,
        );

        $ok = $this->mailer->send($validated['recipient_email'], $mail);

        return response()->json([
            'ok'      => $ok,
            'message' => $ok
                ? 'Invite email sent to ' . $validated['recipient_email']
                : 'Could not send the email right now. Please try again.',
        ], $ok ? 200 : 500);
    }
}
