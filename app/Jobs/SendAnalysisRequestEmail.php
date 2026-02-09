<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\AnalysisRequestSubmitted;
use Illuminate\Support\Facades\Mail;

class SendAnalysisRequestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $university,
        public string $file_subject,
        public string $originalFilename
    ) {}

    public function handle(): void
    {
        Mail::to($this->email)->send(new AnalysisRequestSubmitted(
            name: $this->name,
            email: $this->email,
            phone: $this->phone,
            university: $this->university,
            file_subject: $this->file_subject,
            originalFilename: $this->originalFilename
        ));
    }
}
