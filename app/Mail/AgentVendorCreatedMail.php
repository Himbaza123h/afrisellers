<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentVendorCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $agentName,
        public string $vendorName,
        public string $vendorEmail
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Vendor Added — ' . $this->vendorName)
            ->view('emails.agent.vendor-created');
    }
}
