<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\SendOtpMail;

class OtpMailService
{
    /**
     * Génère et envoie un OTP à un utilisateur par email
     */
    public function sendOtp(string $email, string $lang, string $otp)
    {

        // Envoyer le mail
        Mail::to($email)->send(new SendOtpMail($otp, $lang));

        return [
            'success' => true,
            'message' => 'OTP envoyé avec succès à ' . $email
        ];
    }

}
