<?php

namespace Kssadi\LogTracker\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LogAlertMail extends Mailable
{
    /**
     * @param  array{file: string, level: string, count: int, threshold: int, window_minutes: int}  $payload
     */
    public function __construct(
        public readonly array $payload,
        public readonly string $body,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Log Tracker] '.strtoupper($this->payload['level']).' alert in '.$this->payload['file'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'log-tracker::mail.alert',
            with: [
                'payload' => $this->payload,
                'body' => $this->body,
            ],
        );
    }
}
