<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
    public $locale;
    public $pdfPath;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoice, $pdfPath = null)
    {
        $this->invoice = $invoice;
        $this->locale = $invoice->participant->langue ?? app()->getLocale();
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->getSubject(),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.payment_reminder',
            with: [
                'invoice' => $this->invoice,
                'participant' => $this->invoice->participant,
                'locale' => $this->locale
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        // Générer le PDF
        $pdf = Pdf::loadView('invoice.invoice', [
            'invoice' => $this->invoice,
            'locale' => $this->locale
        ]);

        $filename = 'invoice_' . $this->invoice->participant->email . '_' . $this->invoice->invoice_number . '.pdf';

        return [
            // Attacher le PDF généré
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn() => $pdf->output(),
                $filename
            )->withMime('application/pdf'),
        ];
    }

    private function getSubject()
    {
        return $this->locale == 'fr'
            ? '🔔 Rappel de Paiement ' . $this->invoice->congres->translate($this->locale, 'fallbackLocale')->title
            : '🔔 Payment Reminder - ' . $this->invoice->congres->translate($this->locale, 'fallbackLocale')->title;
    }
}
