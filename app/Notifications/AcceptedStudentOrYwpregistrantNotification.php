<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcceptedStudentOrYwpregistrantNotification extends Notification
{
    use Queueable;

    public $participant;

    public function __construct($participant)
    {
        $this->participant = $participant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->participant;
        $isFr = $p->langue === 'fr';

        return (new MailMessage)
            ->subject($isFr ? "Votre dossier a été accepté" : "Your document has been approved")
            ->greeting(
                $isFr
                    ? "Bonjour {$p->fname}, "
                    : "Hello {$p->fname}, "
            )
            ->line(
                $isFr
                    ? "votre dossier soumis pour la participation aux **{$p->congres->translate('fr', 'fallbackLocale')->title}** congrès a été accepté."
                    : "your document submitted for participation to the **{$p->congres->translate('en', 'fallbackLocale')->title}** congress has been approved.")
            ->line(
                $isFr
                    ? "Vous pouvez désormais accéder à votre facture dans le menu **Factures**, procéder au paiement et finaliser votre participation au congrès."
                    : "You can now access your invoice in the **Invoices** menu, proceed with the payment, and finalize your participation in the congress."
            )
            ->action(
                $isFr ? "Accéder à mon espace" : "Access my dashboard",
                url('/admin/invoices')
            )
            ->line(
                $isFr
                    ? "Merci pour votre confiance."
                    : "Thank you for your trust."
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
