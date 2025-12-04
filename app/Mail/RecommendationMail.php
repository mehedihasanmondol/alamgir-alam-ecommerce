<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class RecommendationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;
    public $recommendedProducts;
    public $basedOn;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $subject, $recommendedProducts = [], $basedOn = 'your browsing history')
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->recommendedProducts = $recommendedProducts;
        $this->basedOn = $basedOn;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recommendation',
            with: [
                'user' => $this->user,
                'recommendedProducts' => $this->recommendedProducts,
                'basedOn' => $this->basedOn,
                'unsubscribeUrl' => route('customer.profile.edit') . '#email-preferences',
            ],
        );
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
