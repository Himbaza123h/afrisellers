<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentRequestRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $reason
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Update on Your Agent Request — AfriSellers')
            ->view('emails.agent.request-rejected');
    }
}
