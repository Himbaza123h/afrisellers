<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $agentName,
        public string $email,
        public string $password
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Welcome to AfriSellers — Your Agent Account is Ready')
            ->view('emails.agent.account-created');
    }
}
