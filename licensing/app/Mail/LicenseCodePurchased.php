<?php

namespace App\Mail;

use App\Models\LicenseCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseCodePurchased extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public LicenseCode $licenseCode,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $productName = $this->licenseCode->licensableProduct->name ?? 'Product';

        return new Envelope(
            subject: "Your {$productName} License Code",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.license-code-purchased',
            with: [
                'code' => $this->licenseCode->code,
                'productName' => $this->licenseCode->licensableProduct->name ?? 'Product',
                'durationDays' => $this->licenseCode->duration_days,
                'pricePaid' => $this->licenseCode->price_paid,
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
