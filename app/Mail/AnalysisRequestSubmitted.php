<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnalysisRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $university,
        public string $subject,
        public string $originalFilename
    ) {
    }

    public function build(): self
    {
        return $this->subject('Confirmation de réception de votre demande')
            ->view('emails.analysis-request-submitted');
    }
}
