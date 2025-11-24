<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentOrYwpregistrantNotification extends Notification
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


    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->participant;

        return (new MailMessage)
            ->subject("Nouveau dossier à valider - Étudiant / Jeune Professionnel")
            ->line("Un nouveau participant a soumis un dossier qui nécessite une validation.")
            ->line("Nom : {$p->fname} {$p->lname}")
            ->line("Email : {$p->email}")
            ->line("Type : " . ($p->ywp_or_student === 'ywp' ? 'Jeune Professionnel (YWP)' : 'Étudiant'))
            ->line("Catégorie : " . $p->participantCategory->translate('fr', 'fallbackLocale')->libelle)
            ->action('Traiter ce dossier', url('/admin/view-validation-ywp-students'))
            ->line("Merci d'effectuer la validation dès que possible.");
    }
}
