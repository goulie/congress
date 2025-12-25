<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Congress;
use App\Models\StudentYwpValidation;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class ViewAdminRegistrationController extends VoyagerBaseController
{
    public function index(Request $request)
    {
        $latestCongress = Congress::latest('id')->first();
        $query = Participant::getLastCongressParticipants($latestCongress->id);

        $participantIds = (clone $query)->pluck('id');

        //Statistiques generales
        $statGeneral = [
            // 1. Total participants
            'TotalInscrits' => (clone $query)->count(),

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

            'TotalPass' => (clone $query)->where('pass_deleguate','oui')->count(),

            'TotalPaid' => Invoice::PaidInvoices($latestCongress->id)->count(),

            'TotalUnpaid' => Invoice::UnpaidInvoices($latestCongress->id)->count(),

            'TotalExpired' => Invoice::ExpiredInvoices($latestCongress->id)->count(),
        ];

        //
        $statEtudiants = [
            'AcceptedStudent' => StudentYwpValidation::getAcceptedStudents()->count(),
            'PendingStudent' => StudentYwpValidation::getPendingStudents()->count(),
            'RejectedStudent' => StudentYwpValidation::getRejectedStudents()->count(),
        ];


        $statYwp = [
            'AcceptedYwp' => StudentYwpValidation::getAcceptedYwp()->count(),
            'PendingYwp' => StudentYwpValidation::getPendingYwp()->count(),
            'RejectedYwp' => StudentYwpValidation::getRejectedYwp()->count(),
        ];

        return view('voyager::view-dashboard-registrations.index', compact('statGeneral', 'statEtudiants', 'statYwp', 'query'));
    }

    public function getDashboardData(Request $request)
    {
        $lastCongress = Congress::orderBy('id', 'desc')->first();

        if (!$lastCongress) {
            return response()->json([
                'stats' => $this->getEmptyStats(),
                'charts' => $this->getEmptyCharts(),
                'recent' => []
            ]);
        }

        return response()->json([
            'stats' => $this->getDashboardStats($lastCongress),
            'charts' => $this->getChartsData($lastCongress),
            'recent' => $this->getRecentRegistrations($lastCongress)
        ]);
    }

    public function getRecentRegistrations()
    {
        $lastCongress = Congress::orderBy('id', 'desc')->first();

        if (!$lastCongress) {
            return response()->json([]);
        }

        return response()->json($this->getRecentRegistrationsData($lastCongress));
    }

    private function getDashboardStats($congress)
    {
        $participants = Participant::where('congres_id', $congress->id);

        $total = $participants->count();
        $today = Participant::where('congres_id', $congress->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $students = Participant::where('congres_id', $congress->id)
            ->where(function ($query) {
                $query->where('ywp_or_student', 'student')
                    ->orWhere('isYwpOrStudent', 'student')
                    ->orWhereNotNull('student_level_id');
            })->count();

        $ywp = Participant::where('congres_id', $congress->id)
            ->where(function ($query) {
                $query->where('ywp_or_student', 'ywp')
                    ->orWhere('isYwpOrStudent', 'ywp')
                    ->orWhere('participant_category_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('category_participants')
                            ->where('libelle', 'like', '%YWP%')
                            ->orWhere('libelle', 'like', '%Young Water Professional%');
                    });
            })->count();

        $validated = Participant::where('congres_id', $congress->id)
            ->where('status', 'validated')
            ->count();

        $pending = Participant::where('congres_id', $congress->id)
            ->where('status', 'Unpaid')
            ->count();

        // Nombre de pays représentés
        $countries = Participant::where('congres_id', $congress->id)
            ->distinct('nationality_id')
            ->count('nationality_id');

        // Statistiques de paiement
        $payments = Invoice::whereHas('participant', function ($query) use ($congress) {
            $query->where('congres_id', $congress->id);
        })->get();

        $payments_count = $payments->count();
        $payments_total = $payments->sum('amount');

        // Dîners
        $diners = Participant::where('congres_id', $congress->id)
            ->where('diner', 1)
            ->count();

        // Visites
        $visits = Participant::where('congres_id', $congress->id)
            ->where('visite', 1)
            ->count();

        // Documents complets (supposition: tous les documents requis sont remplis)
        $complete_docs = Participant::where('congres_id', $congress->id)
            ->whereNotNull('passeport_pdf')
            ->whereNotNull('student_card')
            ->count();

        $documents_complete = $total > 0 ? round(($complete_docs / $total) * 100) : 0;
        $documents_missing = $total - $complete_docs;

        // Membres AAE
        $aae_members = Participant::where('congres_id', $congress->id)
            ->where('membre_aae', 'oui')
            ->count();

        return [
            'total' => $total,
            'today' => $today,
            'students' => $students,
            'ywp' => $ywp,
            'validated' => $validated,
            'pending' => $pending,
            'countries' => $countries,
            'payments_count' => $payments_count,
            'payments_total' => $payments_total,
            'diners' => $diners,
            'visits' => $visits,
            'documents_complete' => $documents_complete,
            'documents_missing' => $documents_missing,
            'aae_members' => $aae_members
        ];
    }

    private function getChartsData($congress)
    {
        // Répartition par catégorie de participant
        $categories = DB::table('participants as p')
            ->leftJoin('category_participants as cp', 'p.participant_category_id', '=', 'cp.id')
            ->select('cp.libelle as category', DB::raw('COUNT(*) as count'))
            ->where('p.congres_id', $congress->id)
            ->groupBy('cp.libelle')
            ->orderBy('count', 'desc')
            ->get();

        $categoryLabels = $categories->pluck('category')->toArray();
        $categoryData = $categories->pluck('count')->toArray();

        // Répartition par genre
        $genders = DB::table('participants as p')
            ->leftJoin('genders as g', 'p.gender_id', '=', 'g.id')
            ->select('g.libelle as gender', DB::raw('COUNT(*) as count'))
            ->where('p.congres_id', $congress->id)
            ->groupBy('g.libelle')
            ->get();

        $genderLabels = $genders->pluck('gender')->toArray();
        $genderData = $genders->pluck('count')->toArray();

        return [
            'categories' => [
                'labels' => $categoryLabels,
                'data' => $categoryData
            ],
            'genders' => [
                'labels' => $genderLabels,
                'data' => $genderData
            ]
        ];
    }

    private function getRecentRegistrationsData($congress, $limit = 10)
    {
        return Participant::where('congres_id', $congress->id)
            ->with([
                'country:id,libelle_fr',
                'participantCategory:id,libelle'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'fname', 'lname', 'email', 'status', 'nationality_id', 'participant_category_id', 'created_at'])
            ->map(function ($participant) {
                return [
                    'id' => $participant->id,
                    'fname' => $participant->fname,
                    'lname' => $participant->lname,
                    'email' => $participant->email,
                    'status' => $participant->status,
                    'country_name' => $participant->country->libelle_fr ?? 'N/A',
                    'participant_category' => $participant->participantCategory->libelle ?? 'N/A',
                    'created_at' => $participant->created_at
                ];
            });
    }

    private function getEmptyStats()
    {
        return [
            'total' => 0,
            'today' => 0,
            'students' => 0,
            'ywp' => 0,
            'validated' => 0,
            'pending' => 0,
            'countries' => 0,
            'payments_count' => 0,
            'payments_total' => 0,
            'diners' => 0,
            'visits' => 0,
            'documents_complete' => 0,
            'documents_missing' => 0,
            'aae_members' => 0
        ];
    }

    private function getEmptyCharts()
    {
        return [
            'categories' => [
                'labels' => [],
                'data' => []
            ],
            'genders' => [
                'labels' => [],
                'data' => []
            ]
        ];
    }

    public function exportData(Request $request)
    {
        $lastCongress = Congress::orderBy('id', 'desc')->first();

        if (!$lastCongress) {
            return redirect()->back()->with('error', 'Aucun congrès trouvé');
        }

        $stats = $this->getDashboardStats($lastCongress);
        $charts = $this->getChartsData($lastCongress);

        // Créer un fichier CSV ou Excel ici
        // Pour simplifier, retournons un JSON
        return response()->json([
            'stats' => $stats,
            'charts' => $charts
        ]);
    }

    public function getParticipantDetails($id)
    {
        $participant = Participant::with([
            'civility',
            'country',
            'gender',
            'studentLevel',
            'participantCategory',
            'typeMember',
            'organisationType',
            'badge_color',
            'invoices',
            'siteVisite',
            'jobCountry'
        ])->findOrFail($id);

        return view('voyager::partials.participant-details', compact('participant'));
    }
}
