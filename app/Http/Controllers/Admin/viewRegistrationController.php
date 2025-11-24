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
        $congress = Congress::latest()->first();
        // Récupération des données avec filtres
        $query = Participant::where(['congres_id' => $congress->id])->with(['country', 'civility', 'participantCategory']);

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

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Récupérer les données paginées
        $dataTypeContent = $query->orderBy('created_at', 'desc')->paginate(20);

        // Données pour les filtres
        $categories = CategoryParticipant::all();
        $countries = Country::all();

        // Statistiques
        $stats = [
            'totalParticipants' => Participant::where('congres_id', $congress->id)
            ->where('diner', '<>', '')
            ->where('visite', '<>', '')
            ->count(),
            'paidParticipants' => Participant::where('congres_id', $congress->id)->where('status', 'paid')->count(),
            'countriesCount' => Participant::where('congres_id', $congress->id)->distinct('nationality_id')->count('nationality_id')
        ];

        $invoices = Invoice::where([
            'congres_id' => $congress->id,
            ['total_amount', '>', 0]
        ])->get();

        $view = 'voyager::view-registrations.browse';

        return Voyager::view($view, compact(
            'categories',
            'countries',
            'stats',
            'dataTypeContent',
            'invoices'
        ));
    }
}
