<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class PromotionalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;
    public $promotionTitle;
    public $promotionDescription;
    public $discountCode;
    public $discountPercentage;
    public $expiryDate;
    public $products;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $subject, $promotionTitle, $promotionDescription, $discountCode = null, $discountPercentage = null, $expiryDate = null, $products = [])
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->promotionTitle = $promotionTitle;
        $this->promotionDescription = $promotionDescription;
        $this->discountCode = $discountCode;
        $this->discountPercentage = $discountPercentage;
        $this->expiryDate = $expiryDate;
        $this->products = $products;
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
            view: 'emails.promotional',
            with: [
                'user' => $this->user,
                'promotionTitle' => $this->promotionTitle,
                'promotionDescription' => $this->promotionDescription,
                'discountCode' => $this->discountCode,
                'discountPercentage' => $this->discountPercentage,
                'expiryDate' => $this->expiryDate,
                'products' => $this->products,
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
