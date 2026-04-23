<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentVendorRemovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $agentName,
        public string $vendorBusinessName
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Vendor Removed From Your Account — AfriSellers')
            ->view('emails.agent.vendor-removed');
    }
}
