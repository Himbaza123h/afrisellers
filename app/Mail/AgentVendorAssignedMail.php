<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentVendorAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $agentName,
        public string $vendorBusinessName,
        public string $vendorCity,
        public string $vendorCountry
    ) {}

    public function build(): self
    {
        return $this
            ->subject('New Vendor Assigned to You — AfriSellers')
            ->view('emails.agent.vendor-assigned');
    }
}
