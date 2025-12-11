<?php

namespace App\Services;

use App\Mail\RegistrationConfirmationMail;
use App\Mail\AccountCreationMail;
use App\Mail\CustomVerifyEmail;
use App\Mail\AutomaticInvitationLetter;
use App\Mail\InvitationLetterMail;
use App\Mail\Invoice\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EmailService
{

    /**
     * =============================================
     * EMAILS D'INSCRIPTION PARTICIPANTS - NOUVELLE VERSION
     * =============================================
     */

    /**
     * Envoyer la confirmation d'inscription avec le nouveau template bilingue
     */
    public function sendRegistrationConfirmation($participant)
    {
        try {
            // Charger les relations nécessaires
            $participant->load([
                'congres',
                'participantCategory',
                'civility'
            ]);

            // Définir la locale pour l'email
            $locale = $participant->langue ?? app()->getLocale();
            app()->setLocale($locale);

            Mail::to($participant->email)
                ->locale($locale)
                ->send(new RegistrationConfirmationMail($participant, $participant->congres));

            Log::info("Confirmation d'inscription envoyée à: {$participant->email} en langue: {$locale}");

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur envoi confirmation inscription: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer des confirmations en masse avec le nouveau template
     */
    public function sendBulkRegistrationConfirmations($participants)
    {
        $successCount = 0;
        $failCount = 0;
        $failedEmails = [];

        foreach ($participants as $participant) {
            // Charger les relations pour chaque participant
            $participant->load(['congres', 'participantCategory', 'civility']);

            if ($this->sendRegistrationConfirmation($participant)) {
                $successCount++;
            } else {
                $failCount++;
                $failedEmails[] = $participant->email;
                Log::warning('Échec envoi confirmation à: ' . $participant->email);
            }
        }

        Log::info("Envoi en masse terminé: {$successCount} succès, {$failCount} échecs");

        return [
            'success' => $successCount,
            'failed' => $failCount,
            'failed_emails' => $failedEmails,
            'total' => count($participants)
        ];
    }

    /**
     * =============================================
     * AUTRES MÉTHODES EXISTANTES (conservées)
     * =============================================
     */

    /**
     * Envoyer l'email de vérification
     */
    public function sendVerificationEmail($user, $verificationUrl = null)
    {
        try {
            if (!$verificationUrl) {
                $verificationUrl = $this->generateVerificationUrl($user);
            }

            Mail::to($user->email)->send(new CustomVerifyEmail($verificationUrl, $user));
            Log::info('Email de vérification envoyé à: ' . $user->email);
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur envoi email vérification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer l'email de création de compte
     */
    public function sendAccountCreationEmail($user, $password = null)
    {
        try {
            $loginUrl = route('login');
            Mail::to($user->email)->send(new AccountCreationMail($user, $password, $loginUrl));

            Log::info('Email création de compte envoyé à: ' . $user->email);
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur envoi email création compte: ' . $e->getMessage());
            return false;
        }
    }

    // ... autres méthodes existantes ...

    /**
     * Générer l'URL de vérification
     */
    protected function generateVerificationUrl($user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );
    }

    public function sendInvitationEmail($participant)
    {
        try {

            Mail::to($participant->email)->send(new InvitationLetterMail($participant, $participant->langue ?? 'fr'));

            Log::info('Lettre d\'invitation envoyée à : ' . $participant->email);

            return true;
        } catch (\Exception $e) {

            Log::error('Erreur envoi email d\'invitation : ' . $e->getMessage());

            return false;
        }
    }

    public function SendInvoiceEmail($invoice)
    {
        try {
           
            Mail::to($invoice->participant->email)->send(new InvoiceMail($invoice));
            //Mail::to('gouli1212@gmail.com')->send(new InvoiceMail($invoice));

            Log::info('Email facture envoyé à: ' . $invoice->participant->email);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur envoi email facture: ' . $e->getMessage());
            return false;
        }
    }
}
