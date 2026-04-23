<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Congratulations! Your Agent Request Has Been Approved — AfriSellers')
            ->view('emails.agent.request-approved');
    }
}
