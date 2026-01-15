<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendorName;
    public $verificationToken;
    public $verificationUrl;

    public function __construct($vendorName, $verificationToken)
    {
        $this->vendorName = $vendorName;
        $this->verificationToken = $verificationToken;
        $this->verificationUrl = route('vendor.verify.email', ['token' => $verificationToken]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email - Afrisellers Vendor Registration',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-verification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
