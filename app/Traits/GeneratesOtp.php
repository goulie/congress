<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait GeneratesOtp
{
    /**
     * Génère un code OTP et le stocke dans la base.
     *
     * @param string $identifier  Email, téléphone, etc.
     * @param int $digits         Nombre de chiffres du code OTP (par défaut : 4)
     * @param int $validity       Durée de validité en minutes (par défaut : 15)
     * @return array              Informations sur l'OTP généré
     */
    public function generateOtp(string $identifier, int $digits = 4, int $validity = 15): array
    {
        // Génération du code OTP aléatoire
        $otpCode = str_pad(random_int(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);

        // Date d’expiration
        $expiresAt = Carbon::now()->addMinutes($validity);

        // Table OTP (assure-toi d’avoir une table 'otps')
        DB::table('otps')->updateOrInsert(
            ['identifier' => $identifier],
            [
                'token' => $otpCode,
                'expires_at' => $expiresAt,
                'validity' => $validity,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return [
            'identifier' => $identifier,
            'code' => $otpCode,
            'expires_at' => $expiresAt,
        ];
    }

     /**
     * Valide un OTP : vérifie qu’il existe, qu’il est valide et non expiré.
     *
     * @param string $identifier
     * @param string $code
     * @return array
     */
    
}
