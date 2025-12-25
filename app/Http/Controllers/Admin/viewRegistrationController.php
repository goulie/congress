<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryParticipant;
use App\Models\Congress;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Voyager;

class viewRegistrationController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function index(Request $request)
    {
        // Récupération des données avec filtres
        $latestCongress = Congress::latest('id')->first();
        $query = Participant::getLastCongressParticipants($latestCongress->id);

        $participantIds = (clone $query)->pluck('id');

        //Statistiques generales
        $stats = [
            // 1. Total participants
            'totalParticipants' => (clone $query)->count(),

            // 2. Total Delegues
            'TotalDelegues' => (clone $query)
                ->where('participant_category_id', 1)
                ->count(),

            // 2. Total Étudiants
            'TotalEtudiant' => (clone $query)
                ->where('ywp_or_student', 'student')
                ->count(),

            // 3. Total YWP
            'TotalYwp' => (clone $query)
                ->where('ywp_or_student', 'ywp')
                ->count(),

            'TotalNationalites' => Participant::countNationalities(),

            'TotalOrganisations' => (clone $query)->whereNotNull('organisation')->pluck('organisation')->unique()->count(),

            //totaDiner,TotalVisite,TotalPass,TotalPaid,TotalUnpaid,TotalExpired
            'TotalDiner' => (clone $query)->where('diner', 'oui')->count(),

            'TotalVisite' => (clone $query)->where('visite', 'oui')->count(),

            'TotalPass' => (clone $query)->where('pass_deleguate', 'oui')->count(),

            'paidParticipants' => Invoice::PaidInvoices($latestCongress->id)->count(),

            'TotalUnpaid' => Invoice::UnpaidInvoices($latestCongress->id)->count(),

            'TotalExpired' => Invoice::whereIn('participant_id', $participantIds)->where('invoices.status', Invoice::PAYMENT_STATUS_EXPIRED)->count(),
        ];

        // Application des filtres
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('fname', 'like', '%' . $request->search . '%')
                    ->orWhere('lname', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('organisation', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('participant_category_id', $request->category);
        }

        if ($request->has('country') && $request->country) {
            $query->where('nationality_id', $request->country);
        }

        if ($request->has('invoices.status') && $request->status) {
            $query->where('invoices.status', $request->status);
        }

        // Récupérer les données paginées
        $dataTypeContent = $query;

        // Données pour les filtres
        $categories = CategoryParticipant::all();
        $countries = Country::all();

        $invoices = Invoice::AllInvoices($latestCongress->id)->get();

        $view = 'voyager::view-registrations.browse';

        return Voyager::view($view, compact(
            'categories',
            'countries',
            'stats',
            'dataTypeContent','invoices'
        ));
    }
}
