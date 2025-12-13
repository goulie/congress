<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $participant;
    public $locale;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->participant = $invoice->participant;

        // langue: 'fr' ou 'en'
        $this->locale = $this->participant->langue ?? app()->getLocale();

        // Configure la langue Laravel pour la vue
        app()->setLocale($this->locale);
    }

    public function envelope()
    {
        return new Envelope(
            subject: $this->getSubject(),
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'emails.invoice_updated_mail',
            with: [
                'invoice'     => $this->invoice,
                'participant' => $this->participant,
                'locale'      => $this->locale,
            ]
        );
    }

    public function attachments()
    {
        // PDF avec langue adaptée
        $pdf = Pdf::loadView('invoice.invoice', [
            'invoice'     => $this->invoice,
            'participant' => $this->participant,
            'locale'      => $this->locale,
        ]);

        $filename = 'invoice_' . $this->participant->email . '_' . $this->invoice->invoice_number . '.pdf';

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn() => $pdf->output(),
                $filename
            )->withMime('application/pdf'),
        ];
    }

    /**
     * Sujet selon la langue
     */
    private function getSubject()
    {
        return $this->locale === 'fr'
            ? 'Facture mise à jour - Nouvelle facture disponible'
            : 'Invoice Updated - Your new invoice is available';
    }
}
