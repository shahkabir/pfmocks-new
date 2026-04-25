<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $referrerName,
        public string $referralCode,
        public string $referralLink,
        public ?string $personalMessage = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->referrerName} invited you to join PerfectMocks",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.referral-invite',
            with: [
                'referrerName'    => $this->referrerName,
                'referralCode'    => $this->referralCode,
                'referralLink'    => $this->referralLink,
                'personalMessage' => $this->personalMessage,
            ],
        );
    }
}
