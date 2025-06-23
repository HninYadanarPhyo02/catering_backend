<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
    public $details;

    public function __construct($invoice, $details)
    {
        $this->invoice = $invoice;
        $this->details = $details;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Monthly Report Mail',
        );
    }
    // public function build()
    // {
    //     return $this->subject('Monthly Catering Invoice')
    //         ->view('emails.monthly_invoice');
    // }
    public function build()
{
    return $this->view('emails.monthly-invoice')
                ->with([
                    'invoice' => $this->invoice,
                    'details' => $this->details, // optional if you want to show items
                ])
                ->subject('Your Monthly Invoice');
}

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly_invoice',
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
