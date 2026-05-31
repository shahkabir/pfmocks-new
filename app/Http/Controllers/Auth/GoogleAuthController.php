<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /** Kick off the OAuth flow */
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /** Handle the callback: sign in or create a brand-new user */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google sign-in failed: ' . $e->getMessage());
        }

        // Match by google_id first, then fall back to email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (!$user) {
            // New account — created with implicit SMS-terms acceptance via Google sign-up
            $user = User::create([
                'name'                  => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
                'email'                 => $googleUser->getEmail(),
                'google_id'             => $googleUser->getId(),
                'avatar'                => $googleUser->getAvatar(),
                'password'              => bcrypt(Str::random(32)),
                'role'                  => 'user',
                'is_verified'           => true,
                'sms_terms_accepted_at' => now(),
            ]);
        } else {
            // Existing user — backfill google_id/avatar so future logins are clean
            $user->forceFill([
                'google_id'   => $googleUser->getId(),
                'avatar'      => $user->avatar ?: $googleUser->getAvatar(),
                'is_verified' => true,
            ])->save();
        }

        Auth::login($user, true);
        session(['otp_user_id' => $user->id]); // satisfy the existing dashboard router

        return redirect()->route('dashboard.student');
    }
}
