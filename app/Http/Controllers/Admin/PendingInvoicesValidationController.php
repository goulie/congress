<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingInvoicesValidationController extends Controller
{
    public function index(Request $request)
    {
        $congress = Congress::latest()->first();

        if (!$congress) {
            return abort(404, 'Aucun congrès trouvé.');
        }

        // Query principale
        $query = Invoice::where('participants.congres_id', $congress->id)->where('invoices.status', Invoice::PAYMENT_STATUS_PENDING)
            ->join('participants', 'participants.id', '=', 'invoices.participant_id')
            ->whereNotNull('email');


        // Liste pour le tableau
        $invoices = Invoice::AllInvoices($congress->id)->where('invoices.status', Invoice::PAYMENT_STATUS_PENDING)->get();

        /* ---- Statistiques correctes ---- */

        $stats = [
            'totalInvoices' => $query->count(),
            'amountTotal'   => $query->sum('total_amount'),

            'totalPaid'     => Invoice::PaidInvoices($congress->id)->count(),
            'amountPaid'    => Invoice::PaidInvoices($congress->id)->sum('amount_paid'),

            'totalUnpaid'   => Invoice::UnpaidInvoices($congress->id)->count(),
            'amountUnpaid'  => Invoice::UnpaidInvoices($congress->id)->sum('total_amount'),
        ];


        return view('voyager::view-pending-invoices.index', compact('congress', 'stats', 'invoices'));
    }
}
