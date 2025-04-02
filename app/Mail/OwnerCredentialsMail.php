<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OwnerCredentialsMail extends Mailable
{
    use Queueable,
        SerializesModels;

    public string $email;

    public string $password;

    public string $text;

    /**
     * The array of subjects for different recipients.
     *
     * @var array<string, \Illuminate\Mail\Mailables\Envelope>
     */
    protected $subjects = [];

    protected $contents = [];

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, string $password, $text = '')
    {
        $this->email = $email;
        $this->password = $password;
        $this->text = $text;

        $this->subjects['owner'] = new Envelope(subject: 'Ви зареєструвались у системі');
        $this->subjects['anotherUser'] = new Envelope(subject: 'Реєстрація нового користувача');

        $this->contents['owner'] = new Content(view: 'emails.legalEntity.owner-credentials');
        $this->contents['anotherUser'] = new Content(view: 'emails.legalEntity.creator-notification');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return $this->text ? $this->subjects['anotherUser'] : $this->subjects['owner'];
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return $this->text ? $this->contents['anotherUser'] : $this->contents['owner'];
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
