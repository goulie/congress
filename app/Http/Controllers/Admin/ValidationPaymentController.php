<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\Participant;
use App\Models\StudentYwpValidation;
use App\Notifications\RejectedStudentOrYwpregistrantNotification;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class ValidationPaymentController extends VoyagerBaseController
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    public function index(Request $request)
    {
        // Récupérer les congrès pour le filtre
        $congress = Congress::orderBy('id', 'desc')->latest()->first();

        // Construction de la requête avec filtres
        $query = Invoice::where('congres_id', $congress->id);
        /* 
        'invoice_number',
        'invoice_date',
        'status',
        'user_id',
        'total_amount',
        'amount_paid',
        'participant_id',
        'payment_method',
        'congres_id',
        'payment_date',
        'created_at',
        'updated_at',
        'currency',
         */
        // Filtre par type
        /*         if ($request->filled('type_filter')) {
            $query->where('ywp_or_student', $request->type_filter);
        }

        // Filtre par statut de validation
        if ($request->filled('status_filter')) {
            $query->whereHas('validation_ywp_student', function ($q) use ($request) {
                $q->where('status', $request->status_filter);
            });
        }       
 */

        $participants = $query->orderBy('created_at', 'desc')->get();
        // Statistiques
        $stats = [
            'totalInvoices' => $query->count(),
            'amountTotal' => $query->sum('total_amount'),

            'totalPaid' => $query->count(),
            'amountPaid' => $query->sum('amount_paid'),

            'totalUnpaid' => $query->count(),
            'amountUnpaid' => $query->sum('amount_paid'),
        ];


        return view('voyager::view-validation-payments.browse', compact('stats', 'participants'));
    }

    public function approve_payment(Request $request, $id)
    {
        try {
            Log::info($request->payment_method);
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json(['message' => 'Facture introuvable'], 404);
            }


            $invoice->status = Invoice::PAYMENT_STATUS_PAID;
            $invoice->amount_paid = $invoice->total_amount;
            $invoice->payment_date = now();
            $invoice->payment_method = $request->payment_method;
            $invoice->user_id_validation = auth()->id();
            $invoice->save();

            

            // Envoi de l'email de validation
            $this->emailService->sendInvoiceEmail($invoice);
        } catch (\Exception $e) {
            Log::error('erreur lors de la validation du paiement :' . $e->getMessage());
            return response()->json(['message' => 'Une erreur s\'est produite lors de la validation du paiement.'], 500);
        }

        return response()->json([
            'message' => 'Le paiement a été validé avec succès.'
        ]);

        
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $participant = Participant::findOrFail($id);

            $validation = StudentYwpValidation::updateOrCreate(
                ['participant_id' => $id],
                [
                    'status' => StudentYwpValidation::STATUS_REJECTED,
                    'reason' => $request->reason,
                    'validator_id' => auth()->id(),
                ]

            );

            //Send notification to the participant

            return redirect()->back()->with([
                'message' => 'Inscription rejetée avec succès.',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {

            dd($e->getMessage());
            Log::error($e->getMessage());
        }
    }

    public function details($id)
    {
        $invoice = Invoice::where('participant_id', $id)->firstOrFail();

        return response()->json([
            'id_invoice' => $invoice->invoice_number,
            'invoice_date' => $invoice->invoice_date,
            'status' => $invoice->status,
            'total_amount' => $invoice->total_amount,
            'currency' => $invoice->currency,
            'amount_paid' => $invoice->amount_paid ?? 0,
            'payment_date' => $invoice->payment_date,
            'payment_method' => $invoice->payment_method,
            'validator' => $invoice->userValidation->name ?? 'Aucun',

            // Participant
            'fname' => $invoice->participant->fname,
            'lname' => $invoice->participant->lname,
            'email' => $invoice->participant->email,
            'phone' => $invoice->participant->phone,
            'nationality' => $invoice->participant->nationality->libelle_fr ?? '',
            'category' => $invoice->participant->participantCategory->libelle ?? '',
            'organisation' => $invoice->participant->organisation,
        ]);
    }
}
