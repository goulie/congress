<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectedStudentOrYwpregistrantNotification extends Notification
{
    public $participant,$reason;

    public function __construct($participant, $reason)
    {
        $this->participant = $participant;
        $this->reason = $reason;
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
            ->subject($isFr ? "Votre dossier a été rejeté" : "Your document has been rejected")
            ->line(
                $isFr
                    ? "Bonjour {$p->fname} {$p->lname},"
                    : "Hello {$p->fname} {$p->lname},"
            )
            ->greeting(
                $isFr
                    ? "Bonjour {$p->fname} {$p->lname},"
                    : "Hello {$p->fname} {$p->lname},"
            )
            ->line(
                $isFr
                    ? "malheureusement votre dossier a été refusé."
                    : "unfortunately your document has been rejected."
            )
            ->line(
                $isFr
                    ? "Veuillez soumettre un document conforme ou contacter notre équipe."
                    : "Please upload a valid document or contact our support team."
            )
            ->line($isFr ? "Raison de rejet :" .$this->reason : "Reason of rejection : ".$this->reason)
            ->action(
                $isFr ? "Accéder à mon espace" : "Access my dashboard",
                url('/login')
            )
            ->line(
                $isFr
                    ? "Nous restons à votre disposition."
                    : "We remain at your disposal."
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
