<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait ValidOtp
{
    /**
     * Valide un OTP : existe, non utilisé, non expiré.
     *
     * @param string $identifier Email ou téléphone
     * @param string $code OTP soumis
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateOtp(string $identifier, string $code): array
    {
        $record = DB::table('otps')
            ->where('identifier', $identifier)
            ->where('token', $code)
            ->first();

        // Aucun OTP trouvé
        if (!$record) {
            return [
                'valid' => false,
                'message' => __('Code OTP invalide.'),
            ];
        }

        // Déjà utilisé
        if (!empty($record->is_used) && $record->is_used) {
            return [
                'valid' => false,
                'message' => __('Ce code OTP a déjà été utilisé.'),
            ];
        }

        // Expiré
        if (Carbon::parse($record->expires_at)->isPast()) {
            return [
                'valid' => false,
                'message' => __('Ce code OTP est expiré.'),
            ];
        }

        // Tout est OK → marquer comme utilisé
        DB::table('otps')
            ->where('id', $record->id)
            ->update([
                'is_used' => true,
                'updated_at' => now(),
            ]);

        return [
            'valid' => true,
            'message' => __('OTP validé avec succès.'),
        ];
    }

    /**
     * Vérifie si l'utilisateur a déjà un OTP valide non utilisé
     *
     * @param string $identifier
     * @return bool
     */
    public function hasValidOtp(string $identifier): bool
    {
        $record = DB::table('otps')
            ->where('identifier', $identifier)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        return $record ? true : false;
    }
}
