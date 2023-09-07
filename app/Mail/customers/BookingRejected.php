<?php

namespace App\Mail\customers;

use App\Models\CarUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRejected extends Mailable
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
            subject: 'Booking Rejected',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customers.booking-rejected',
            with: [
                'carName'=> $this->booking->car->name,
                'from' => date_format(date_create($this->booking->start_date),'d/m/Y'),
                'until' => date_format(date_create($this->booking->end_date),'d/m/Y'),
                'url' => route('dashboard'),
                'url2' => route('car.index')
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
