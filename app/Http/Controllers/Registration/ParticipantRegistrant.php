<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Participant;
use App\Models\User;
use App\Notifications\StudentOrYwpregistrantNotification;
use App\Services\EmailService;
use App\Services\InvoiceService;
use App\Traits\MemberApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Svg\Tag\Rect;
use Symfony\Component\Mime\Email;

class ParticipantRegistrant extends Controller
{
    use MemberApiTrait;

    public ?string $company;
    public ?string $type_member;
    protected EmailService $emailService;

    public function __construct(EmailService $emailService, ?string $company = null, ?string $type_member = null)
    {
        $this->emailService = $emailService;
        $this->company = $company;
        $this->type_member = $type_member;
    }

    public function index()
    {
        return view('registration.participant-registrant');
    }
    public function step(Request $request)
    {
        try {
            //Locale
            $locale = app()->getLocale();
            $participant = Participant::where('uuid', $request->uuid)->first();

            //
            if ($participant->invoices->first()->status == 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => $locale == 'fr'
                        ? 'Les information ne peuvent changer car ce participant a déja une facture payée  .'
                        : 'The information can not change because this participant has already paid an invoice  .',
                    'membership_code' => '',
                    'status' => 'paid_invoice'
                ], 422);
            }


            $membershipCode = $request->member_code;
            //Récupérer le code de membre

            $memberData = null;
            $typeMember = null;
            $company = null;
            $sigle = null;

