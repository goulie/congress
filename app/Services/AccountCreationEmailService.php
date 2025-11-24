<?php

namespace App\Services;

use App\Mail\AccountCreationMail;
use App\Mail\CustomVerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class AccountCreationEmailService
{
    
    public function sendAccountCreationAndVerification($user)
    {
        try {
            // 1. Email de création de compte
            Mail::to($user->email)->send(new AccountCreationMail($user));

            // 2. Email de vérification
            $verificationUrl = $this->generateVerificationUrl($user);
            Mail::to($user->email)->send(new CustomVerifyEmail($verificationUrl, $user));

            Log::info('Account creation and verification emails sent to: ' . $user->email);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send account emails: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send only account creation email
     */
    public function sendAccountCreationEmail($user)
    {
        try {
            $loginUrl = route('login');
            Mail::to($user->email)->send(new AccountCreationMail($user, $loginUrl));
            Log::info('Account creation email sent to: ' . $user->email);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send account creation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate verification URL
     */
    protected function generateVerificationUrl($user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );
    }

    /**
     * Send verification email only
     */
    public function sendVerificationEmail($user)
    {
        try {
            $verificationUrl = $this->generateVerificationUrl($user);
            Mail::to($user->email)->send(new CustomVerifyEmail($verificationUrl, $user));
            Log::info('Verification email sent to: ' . $user->email);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            return false;
        }
    }
}
