<?php

namespace App\Services\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Centralised mailer helper.
 *
 * All outbound mail in the app should go through Mailer::send() so we get
 *   - one place to apply the audit BCC (gorrjon.official@gmail.com)
 *   - one place for retry / suppress logic later
 *   - one place to swap mail driver per env
 *
 * Usage:
 *   app(Mailer::class)->send($recipient, new ReferralInviteMail($code, $sender));
 *   // or static:
 *   Mailer::dispatch($recipient, $mailable);
 */
class Mailer
{
    /** Permanent BCC recipient required on every outgoing mail. */
    public const AUDIT_BCC = 'gorrjon.official@gmail.com';

    /**
     * Send a Mailable to a single recipient (or array of recipients).
     * Always copies AUDIT_BCC.
     *
     * @param  string|array  $to       email address or [email => name] map
     * @param  Mailable      $mailable  prepared Mail class instance
     * @param  array         $extraCc   optional extra CC list
     */
    public function send(string|array $to, Mailable $mailable, array $extraCc = []): bool
    {
        try {
            $mailable->bcc(self::AUDIT_BCC);

            if (!empty($extraCc)) {
                $mailable->cc($extraCc);
            }

            Mail::to($to)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error('[Mailer] send failed', [
                'to'        => $to,
                'mailable'  => $mailable::class,
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /** Static convenience wrapper. */
    public static function dispatch(string|array $to, Mailable $mailable, array $extraCc = []): bool
    {
        return app(self::class)->send($to, $mailable, $extraCc);
    }
}
