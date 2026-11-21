<?php

namespace App\Mail\Duolingo;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DuolingoResultPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $moduleName,
        public ?int   $overallScore,
        public string $resultUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Your {$this->moduleName} result is ready");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.duolingo.result-published');
    }
}