            if (isset($membershipCode)) {

                $result = $this->checkMemberSubscription($membershipCode, $locale);

                if (!$result['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                        'membership_code' => $membershipCode,
                        'status' => $result['member_status']
                    ], 422);
                }
                // Récupérer le type_member et la company du membre
                $memberData = $result['data']['member'] ?? null;
                $typeMember = $result['data']['type_member']['libelle'] ?? null;
                $company = $result['data']['companies'] ?? null;
                $sigle = $result['data']['sigle'] ?? null;
            }
            // Récupérer le type_member et la company du membre

            // Messages d'erreur personnalisés selon la langue
            $messages = [
                'required' => $locale == 'fr' ? 'Le champ :attribute est obligatoire.' : 'The :attribute field is required.',
                'email' => $locale == 'fr' ? 'Le champ :attribute doit être une adresse email valide.' : 'The :attribute must be a valid email address.',
                'file' => $locale == 'fr' ? 'Le champ :attribute doit être un fichier.' : 'The :attribute must be a file.',
                'image' => $locale == 'fr' ? 'Le champ :attribute doit être une image.' : 'The :attribute must be an image.',
                'max' => $locale == 'fr' ? 'Le champ :attribute ne doit pas dépasser :max caractères.' : 'The :attribute must not exceed :max characters.',
                'string' => $locale == 'fr' ? 'Le champ :attribute doit être une chaîne de caractères.' : 'The :attribute must be a string.',
                'exists' => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'The selected :attribute is invalid.',
                'in' => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'The selected :attribute is invalid.',
                'required_if' => $locale == 'fr' ? 'Le champ :attribute est obligatoire.' : 'The :attribute field is required ',
                'mimes' => $locale == 'fr' ? 'Le fichier :attribute doit être de type :values.' : 'The :attribute file must be a :values.',
            ];

            // Attributs personnalisés pour les noms de champs
            $attributes = [
                'title' => $locale == 'fr' ? 'civilité' : 'title',
                'first_name' => $locale == 'fr' ? 'prénom' : 'first name',
                'last_name' => $locale == 'fr' ? 'nom' : 'last name',
                'education' => $locale == 'fr' ? 'niveau d\'étude' : 'education level',
                'gender' => $locale == 'fr' ? 'genre' : 'gender',
                'country' => $locale == 'fr' ? 'pays de nationalité' : 'nationality',
                'email' => $locale == 'fr' ? 'email' : 'email',
                'telephone' => $locale == 'fr' ? 'téléphone' : 'telephone',
                'organisation' => $locale == 'fr' ? 'organisation' : 'organization',
                'type_organisation' => $locale == 'fr' ? 'type d\'organisation' : 'organization type',
                'autre_type_org' => $locale == 'fr' ? 'autre type d\'organisation' : 'other organization type',
                'fonction' => $locale == 'fr' ? 'fonction' : 'function',
                'categorie.id,1' => $locale == 'fr' ? 'Délégué' : 'Deleguate',
                'membership' => $locale == 'fr' ? 'adhésion' : 'membership',
                'member_code' => $locale == 'fr' ? 'code d\'adhésion' : 'membership code',
                'dinner' => $locale == 'fr' ? 'dîner de gala' : 'gala dinner',
                'visit' => $locale == 'fr' ? 'visite technique' : 'technical visit',
                'passport_number' => $locale == 'fr' ? 'numéro de passeport' : 'passport number',
                'lettre_invitation' => $locale == 'fr' ? 'lettre d\'invitation' : 'invitation letter',
                'student_card' => $locale == 'fr' ? 'carte étudiante' : 'student card',
                'student_letter' => $locale == 'fr' ? 'lettre d\'attestation' : 'attestation letter',
                'pass_date' => $locale == 'fr' ? 'date de pass' : 'pass date',
                'pass_deleguate' => $locale == 'fr' ? 'Pass jour délégué' : 'Pass day deleguate',
            ];

            // Règles de validation de base
            $rules = [
                'categorie'           => 'required|in:1,4',
                'dinner'              => 'required|in:oui,non',
                'visit'               => 'required|in:oui,non',
                'lettre_invitation'   => 'required|in:oui,non',

                // Visite technique
                'site_visit'          => 'required_if:visit,oui|nullable',

                // ----------------------- DÉLÉGUÉ -----------------------
                // Passe 1 jour
                'pass_deleguate'      => 'required_if:categorie,1|nullable|in:oui,non',

                // Dates du pass
                'pass_date'           => 'required_if:pass_deleguate,oui|array|nullable',

                // Passeport obligatoire pour délégué
                'passport_number'     => 'required_if:categorie,1|string|max:255',
                'passport_date'       => 'required_if:categorie,1|date_format:Y-m-d',

                // Membership si pass = non
                'membership'          => 'required_if:pass_deleguate,non|required_if:categorie,4|nullable|in:oui,non',

                // Code membre si membership = oui
                'member_code'         => 'required_if:membership,oui|string|max:255',

                // ----------------------- ÉTUDIANT -----------------------
                'ywp_or_student'      => 'required_if:categorie,4|in:ywp,student',
            ];
            //
            if ($request->hasFile('student_card') && !$participant->student_card) {
                $rules['student_card'] = 'required_if:ywp_or_student,ywp|file|max:2048';
            }

            if ($request->hasFile('student_letter') && !$participant->student_letter) {
                $rules['student_letter'] = 'required_if:ywp_or_student,student|file|max:2048';
            }

            //Application de la validation et des règles
            $validated = $request->validate($rules, $messages, $attributes);

            //company
            $organisation = $sigle ? $sigle : $company;

            $participant->update([
                'participant_category_id' => $request->categorie,
                'ywp_or_student' => $request->ywp_or_student ?? null,
                'membership_code' => $request->member_code,
                'organisation' => $participant->organisation ? $participant->organisation : $organisation,
                'diner' => $request->dinner,
                'visite' => $request->visit,
                'passeport_number' => $request->passport_number,
                'invitation_letter' => $request->lettre_invitation,
                'deleguate_day' => json_encode($request->pass_date),
                'langue' => $locale,
                'site_visit_id' => $request->site_visit ?? null,
                'membre_aae' => $request->membership ?? 'non',
                'pass_deleguate' => $request->pass_deleguate ?? null,
                'expiration_passeport_date' => $request->passport_date
            ]);


            if ($request->hasFile('student_card')) {
                $participant->student_card = $request->file('student_card')->store('uploads/student_cards', 'public');
            }
            if ($request->hasFile('student_letter')) {
                $participant->student_letter = $request->file('student_letter')->store('uploads/student_letters', 'public');
            }

            $participant->save();

            // Redirection vers la page de confirmation

            session(['step' => 2]);

            return response()->json([
                'success' => true,
                'message' => $locale == 'fr'
                    ? 'Etape 1 Enregstrée avec succès !'
                    : 'Step 1 registered successfully !',
                'participant_id' => $participant->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $locale == 'fr' ? 'Erreur de validation' : 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Erreur modification participant: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'request' => $request->all(),
                'uuid' => $request->uuid
            ]);

            return response()->json([
                'success' => false,
                'message' => $locale == 'fr'
                    ? 'Une erreur est survenue lors de la modification.'
                    : 'An error occurred during update.'
            ], 500);
        }
    }

    public function step1(Request $request)
    {
        /* dd($request->all()); */

        $meeting = Congress::latest('id')->first();
        $userId = auth()->user()->id;

        // Vérifier si le participant existe déjà
        $participant = Participant::where('uuid', $request->uuid)
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
                'age_range_id' => $request->age_range
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

        session(['step' => 3, 'participant_id' => $participant->id]);

        return redirect()->back()->with('success', app()->getLocale() == 'fr' ? 'informations enregistrées avec success !' : 'Informations registered successfully !');
    }

    public function step2(Request $request)
    {
        DB::beginTransaction();

        try {
            // Vérification UUID
            if (!$request->uuid) {
                return back()->withErrors(['uuid' => 'Identifiant du participant manquant.']);
            }

            // Récupération du participant
            $participant = Participant::where('uuid', $request->uuid)->first();
            if (!$participant) {
                return back()->withErrors(['participant' => 'Participant introuvable.']);
            }

            // Validation
            $validated = $request->validate([
                'telephone' => 'required|string|min:6|max:20',
                'organisation' => 'required|string|max:255',
                'type_organisation' => 'required|exists:type_organisations,id',
                'autre_type_org' => 'nullable|string|max:255',
                'fonction' => 'required|string|max:255',
                'job_country' => 'required|exists:countries,id',
            ]);

            // Mise à jour
            $participant->update([
                'email' => Auth::user()->email ?? $participant->email,
                'phone' => $validated['telephone'],
                'organisation' => $validated['organisation'],
                'organisation_type_id' => $validated['type_organisation'],
                'organisation_type_other' => $validated['autre_type_org'] ?? null,
                'job' => $validated['fonction'],
                'job_country_id' => $validated['job_country'],
            ]);

            DB::commit();

            //Si participant est student ou ywp 
            if ($participant->ywp_or_student == 'ywp' || $participant->ywp_or_student == 'student') {
                foreach (User::where('role_id', 6)->get() as $admin) {
                    $admin->notify(new StudentOrYwpregistrantNotification($participant));
                }
            }

            // Passage à l’étape suivante
            session(['step' => 4]);

            // Redirection ou affichage de la vue
            /* return view('voyager::view-single-registrations.browse', compact('participant')); */

            return redirect()->route('participant.recap', $request->uuid);
        } catch (\Throwable $ex) {
            DB::rollBack();

            Log::error('Erreur modification participant: ' . $ex->getMessage(), [
                'uuid' => $request->uuid,
                'trace' => $ex->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour.']);
        }
    }
    /* public function step3(Request $request, InvoiceService $invoiceService)
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
            'type_participant' => 'individual',
        ];

        //Ajouter `passeport_pdf` seulement si un fichier a été uploadé
        if (!empty($path)) {
            $data['passeport_pdf'] = $path;
        }
        //Update du participant
        $Participant->update($data);

        session(['step' => 1]);

        return redirect()->to('/admin')->with('success', 'Enregistrement effectué avec succès !');
    } */

    public function previous()
    {
        $session = Session::get('step') > 0 ? Session::get('step') : 2;

        Session::put('step', $session - 1);

        return redirect()->back();
    }

    public function recap($participant)
    {
        $edit = 'recap';
        session(['step' => 4]);
        return view('voyager::view-single-registrations.browse', compact('participant'));
    }

    public function editWithStep(Request $request)
    {
        session(['step' => 1]);

        return redirect()->route('voyager.view-single-registrations.index');
    }
}
