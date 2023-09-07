<?php

namespace App\Mail\customers;

use App\Models\CarUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingApproved extends Mailable
{
    use Queueable, SerializesModels;
private CarUser $booking;
    /**
     * Create a new message instance.
     */
    public function __construct(CarUser $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customers.booking-approved',
            with: [
                'carName'=> $this->booking->car->name,
                'from' => date_format(date_create($this->booking->start_date),'d/m/Y'),
                'until' => date_format(date_create($this->booking->end_date),'d/m/Y'),
                'deposit' => $this->booking->car->deposit,
                'url' => route('dashboard')
            ]
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
