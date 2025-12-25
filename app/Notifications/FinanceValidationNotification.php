<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FinanceValidationNotification extends Notification
{
    use Queueable;

    protected string $validatorName;
    protected string $invoiceNumber;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $validatorName, string $invoiceNumber)
    {
        $this->validatorName = $validatorName;
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Bonjour,')
            ->line(
                'La facture n° ' . $this->invoiceNumber .
                    ' a été vérifiée et validée par ' . $this->validatorName .
                    ' après confirmation du paiement.'
            )
            ->line('Elle est désormais transmise pour votre traitement.')
            ->action('Accéder à votre espace', url('/login'))
            ->line('Cordialement.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'invoice_number' => $this->invoiceNumber,
            'validated_by' => $this->validatorName,
        ];
    }
}
