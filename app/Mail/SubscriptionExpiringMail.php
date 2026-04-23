<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $planName,
        public string $expiryDate,
        public int    $daysLeft,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠️ Your {$this->planName} plan expires in {$this->daysLeft} days",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-expiring',
        );
    }
}
