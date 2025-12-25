<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeRange;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\Participant;
use App\Services\InvoicePdfService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $invoices = Invoice::AllInvoices(Congress::latest()->first()->id);
        
        if (Auth::user()->isAdmin()|| Auth::user()->isFinance() || Auth::user()->isSecretary()) {
            $invoices = $invoices->get();
        } else {
            $invoices = $invoices->where('participants.user_id', Auth::user()->id)->get();
            
        }


        return view('voyager::invoices.index', compact('invoices'));
    }
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
        $invoice = $participant->invoices()
            ->with('items', 'congres', 'user')
            ->latest()
            ->first();

        if (!$invoice) {
            abort(404, 'Facture non trouvée pour ce participant.');
        }

        // Trier les items par date (EN + FR)
        $invoice->items = $invoice->items
            ->sortBy(function ($item) {

                $text = $item->description_en
                    ?? $item->description_fr
                    ?? '';

                // FORMAT ISO (EN) : 2026-02-09
                if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $text, $m)) {
                    return Carbon::createFromDate(
                        (int) $m[1], // year
                        (int) $m[2], // month
                        (int) $m[3]  // day
                    );
                }

                // FORMAT FR : 09 février 2026
                if (preg_match('/(\d{2})\s+février\s+(\d{4})/iu', $text, $m)) {
                    return Carbon::createFromDate(
                        (int) $m[2], // year
                        2,           // month (février)
                        (int) $m[1]  // day
                    );
                }

                // Sans date → à la fin
                return Carbon::maxValue();
            })
            ->values(); // réindexation propre

        // Télécharger le PDF
        return $pdfService->download($invoice);
    }

    public function export(Request $request)
    {
        $request->validate([
            'organization' => 'required',
            'email' => 'required|email',
            'Adresse' => 'nullable'
        ]);

        $ids = $request->input('participant_ids', []);
        $organisation = $request->input('organization');
        $email = $request->input('email');
        $Adresse = $request->input('Adresse');
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
            'email' => $email,
            'organisation' => $organisation,
            'Adresse' => $Adresse
        ])->setPaper('A4', 'portrait');

        return $pdf->download('facture_groupee_' . $organisation . '.pdf');
    }

    /* public function index()
    {
        $participants = Participant::with(['invoices.items', 'participantCategory'])->get();
        dd($participants);
        // return view('invoice.index');
    }
 */
    public function details($id)
    {
        try {

            $participant = Participant::with(['congres', 'country', 'gender', 'participantCategory', 'ageRange'])
                ->findOrFail($id);

            $invoice = Invoice::where('participant_id', $id)
                ->with('items')
                ->first();

            return response()->json([
                'participant' => $participant,
                'invoice' => $invoice,
                'items' => $invoice ? $invoice->items : []
            ]);
        } catch (\Exception $e) {
            Log::error("message: " . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
