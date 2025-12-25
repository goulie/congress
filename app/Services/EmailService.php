<?php

namespace App\Services;

use App\Mail\RegistrationConfirmationMail;
use App\Mail\AccountCreationMail;
use App\Mail\CustomVerifyEmail;
use App\Mail\AutomaticInvitationLetter;
use App\Mail\InvitationLetterMail;
use App\Mail\Invoice\InvoiceMail;
use App\Mail\PaymentReminderMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

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
        $adminEmails = Voyager::setting('admin.admin_email')
            ? array_map('trim', explode(',', Voyager::setting('admin.admin_email')))
            : [];
        try {
            $loginUrl = route('login');
            Mail::to($user->email)->bcc($adminEmails)->send(new AccountCreationMail($user, $password, $loginUrl));

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
        $adminEmails = Voyager::setting('admin.admin_email')
            ? array_map('trim', explode(',', Voyager::setting('admin.admin_email')))
            : [];
        try {

            Mail::to($participant->email)->bcc($adminEmails)->send(new InvitationLetterMail($participant, $participant->langue ?? 'fr'));

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
            $adminEmails = Voyager::setting('admin.admin_finance')
                ? array_map('trim', explode(',', Voyager::setting('admin.admin_finance')))
                : [];
            Mail::to($invoice->participant->email)->bcc($adminEmails)->send(new InvoiceMail($invoice));
            //Mail::to('gouli1212@gmail.com')->send(new InvoiceMail($invoice));

            Log::info('Email facture envoyé à: ' . $invoice->participant->email);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur envoi email facture: ' . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentReminder(Invoice $invoice)
    {
        $locale = $invoice->participant->langue ?? 'fr';

        app()->setLocale($locale);

        Mail::to($invoice->participant->email)
            ->send(new PaymentReminderMail($invoice));
    }
}
