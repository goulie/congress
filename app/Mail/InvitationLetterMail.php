<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $letterBody;
    public $lang;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($letterBody, $lang = 'fr')
    {
        $this->letterBody = $letterBody;
        $this->lang = $lang;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = $this->lang === 'fr'
            ? 'Lettre d\'invitation'
            : 'Invitation Letter';

        return new Envelope(
            subject: $subject,
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
            markdown: 'emails.invitation-letter',
            with: [
                'data' => $this->letterBody,
                'lang' => $this->lang,
                'event' => $this->letterBody->congres->translate($this->lang, 'fallbackLocale')->title,
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
        $pdf = Pdf::loadView('letter.invitation', [
            'data' => $this->letterBody,
            'event' => $this->letterBody->congres->translate($this->lang, 'fallbackLocale')->title,
            'content' => $this->letterBody->congres->invitationLetter,
            'lang' => $this->lang
        ]);

        $filename = ($this->lang === 'fr' ? 'lettre_invitation_' : 'invitation_letter_')
            . $this->letterBody->email . '.pdf';

        return [
            // Attacher le PDF généré
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn() => $pdf->output(),
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
