<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeToGuessingGameMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $playerName)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Guessing Game'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'playerName' => $this->playerName,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
