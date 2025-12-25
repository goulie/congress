<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\Participant;
use App\Models\StudentYwpValidation;
use App\Models\User;
use App\Notifications\FinanceValidationNotification;
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
        // Congrès actif (le plus récent)
        $congress = Congress::latest()->first();

        if (!$congress) {
            return abort(404, 'Aucun congrès trouvé.');
        }

        // Query principale
        $query = Invoice::where('participants.congres_id', $congress->id)
            ->join('participants', 'participants.id', '=', 'invoices.participant_id')
            ->whereNotNull('email');

        /* ---- Filtres (ajoute si nécessaire) ---- */
        if ($request->filled('invoices.status')) {
            $query->where('invoices.status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('date')) {
            $query->whereDate('invoice_date', $request->date);
        }

        // Liste pour le tableau
        $invoices = Invoice::AllInvoices($congress->id)->get();

        /* ---- Statistiques correctes ---- */

        $stats = [
            'totalInvoices' => $query->count(),
            'amountTotal'   => $query->sum('total_amount'),

            'totalPaid'     => Invoice::PaidInvoices($congress->id)->count(),
            'amountPaid'    => Invoice::PaidInvoices($congress->id)->sum('amount_paid'),

            'totalUnpaid'   => Invoice::UnpaidInvoices($congress->id)->count(),
            'amountUnpaid'  => Invoice::UnpaidInvoices($congress->id)->sum('total_amount'),
        ];


        return view('voyager::view-validation-payments.browse', compact('congress', 'stats', 'invoices'));
    }

    public function approve_payment(Request $request, $id)
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json(['message' => 'Facture introuvable'], 404);
            }


            $invoice->update([
                'status' => Invoice::PAYMENT_STATUS_PAID,
                'user_id_validation' => auth()->id()
            ]);

            // Envoi de l'email de facturation
            $this->emailService->sendInvoiceEmail($invoice);

            //envoie de la lettre d'invitation
            $this->emailService->sendInvitationEmail($invoice->participant);
        } catch (\Exception $e) {
            Log::error('erreur lors de la validation du paiement :' . $e->getMessage());
            return response()->json(['message' => 'Une erreur s\'est produite lors de la validation du paiement.'], 500);
        }

        return response()->json([
            'message' => 'Le paiement a été validé avec succès.'
        ]);
    }

    public function approve_for_pending(Request $request, $id)
    {
        try {
            Log::info($request->payment_method);
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json(['message' => 'Facture introuvable'], 404);
            }

            $invoice->status = Invoice::PAYMENT_STATUS_PENDING;
            $invoice->amount_paid = $invoice->total_amount;
            $invoice->payment_date = $request->payment_date;
            $invoice->payment_method = $request->payment_method;
            $invoice->user_id_pending = auth()->id();
            $invoice->save();

            $user = User::where('role_id', 7)->first();

            $user->notify(
                new FinanceValidationNotification(
                    auth()->user()->name,
                    $invoice->invoice_number
                )
            );
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
            'raison' => $invoice->participant->validation_ywp_students->first()->reason ?? '',
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

    public function approve_group_pending(Request $request)
    {
        try {
            $request->validate([
                "invoices" => "required|array",
                "method" => "required|string",
                'payment_date'    => 'required|date'
            ]);

            foreach ($request->invoices as $id) {
                $invoice = Invoice::find($id);

                if ($invoice && $invoice->status == Invoice::PAYMENT_STATUS_UNPAID) {

                    $invoice->update([
                        'status' => Invoice::PAYMENT_STATUS_PENDING,
                        'amount_paid' => $invoice->total_amount,
                        'payment_method' => $request->method,
                        'payment_date' => $request->payment_date,
                        'user_id_validation' => auth()->id()
                    ]);
                    $user = User::where('role_id', 7)->first();
                    $user->notify(
                        new FinanceValidationNotification(
                            auth()->user()->name,
                            $invoice->invoice_number
                        )
                    );

                    // Envoi de l'email de validation
                    //$this->emailService->sendInvoiceEmail($invoice);
                }
            }

            return response()->json(["message" => "Paiement groupé validé avec succès"]);
        } catch (\Exception $e) {

            Log::error('erreur lors de la validation du paiement groupé :' . $e->getMessage());
            return response()->json(['message' => 'Une erreur s\'est produite lors de la validation du paiement.'], 500);
        }
    }

    public function approve_group(Request $request)
    {
        try {
            $validated = $request->validate([
                "invoices" => "required|string"
            ]);

            $invoiceIds = is_string($validated['invoices'])
                ? explode(',', $validated['invoices'])
                : $validated['invoices'];

            // Filtrer les IDs valides
            $invoiceIds = array_filter($invoiceIds, 'is_numeric');

            foreach ($invoiceIds as $id) {
                $invoice = Invoice::find($id);

                if ($invoice && $invoice->status == Invoice::PAYMENT_STATUS_PENDING) {

                    $invoice->update([
                        'status' => Invoice::PAYMENT_STATUS_PAID,
                        'user_id_validation' => auth()->id()
                    ]);

                    // Envoi de l'email de validation
                    $this->emailService->sendInvoiceEmail($invoice);
                }
            }

            return response()->json(["message" => "Paiement groupé validé avec succès"]);
        } catch (\Exception $e) {

            Log::error('erreur lors de la validation du paiement groupé :' . $e->getMessage());
            return response()->json(['message' => 'Une erreur s\'est produite lors de la validation du paiement.'], 500);
        }
    }

    public function reject_group(Request $request)
    {


        try {
            $validated = $request->validate([
                "invoices" => "required|string"
            ]);

            $invoiceIds = is_string($validated['invoices'])
                ? explode(',', $validated['invoices'])
                : $validated['invoices'];

            // Filtrer les IDs valides
            $invoiceIds = array_filter($invoiceIds, 'is_numeric');

            foreach ($invoiceIds as $id) {
                $invoice = Invoice::find($id);

                if ($invoice && $invoice->status == Invoice::PAYMENT_STATUS_PENDING) {

                    $invoice->update([
                        'status' => Invoice::PAYMENT_STATUS_UNPAID,
                        'amount_paid' => NULL,
                        'payment_method' => NULL,
                        'payment_date' => NULL,
                        'user_id_validation' => NULL
                    ]);

                    // Envoi de l'email de validation
                    //$this->emailService->sendInvoiceEmail($invoice);
                }
            }

            return response()->json(["message" => "Paiement groupé rejeté avec succès"]);
        } catch (\Exception $e) {

            Log::error('erreur lors de la validation du paiement groupé :' . $e->getMessage());
            return response()->json(['message' => 'Une erreur s\'est produite lors de la validation du paiement.'], 500);
        }
    }

    public function rejectInvoice(Request $request, $id)
    {
        try {

            $invoice = Invoice::find($id);

            if ($invoice && $invoice->status == Invoice::PAYMENT_STATUS_PENDING) {

                $invoice->update([
                    'status' => Invoice::PAYMENT_STATUS_UNPAID,
                    'amount_paid' => NULL,
                    'payment_method' => NULL,
                    'payment_date' => NULL,
                    'user_id_validation' => NULL
                ]);

                // Envoi de l'email de validation
                //$this->emailService->sendInvoiceEmail($invoice);
            }


            return response()->json(["message" => "Paiement rejeté avec succès"]);
        } catch (\Exception $e) {

            Log::error('erreur lors de la validation du paiement groupé :' . $e->getMessage());
            return response()->json(['message' => 'Une erreur s\'est produite lors de la validation du paiement.'], 500);
        }
    }
}
