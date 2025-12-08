<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $contactSubject;
    public $messageContent;

    public function __construct(string $name, string $email, string $subject, string $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->contactSubject = $subject;
        $this->messageContent = $message;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesan Baru dari Form Kontak: ' . $this->contactSubject,
            replyTo: [
                new Address($this->email, $this->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'contactSubject' => $this->contactSubject,
                'messageContent' => $this->messageContent,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
