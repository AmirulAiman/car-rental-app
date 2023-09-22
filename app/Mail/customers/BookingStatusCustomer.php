<?php

namespace App\Mail\Customers;

use App\Models\CarUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class BookingStatusCustomer extends Mailable
{
    use Queueable, SerializesModels;
    private CarUser $booking;
    private $status;
    /**
     * Create a new message instance.
     */
    public function __construct(CarUser $booking, $status=false)
    {
        $this->booking = $booking;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Status:'.$this->booking->car->name.'('.$this->booking->car->plate_number.')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customers.booking-status',
            with: [
                'car' => $this->booking->car->name,
                'from' => date_format(date_create($this->booking->start_date),'d/m/Y'),
                'until' => date_format(date_create($this->booking->end_date),'d/m/Y'),
                'status' => Str::ucfirst(Str::replace('_',' ',$this->booking->status)),
                'approved' => $this->status ?? false,
                'deposit' => $this->booking->car->deposit ?? null,
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
