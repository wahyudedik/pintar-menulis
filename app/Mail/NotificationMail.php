<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $notifSubject;
    public string $notifMessage;
    public ?string $actionUrl;
    public ?string $actionLabel;

    public function __construct(string $subject, string $message, ?string $actionUrl = null, ?string $actionLabel = null)
    {
        $this->notifSubject = $subject;
        $this->notifMessage = $message;
        $this->actionUrl    = $actionUrl;
        $this->actionLabel  = $actionLabel ?? 'Lihat Detail';
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->notifSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notification');
    }

    public function attachments(): array
    {
        return [];
    }
}
