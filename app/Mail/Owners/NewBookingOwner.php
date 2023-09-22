<?php

namespace App\Mail\Owners;

use App\Models\Car;
use App\Models\CarUser;
use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingOwner extends Mailable
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
            subject: 'New Booking Owner',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.owners.new-booking',
            with: [
                'by'=> $this->booking->user->name,
                'carName'=> $this->booking->car->name,
                'from' => date_format(date_create($this->booking->start_date),'d/m/Y'),
                'until' => date_format(date_create($this->booking->end_date),'d/m/Y'),
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
