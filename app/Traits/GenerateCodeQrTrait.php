<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait GenerateCodeQrTrait
{

    public function generateAndStoreQrCode($content)
    {
        // Générer le QR code sous forme binaire PNG
        $qr = QrCode::format('png')
            ->size(200)
            ->generate($content);

        // Nom unique du fichier
        $filename = $content . '.png';

        // Créer le dossier s'il n'existe pas
        if (!Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->makeDirectory('qrcodes');
        }

        // Stocker le fichier
        Storage::disk('public')->put('qrcodes/' . $filename, $qr);

        // Retourner l’URL publique
        return Storage::url('qrcodes/' . $filename);
    }
}
