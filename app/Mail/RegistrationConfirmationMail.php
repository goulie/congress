<?php

namespace App\Mail;

use App\Models\Congress;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $congress;

    /**
     * Create a new message instance.
     */
    public function __construct(Participant $participant, Congress $congress)
    {
        $this->participant = $participant;
        $this->congress = $congress;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = $this->getTranslatedSubject();

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
            markdown: 'emails.registration-confirmation',
            with: [
                'participant' => $this->participant,
                'congress' => $this->congress,
                'locale' => $this->participant->langue ?? 'fr',
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
        return [];
    }

    private function getTranslatedSubject(): string
    {
        $subjects = [
            'fr' => 'Confirmation d\'inscription - ' . ($this->congress->title ?? 'Congrès'),
            'en' => 'Registration Confirmation - ' . ($this->congress->translate($this->participant->langue ?? 'fr', 'fallbackLocale')->title ?? 'Congress'),
        ];

        return $subjects[$this->participant->langue ?? 'fr'] ?? $subjects['fr'];
    }
}
