<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Services\InvoicePdfService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function store(Request $request, InvoiceService $invoiceService, InvoicePdfService $pdfService)
    {
        // Création / mise à jour de la facture
        $invoice = $invoiceService->createOrUpdateInvoice($request->all(), $request->items);

        // Génération automatique du PDF
        $pdfPath = $pdfService->generate($invoice);

        // Mise à jour du chemin du PDF dans la facture
        $invoice->update(['pdf_path' => $pdfPath]);

        return redirect()->back()->with('success', 'Facture générée avec succès !');
    }

    public function downloadByParticipant(Participant $participant, InvoicePdfService $pdfService)
    {
        // Charger la dernière facture avec ses items
        $invoice = $participant->invoices()->with('items', 'congres', 'user')->latest()->first();

        if (!$invoice) {
            abort(404, 'Facture non trouvée pour ce participant.');
        }

        // Télécharger le PDF directement
        return $pdfService->download($invoice);
    }

    public function export(Request $request)
    {
        $ids = $request->input('participant_ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Veuillez sélectionner au moins un participant.');
        }

        $participants = Participant::with(['invoices.items', 'participantCategory'])
            ->whereIn('id', $ids)
            ->get();

        // Calculer le total global
        $totalAmount = 0;
        $currency = 'EUR'; // par défaut
        foreach ($participants as $participant) {
            $invoice = $participant->invoices->first();
            if ($invoice) {
                $totalAmount += $invoice->total_amount;
                $currency = $invoice->currency ?? $currency;
            }
        }

        $pdf = Pdf::loadView('invoice.invoiceGroup', [
            'participants' => $participants,
            'totalAmount' => $totalAmount,
            'currency' => $currency,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('facture_groupee.pdf');
    }

    public function index()
    {

        $participants = Participant::with(['invoices.items', 'participantCategory'])->get();
        dd($participants);
       // return view('invoice.index');
    }
}
