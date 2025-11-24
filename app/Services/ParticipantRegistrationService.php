<?php

namespace App\Services;

use App\Mail\ParticipantConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ParticipantRegistrationService
{
    /**
     * Envoie l'email de confirmation au participant
     */
    public function sendConfirmationEmail($participant, $eventDetails = [])
    {
        try {
            Mail::to($participant['email'])
                ->send(new ParticipantConfirmationMail($participant, $eventDetails));

            Log::info('Email de confirmation envoyé à : ' . $participant['email']);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Traite l'inscription complète d'un participant
     */
    public function registerParticipant($participantData, $eventDetails = [])
    {
        // Ici vous pouvez ajouter la logique pour sauvegarder le participant en base
        // $participant = Participant::create($participantData);

        // Envoi de l'email de confirmation
        $emailSent = $this->sendConfirmationEmail($participantData, $eventDetails);

        return [
            'success' => $emailSent,
            'message' => $emailSent ?
                'Inscription confirmée et email envoyé' :
                'Inscription enregistrée mais échec de l\'envoi de l\'email'
        ];
    }
}
