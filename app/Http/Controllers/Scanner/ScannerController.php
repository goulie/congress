<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Participant;
use App\Models\ScanneHistory;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ScannerController extends Controller
{
    public function index()
    {
        $sessions = Session::CurrentSession();

        return view('scanner.index', compact('sessions'));
    }

    public function GotoSession($session)
    {
        session([
            'congres_session' => $session
        ]);
        return redirect()->back();
    }

    public function RemoveSession(Request $request)
    {
        $request->session()->forget('congres_session');

        return redirect()->back();
    }
    public function scanner($participantid)
    {
        try {
            // Rechercher le participant
            $participant = Participant::with(['civility', 'badge_color', 'nationality'])
                ->where('uuid', $participantid)
                ->first();

            if (!$participant) {
                // Participant non trouvé
                return response()->json([
                    'success' => false,
                    'message' => 'Participant non trouvé',
                    'participant' => null
                ], 404);
            }

            // Vérifier si déjà scanné aujourd'hui
            $alreadyScanned = ScanneHistory::where('participant_id', $participant->id)
                ->whereDate('scanned_at', Carbon::today())
                ->exists();

            // Enregistrer le scan
            ScanneHistory::create([
                'participant_id' => $participant->id,
                'scanned_at' => Carbon::now(),
                'scanner_user_id' => auth()->id(),
                'is_duplicate' => $alreadyScanned
            ]);

            // Mettre à jour le statut du participant
            $participant->update([
                'last_scanned_at' => Carbon::now(),
                'scanned_count' => $participant->scanned_count + 1
            ]);

            // Données formatées
            $participantData = [
                'id' => $participant->id,
                'uuid' => $participant->uuid,
                'name' => $participant->badge_full_name ??
                    ($participant->civility->libelle . ' ' . $participant->fname . ' ' . $participant->lname),
                'organization' => $participant->organisation,
                'role' => $participant->role_badge_congres ?? $participant->job,
                'badge_color' => $participant->badge_color->color ?? '#667eea',
                'badge_type' => $participant->badge_color->libelle ?? 'Standard',
                'nationality' => $participant->nationality->libelle_fr ?? 'Non spécifiée',

                'scanned_at' => Carbon::now()->format('H:i:s'),
                'scanned_date' => Carbon::now()->format('d/m/Y')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Présence enregistrée avec succès',
                'participant' => $participantData
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du scan', [
                'participant_id' => $participantid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du scan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function scan(Request $request)
    {
        $locale = app()->getLocale();

        // Validation des paramètres requis
        $validator = Validator::make($request->all(), [
            'session_id'     => 'required',
            'participant_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $sessionId     = $request->get('session_id');
        $participantId = $request->get('participant_id');

        // 1. Vérification de la session
        $session = Session::find($sessionId);
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => $this->t(
                    'Session introuvable',
                    'Session not found'
                )
            ], 404);
        }

        // 2. Vérification du participant
        $participant = Participant::where('uuid', $participantId)->first();
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => $this->t(
                    'Participant introuvable',
                    'Participant not found'
                )
            ], 404);
        }

        // 3. Vérification des autorisations spécifiques à la session
        $authorizationCheck = $this->checkSessionAuthorization($session, $participant);
        if (!$authorizationCheck['authorized']) {
            return response()->json([
                'success' => false,
                'message' => $authorizationCheck['message']
            ], 403);
        }

        // 4. Vérification si déjà scanné aujourd'hui
        $today = Carbon::today();
        $alreadyScanned = ScanneHistory::where([
            'participant_id' => $participant->id,
            'session_id'     => $sessionId,
            'scanne_date'    => $today
        ])->exists();

        // 5. Enregistrement du scan si nouveau
        if (!$alreadyScanned) {
            $congresId = Congress::latest()->value('id');

            ScanneHistory::create([
                'congres_id'     => $congresId,
                'participant_id' => $participant->id,
                'session_id'     => $sessionId,
                'scanne_date'    => Carbon::now()
            ]);
        }

        // 6. Réponse
        return response()->json([
            'success' => true,
            'message' => $alreadyScanned
                ? $this->t('Participant déjà scanné', 'Participant already scanned')
                : $this->t('Présence enregistrée', 'Attendance recorded'),
            'participant' => $this->formatParticipantData($participant, $alreadyScanned)
        ]);
    }


    /**
     * Vérifie si le participant est autorisé pour la session
     */
    private function checkSessionAuthorization(Session $session, Participant $participant): array
    {
        $typeSession = $session->type_session;

        $authorizationRules = [
            'dinner_gala' => [
                'field' => 'diner',
                'denied_value' => 'non',
                'message' => $this->t(
                    'Participant non autorisé pour le dîner de gala',
                    'Participant not authorized for the gala dinner'
                )
            ],
            'visite_technique' => [
                'field' => 'visite',
                'denied_value' => 'non',
                'message' => $this->t(
                    'Participant non autorisé pour la visite technique',
                    'Participant not authorized for the technical visit'
                )
            ]
        ];

        if (array_key_exists($typeSession, $authorizationRules)) {
            $rule = $authorizationRules[$typeSession];

            if ($participant->{$rule['field']} === $rule['denied_value']) {
                return [
                    'authorized' => false,
                    'message'    => $rule['message']
                ];
            }
        }

        return ['authorized' => true, 'message' => ''];
    }


    /**
     * Formate les données du participant pour la réponse
     */
    private function formatParticipantData(Participant $participant, bool $alreadyScanned): array
    {
        return [
            'name' => $participant->badge_full_name
                ?? ($participant->civility->libelle . ' ' . $participant->fname . ' ' . $participant->lname),
            'organization' => $participant->organisation,
            'badge_color' => $participant->badge_color->color ?? '#667eea',
            'badge_type' => $participant->badge_type->libelle ?? '',
            'already_scanned' => $alreadyScanned,
            'participant' => $participant
        ];
    }

    private function t(string $fr, string $en): string
{
    return app()->getLocale() === 'fr' ? $fr : $en;
}

}
