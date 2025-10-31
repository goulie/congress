<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Participant;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ParticipantRegistrant extends Controller
{
    public function index()
    {
        return view('registration.participant-registrant');
    }

    public function step1(Request $request)
    {
        $meeting = Congress::latest('id')->first();
        $userId = auth()->user()->id;

        // Vérifier si le participant existe déjà
        $participant = Participant::where('user_id', $userId)
            ->where('congres_id', $meeting->id)
            ->first();

        if ($participant) {
            // UPDATE - Mettre à jour l'enregistrement existant
            $participant->update([
                'civility_id' => $request->title,
                'fname' => $request->first_name,
                'lname' => $request->last_name,
                'student_level_id' => $request->education,
                'gender_id' => $request->gender,
                'nationality_id' => $request->country,
                'registration_id' => auth()->user()->user_id,
            ]);
        } else {
            // INSERT - Créer un nouvel enregistrement
            $participant = Participant::create([
                'civility_id' => $request->title,
                'fname' => $request->first_name,
                'lname' => $request->last_name,
                'student_level_id' => $request->education,
                'gender_id' => $request->gender,
                'nationality_id' => $request->country,
                'user_id' => $userId,
                'registration_id' => auth()->user()->user_id,
                'congres_id' => $meeting->id
            ]);
        }

        session(['step' => 2, 'participant_id' => $participant->id]);

        return redirect()->back();
    }

    public function step2(Request $request)
    {

        $Participant = Participant::find($request->participant_id);
        $Participant->update([
            'email' => Auth::user()->email,
            'phone' => $request->telephone,
            'organisation' => $request->organisation,
            'organisation_type_id' => $request->type_organisation,
            'organisation_type_other' => $request->autre_type_org,
            'job' => $request->fonction
        ]);

        session(['step' => 3]);
        return redirect()->back();
    }
    public function step3(Request $request, InvoiceService $invoiceService)
    {
        $Participant = Participant::find($request->participant_id);
        if ($request->hasFile('photo_passeport')) {
            $path = $request->file('photo_passeport')->store('passeports', 'public');
        }
        $data = [
            'participant_category_id' => $request->category,
            'type_member_id' => $request->membership,
            'membership_code' => $request->membershipcode,
            'diner' => $request->diner_gala,
            'visite' => $request->visite_touristique,
            'passeport_number' => $request->num_passeport,
            'invitation_letter' => $request->lettre_invitation,
            'author' => $request->auteur,
            'type_participant'=>'individual',
        ];

        //Ajouter `passeport_pdf` seulement si un fichier a été uploadé
        if (!empty($path)) {
            $data['passeport_pdf'] = $path;
        }
        //Update du participant
        $Participant->update($data);
/* 
        //Créer la facture
        $data = [
            'participant_id' => $Participant->id,
            'status' => 'pending',
        ];
        //list des items
        $items = [
            ['description' => app()->getLocale() == 'fr' ? 'Frais d’inscription ' . '-'
                . $Participant->participantCategory->libelle . ' - ' . $Participant->typeMember->libelle
                : 'Registration fee ' . ' - ' . $Participant->participantCategory->libelle . ' - ' . $Participant->typeMember->libelle, 'price' => $Participant->typeMember->amount],

            ['description' => app()->getLocale() == 'fr' ? 'Diner gala' : 'Gala dinner', 'price' => $request->diner_gala == 'oui' ? $Participant->congres->amount_diner : 0],

            ['description' => app()->getLocale() == 'fr' ? 'Visites techniques' : 'Technical Tours', 'price' => $request->visite_touristique == 'oui' ? $Participant->congres->amount_visit : 0],
        ];

        $invoice = $invoiceService->createOrUpdateInvoice($data, $items);
 */
        session(['step' => 1]);

        return redirect()->back()->with('success', 'Enregistrement effectué avec succès !');
    }

    public function previous()
    {
        $session = Session::get('step') > 0 ? Session::get('step') : 1;

        Session::put('step', $session - 1);

        return redirect()->back();
    }
}
