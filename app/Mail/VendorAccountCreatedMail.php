<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $vendorName,
        public string $email,
        public string $password
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Welcome to AfriSellers — Your Vendor Account is Ready')
            ->view('emails.vendor.account-created');
    }
}
