<?php

namespace App\Mail;

use App\Models\Evaluation\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EvaluationAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Evaluation $evaluation
     * @param  string     $recipientType  'evaluator' | 'student'
     */
    public function __construct(
        public Evaluation $evaluation,
        public string     $recipientType,
        public string     $studentName,
        public string     $evaluatorName,
        public string     $examName,
        public string     $moduleName,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'evaluator'
            ? "New {$this->moduleName} evaluation assigned"
            : "Your {$this->moduleName} answers have been sent for evaluation";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.evaluation-assigned',
            with: [
                'evaluation'    => $this->evaluation,
                'recipientType' => $this->recipientType,
                'studentName'   => $this->studentName,
                'evaluatorName' => $this->evaluatorName,
                'examName'      => $this->examName,
                'moduleName'    => $this->moduleName,
            ],
        );
    }
}
