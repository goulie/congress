<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    /**
     * Génère et enregistre le PDF d'une facture.
     *
     * @param Invoice $invoice
     * @return string Le chemin du fichier PDF généré
     */
    public function generate(Invoice $invoice)
    {
        // Charger les relations nécessaires
        $invoice->load('items', 'participant', 'user', 'congres');

        // Préparer les données pour la vue
        $data = [
            'invoice' => $invoice,
            'logo' => public_path('images/logo.png'), // chemin vers ton logo
            'date' => Carbon::now()->format('d/m/Y'),
        ];

        // Générer le contenu HTML via une vue Blade
        $html = View::make('invoices.pdf', $data)->render();

        // Utiliser Dompdf (barryvdh/laravel-dompdf)
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        // Nom du fichier
        $fileName = 'invoice_' . $invoice->invoice_number . '.pdf';
        $filePath = 'invoices/' . $fileName;

        // Sauvegarde dans le storage
        Storage::disk('public')->put($filePath, $pdf->output());

        // Retourne le chemin du fichier
        return 'storage/' . $filePath;
    }

    public function download(Invoice $invoice)
    {
        $invoice->load('items', 'participant', 'user', 'congres');

        $data = [
            'invoice' => $invoice,
            'logo' => public_path('images/logo.png'),
            'date' => Carbon::now()->format('d/m/Y'),
        ];

        $pdf = PDF::loadView('invoice.invoice', $data);

        $fileName = 'invoice_' . $invoice->invoice_number . '.pdf';

        return $pdf->download($fileName);
    }
}
