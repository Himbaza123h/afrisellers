<?php

namespace App\Mail;

use App\Models\ServiceDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceDelivery $delivery) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Your Service Has Been Delivered — ' . $this->delivery->service_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-delivered',
        );
    }
}
