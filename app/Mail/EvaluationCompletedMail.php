<?php

namespace App\Mail;

use App\Models\Evaluation\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EvaluationCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Evaluation $evaluation,
        public string     $studentName,
        public string     $examName,
        public string     $moduleName,
        public string     $resultUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->moduleName} evaluation is ready!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.evaluation-completed',
            with: [
                'evaluation'  => $this->evaluation,
                'studentName' => $this->studentName,
                'examName'    => $this->examName,
                'moduleName'  => $this->moduleName,
                'resultUrl'   => $this->resultUrl,
                'overallBand' => $this->evaluation->overallBand(),
            ],
        );
    }
}
