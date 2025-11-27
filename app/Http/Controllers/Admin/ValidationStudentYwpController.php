<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Participant;
use App\Models\StudentYwpValidation;
use App\Notifications\AcceptedStudentOrYwpregistrantNotification;
use App\Notifications\RejectedStudentOrYwpregistrantNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class ValidationStudentYwpController extends VoyagerBaseController
{
    public function index(Request $request)
    {
        // Récupérer les congrès pour le filtre
        $congress = Congress::latest()->first();

        // Construction de la requête avec filtres
        $query = Participant::with(['validation_ywp_student', 'congres'])->where(['participant_category_id' => 4, 'congres_id' => $congress->id]);

        // Filtre par type
        if ($request->filled('type_filter')) {
            $query->where('ywp_or_student', $request->type_filter);
        }

        // Filtre par statut de validation
        if ($request->filled('status_filter')) {
            $query->whereHas('validation_ywp_student', function ($q) use ($request) {
                $q->where('status', $request->status_filter);
            });
        }

        // Filtre par congrès
        if ($request->filled('congres_filter')) {
            $query->where('congres_id', $request->congres_filter);
        }

        $participants = $query->orderBy('created_at', 'desc')->get();

        // Statistiques


        $stats = [
            'totalParticipants' => (clone $query)->count(),

            'validatedParticipants' => (clone $query)
                ->whereHas('validation_ywp_student', function ($q) {
                    $q->where('status', StudentYwpValidation::STATUS_APPROVED);
                })
                ->count(),

            'pendingValidations' => (clone $query)
                ->whereHas('validation_ywp_student', function ($q) {
                    $q->where('status', StudentYwpValidation::STATUS_PENDING);
                })
                ->count(),

            'rejectedValidations' => (clone $query)
                ->whereHas('validation_ywp_student', function ($q) {
                    $q->where('status', StudentYwpValidation::STATUS_REJECTED);
                })
                ->count(),
        ];


        return view('voyager::view-validation-ywp-students.browse', compact('participants', 'stats'));
    }

    public function approve($id)
    {
        $participant = Participant::findOrFail($id);
        $participant->update([
            'isYwpOrStudent' => true
        ]);
        // Créer ou mettre à jour la validation
        $validation = StudentYwpValidation::updateOrCreate(
            ['participant_id' => $id],
            [
                'status' => StudentYwpValidation::STATUS_APPROVED,
                'validator_id' => auth()->id(),
                'reason' => null
            ]
        );
        //Send noification to the participant
        $participant->notify(new AcceptedStudentOrYwpregistrantNotification($participant));

        return redirect()->back()->with([
            'message' => 'Inscription approuvée avec succès.',
            'alert-type' => 'success'
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
            $participant->notify(new RejectedStudentOrYwpregistrantNotification($participant, $request->reason));

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
        $participant = Participant::with('validation_ywp_student')->findOrFail($id);

        // Détermine le fichier fourni
        $documentUrl = null;
        $documentType = null;

        if ($participant->student_letter) {
            $documentUrl = asset('/public/storage/' . $participant->student_letter);
            $documentType = "Lettre d’attestation";
        } elseif ($participant->student_card) {
            $documentUrl = asset('/public/storage/' . $participant->student_card);
            $documentType = "Carte Étudiante";
        }

        return response()->json([
            'fname' => $participant->fname,
            'lname' => $participant->lname,
            'email' => $participant->email,
            'phone' => $participant->phone,
            'ywp_or_student' => $participant->ywp_or_student,
            'validation_status' => optional($participant->validation_ywp_student->last())->status ?? "pending",

            'document_type' => $documentType,
            'document_url' => $documentUrl,
        ]);
    }
}
