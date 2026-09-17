<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CompanyFeedbackMail extends Mailable
{
    use Queueable;

    public function __construct(
        public string $company,
        public string $reportSubject,
        public string $reportBody,
        public string $sender,
        public string $attachmentData,
        public string $attachmentName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->reportSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.company-feedback');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn (): string => $this->attachmentData, $this->attachmentName)
                ->withMime('application/pdf'),
        ];
    }
}
