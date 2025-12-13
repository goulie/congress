<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait GenerateCodeQr
{
    /**
     * Génère un fichier QR code en PNG et le stocke dans storage/app/public/qrcodes/
     *
     * @param string $content   Contenu du QR code
     * @param int    $size      Taille du QR code
     * @return string           URL publique du fichier QR code
     */
    public function generateAndStoreQrCode(string $content, int $size = 300)
    {
        // Générer le QR code sous forme binaire PNG
        $qr = QrCode::format('png')
            ->size($size)
            ->generate($content);

        // Nom unique du fichier
        $filename = 'qrcode_' . time() . '.png';

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
