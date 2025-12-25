<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\Participant;
use App\Services\EmailService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    public function pay(Request $request, PaymentService $paymentService)
    {
        try {

            $request->validate([
                'uuid' => 'required|string'
            ]);

            $participant = Participant::where('uuid', $request->uuid)->first();

            if (! $participant) {
                return $this->errorResponse(
                    $request,
                    'Participant introuvable',
                    404
                );
            }


            if (
                empty($participant->fname) ||
                empty($participant->lname) ||
                empty($participant->email) ||
                empty($participant->phone)
            ) {
                return $this->errorResponse(
                    $request,
                    'Informations du participant incomplètes',
                    422
                );
            }


            $invoice = $participant->invoices()->latest()->first();

            if (! $invoice) {
                return $this->errorResponse(
                    $request,
                    'Aucune facture trouvée pour ce participant',
                    404
                );
            }

            if (empty($invoice->total_amount) || empty($invoice->currency)) {
                return $this->errorResponse(
                    $request,
                    'Données de facture invalides',
                    422
                );
            }
            $response = $paymentService->createPayment([
                'amount' => $invoice->total_amount,
                'currency' => $invoice->currency,
                'description' => 'Paiement frais de participation au congrès',
                'notify_url' => route('payment.notify'),
                'return_url' => route('payment.return'),
                'customer_name' => $participant->lname,
                'customer_surname' => $participant->fname,
                'customer_email' => $participant->email,
                'customer_phone_number' => $participant->phone,
                'customer_city' => 'Abidjan',
                'customer_country' => 'CI',
                'transaction_id' => $request->uuid,
            ]);


            if (! $response['success']) {
                return $this->errorResponse(
                    $request,
                    'Erreur lors de l’initiation du paiement',
                    502,
                    $response['error'] ?? null
                );
            }


            $paymentUrl = $response['url']
                ?? $response['data']['payment_url']
                ?? null;

            if (! $paymentUrl) {
                return $this->errorResponse(
                    $request,
                    'URL de paiement introuvable',
                    500
                );
            }


            if (! $request->expectsJson()) {
                return redirect()->away($paymentUrl);
            }


            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'data' => $response['data']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return $this->errorResponse(
                $request,
                'Erreur de validation',
                422,
                $e->errors()
            );
        } catch (\Throwable $e) {

            Log::error('PAYMENT INIT ERROR', [
                'uuid' => $request->uuid ?? null,
                'exception' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                $request,
                'Erreur interne du serveur',
                500
            );
        }
    }

    private function errorResponse(Request $request, string $message, int $status, $errors = null)
    {

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], $status);
        }


        return redirect()
            ->back()
            ->withInput()
            ->withErrors(['payment' => $message]);
    }


    public function notify(Request $request)
    {
        Log::info($request->all());

        /* Log::channel('payment')->info('DBS NOTIFY RECEIVED', [
            'payload' => $request->all(),
            'ip' => $request->ip(),
        ]);


        $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|string',
        ]);


        $participant = Participant::where('uuid', $request->transaction_id)->first();
        $payment = Invoice::where([
            'parricipant_id' => $participant->id,
            'status' => Invoice::PAYMENT_STATUS_UNPAID
        ])->first();


        if (!$payment) {
            Log::warning('PAYMENT NOT FOUND', [
                'transaction_id' => $request->transaction_id,
            ]);

            return response()->json(['message' => 'Payment not found'], 404);
        }


        if ($payment->status !== Invoice::PAYMENT_STATUS_PAID) {
            $payment->update([
                'status' => $this->mapStatus($request->status),
                'raw_response' => json_encode($request->all()),
                'payment_date' => now(),
                'payment_method' => Invoice::PAYMENT_METHOD_ONLINE
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Notification processed',
        ]); */
    }


    private function mapStatus(string $status): string
    {
        return match (strtolower($status)) {
            'success', 'paid', 'completed' => Invoice::PAYMENT_STATUS_PAID,
            'failed', 'cancelled' => Invoice::PAYMENT_STATUS_UNPAID,
            default => Invoice::PAYMENT_STATUS_UNPAID,
        };
    }



    public function return(Request $request, PaymentService $paymentService)
    {
        $invoice = Invoice::where('token', $request->get('payment_token'))->first();

        if (!$invoice) {
            return redirect()
                ->route('payment.failed')
                ->with('error', 'Transaction introuvable');
        }

        $response = $paymentService->checkPayment([
            'transaction_id' => optional($invoice->participant)->uuid,
        ]);

        if (!$response['success']) {
            return redirect()
                ->route('payment.failed')
                ->with('error', 'Erreur lors de la vérification du paiement');
        }

        $paymentData = $response['data']['data'];

        Log::info('Payment check response', $paymentData);

        if ($paymentData['status'] === 'ACCEPTED') {

            //UPDATE AVANT REDIRECT
            $invoice->update([
                'status'         => Invoice::PAYMENT_STATUS_PAID,
                'raw_response'   => json_encode($response['data']),
                'amount_paid'    => $paymentData['total_amount'],
                'payment_date'   => $paymentData['payment_date'],
                'payment_method' => Invoice::PAYMENT_METHOD_ONLINE,
            ]);

            //Envoi de l'email d'invitation
            $this->emailService->sendInvitationEmail($invoice->participant);

            // Envoi de l'email de facturation
            $this->emailService->sendInvoiceEmail($invoice);

            return redirect()
                ->route('payment.success')
                ->with('success', 'Paiement effectué avec succès');
        }

        return redirect()
            ->route('payment.pending')
            ->with('info', 'Paiement en cours de validation');
    }
}
