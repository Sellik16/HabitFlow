<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class WeeklyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user,
        public $pdfContent
    ) {}

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-report-text', // Prosty widok treści maila
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Raport_Tygodniowy.pdf')
                ->withMime('application/pdf'),
        ];
    }
}