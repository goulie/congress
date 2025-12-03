<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Imports\ParticipantsImport;
use App\Models\Congress;
use App\Models\Participant;
use App\Models\StudentYwpValidation;
use App\Models\User;
use App\Notifications\StudentOrYwpregistrantNotification;
use App\Services\EmailService;
use App\Traits\MemberApiTrait;
use App\Traits\TypeMemberCheckerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GroupeRegistrant extends Controller
{
    use MemberApiTrait;
    use TypeMemberCheckerTrait;

    protected  $company, $type_member, $emailService;


    public function __construct(EmailService $emailService, $company = null, $type_member = null)
    {
        $this->company = $company;
        $this->type_member = $type_member;
        $this->emailService = $emailService;
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $locale = $request->langue;

            /* ============================================================
                 * CHECK MEMBERSHIP
                 * ============================================================ */
            if ($request->member_code) {

                $result = $this->checkMemberSubscription($request->member_code, $locale);

                if (!$result['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                        'membership_code' => $request->member_code,
                        'status' => $result['member_status']
                    ], 422);
                }
            }

            /* ============================================================
                 * MESSAGES & ATTRIBUTS PERSONNALISÉS
                 * ============================================================ */
            $messages = [
                'required'      => $locale == 'fr' ? 'Le champ :attribute est obligatoire.' : 'The :attribute field is required.',
                'email'         => $locale == 'fr' ? 'Le champ :attribute doit être un email valide.' : 'The :attribute must be a valid email.',
                'string'        => $locale == 'fr' ? 'Le champ :attribute doit être une chaîne de caractères.' : 'The :attribute must be a string.',
                'exists'        => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'The selected :attribute is invalid.',
                'in'            => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'Invalid :attribute value.',
                'required_if'   => $locale == 'fr' ? 'Le champ :attribute est obligatoire.' : 'The :attribute field is required.',
                'mimes'         => $locale == 'fr' ? 'Le fichier :attribute doit être de type :values.' : 'The :attribute file must be a :values.',
                'max'           => $locale == 'fr' ? 'Le champ :attribute ne doit pas dépasser :max caractères.' : 'The :attribute may not exceed :max characters.',
                'min'           => $locale == 'fr' ? 'Vous devez sélectionner au moins un jour.' : 'You must select at least one day.',
            ];

            $attributes = [
                'categorie'         => $locale == 'fr' ? 'catégorie' : 'category',
                'membership'        => $locale == 'fr' ? 'adhésion' : 'membership',
                'member_code'       => $locale == 'fr' ? 'code membre' : 'membership code',
                'passport_number'   => $locale == 'fr' ? 'numéro de passeport' : 'Passport number', //'numéro de passeport',
                'passport_date'     => $locale == 'fr' ? 'date d\'expiration du passeport' : 'passeport expiration date', //'date d\'expiration du passeport',
                'pass_deleguate'    => $locale == 'fr' ? 'pass délégué' : 'delegat pass', //'pass délégué',
                'pass_date'         => $locale == 'fr' ? 'jour de pass' : 'pass date', //'jour de pass',
                'ywp_or_student'    => $locale == 'fr' ? 'type étudiant ou jeune professionnel' : 'student or young water professional', //'type étudiant',
                'student_card'      => $locale == 'fr' ? 'carte étudiant' : 'student card', //'carte étudiant',
                'student_letter'    => $locale == 'fr' ? 'lettre d\'attestation' : 'invitation letter', //'lettre d\'attestation',
                'visit'             => $locale == 'fr' ? 'visite technique' : 'Technical visit', //'visite technique',
                'site_visit'        => $locale == 'fr' ? 'site de visite' : 'Visit site', //'site de visite',
            ];

            /* ============================================================
                 * RÈGLES DE VALIDATION (BLOC PRINCIPAL)
                 * ============================================================ */
            $rules = [
                'categorie'         => 'required|exists:category_participants,id',
                'dinner'            => 'required|in:oui,non',
                'visit'             => 'required|in:oui,non',
                'lettre_invitation' => 'required|in:oui,non',
                'title'             => 'required|exists:civilities,id',
                'first_name'        => 'required|string|max:255',
                'last_name'         => 'required|string|max:255',
                'education'         => 'required|exists:student_levels,id',
                'gender'            => 'required|exists:genders,id',
                'country'           => 'required|exists:countries,id',
                'email'             => 'required|email|unique:participants,email',
                'telephone'         => 'required|string|max:25',
                'organisation'      => 'required|string|max:255',
                'type_organisation' => 'required|exists:type_organisations,id',
                'autre_type_org'    => 'required_if:type_organisation,10|string|nullable|max:255',
                'fonction'          => 'required|string|max:255',
                'job_country'       => 'nullable|exists:countries,id',
                'passport_number' => 'required|string|max:50',
                'passport_date'   => 'required|date|after:today',
            ];


            // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            // 1. DÉLÉGUÉ
            // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            if ($request->categorie == 1) {

                

                // Pass obligatoire ?
                $rules['pass_deleguate'] = 'required|in:oui,non';

                if ($request->pass_deleguate == 'oui') {
                    // Au moins UN jour sélectionné
                    $rules['pass_date'] = 'required|array|min:1';
                } else {
                    // Membership obligatoire SI pas de pass
                    $rules['membership'] = 'required|in:oui,non';

                    if ($request->membership == 'oui') {
                        $rules['member_code'] = 'required|string|max:255';
                    }
                }
            }

            // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            // 2. STUDENT
            // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            if ($request->categorie == 4) {
                // fichiers obligatoires
                if ($request->ywp_or_student == 'student') {
                    $rules['student_card'] = 'required|mimes:jpeg,png,jpg,pdf|max:2048';
                }
                if ($request->ywp_or_student == 'ywp') {
                    $rules['student_letter'] = 'required|mimes:jpeg,png,jpg,pdf|max:2048';
                }
            }

            // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            // 3. VISITE TECHNIQUE
            // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            if ($request->visit == 'oui') {
                $rules['site_visit'] = 'required|exists:site_visites,id';
            }

            /* ============================================================
                 * VALIDATION
                 * ============================================================ */
            $validated = $request->validate($rules, $messages, $attributes);

            /* ============================================================
                 * VERIF EMAIL PAR CONGRÈS
                 * ============================================================ */
            $congresId = Congress::latest()->first()->id;

            if (Participant::where(['email' => $request->email, 'congres_id' => $congresId])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => $locale == 'fr'
                        ? 'Cette adresse email est déjà enregistrée pour ce congrès.'
                        : 'This email is already registered for this congress.'
                ], 422);
            }

            /* ============================================================
                 * ENREGISTREMENT EN BDD
            * ============================================================ */
            $participant = Participant::create([
                'civility_id'             => $request->title,
                'gender_id'               => $request->gender,
                'fname'                   => $request->first_name,
                'type_member_id'          => $this->getMembershipTypeIdFromCode($request->member_code),
                'lname'                   => $request->last_name,
                'student_level_id'        => $request->education,
                'nationality_id'          => $request->country,
                'age_range_id'            => $request->age_range,
                'email'                   => $request->email,
                'phone'                   => $request->telephone_complet ?: $request->telephone,
                'organisation'            => $request->organisation,
                'organisation_type_id'    => $request->type_organisation,
                'organisation_type_other' => $request->autre_type_org,
                'job'                     => $request->fonction,
                'job_country_id'          => $request->job_country,
                'participant_category_id' => $request->categorie,
                'membre_aae'              => $request->membership,
                'membership_code'         => $request->member_code,
                'ywp_or_student'          => $request->ywp_or_student,
                'diner'                   => $request->dinner,
                'visite'                  => $request->visit,
                'site_visit_id'           => $request->site_visit,
                'passeport_number'        => $request->passport_number,
                'expiration_passeport_date' => $request->passport_date,
                'invitation_letter'       => $request->lettre_invitation,
                'pass_deleguate'          => $request->pass_deleguate,
                'deleguate_day'           => $request->pass_date ? json_encode($request->pass_date) : null,
                'type_participant'        => 'grouped',
                'langue'                  => $locale,
                'congres_id'              => $congresId,
                'user_id'                 => auth()->id(),
            ]);

            /* ============================================================
                 * GESTION DES FICHIERS
                 * ============================================================ */
            if ($request->hasFile('student_card')) {
                $participant->student_card = $request->file('student_card')
                    ->store('uploads/student_cards', 'public');
            }

            if ($request->hasFile('student_letter')) {
                $participant->student_letter = $request->file('student_letter')
                    ->store('uploads/student_letters', 'public');
            }

            $participant->save();

            DB::commit();

            //Send Notification to Admin
            if ($participant->ywp_or_student == 'ywp' || $participant->ywp_or_student == 'student') {
                foreach (User::where('role_id', 6)->get() as $admin) {
                    $admin->notify(new StudentOrYwpregistrantNotification($participant));
                }

                $participant->validation_ywp_student()->create([
                    'status' => StudentYwpValidation::STATUS_PENDING
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => $locale == 'fr'
                    ? 'Participant créé avec succès !'
                    : 'Participant created successfully!',
                'participant_id' => $participant->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $locale == 'fr' ? 'Erreur de validation' : 'Validation error',
                'errors'  => $e->errors()
            ], 422);

            //mettre en log
            Log::error('Une erreur est survenue lors de la création du participant : ' . $e->getMessage());
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Une erreur est survenue lors de la création du participant :'  . $ex->getMessage());

            return response()->json([
                'success' => false,
                'message' => $locale == 'fr'
                    ? 'Une erreur est survenue.'
                    : 'An error occurred.'
            ], 500);
        }
    }


    public function edit($uuid)
    {
        $participant = Participant::where('uuid', $uuid)->first();
        // Changement de langue
        App::setlocale($participant->langue);
        $edit = 'edit';
        return view('vendor.voyager.view-group-registrations.browse', compact('participant', 'edit'));
    }



    public function update(Request $request)
    {
        $locale = $request->langue;

        try {
            DB::beginTransaction();

            /* ============================================================
         * VALIDATION DU CODE MEMBRE
         * ============================================================ */
            if ($request->member_code) {

                $result = $this->checkMemberSubscription($request->member_code, $locale);

                if (!$result['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'],
                        'membership_code' => $request->member_code,
                        'status' => $result['member_status']
                    ], 422);
                }
            }

            /* ============================================================
         * MESSAGES & ATTRIBUTS
         * ============================================================ */
            $messages = [
                'required'      => $locale == 'fr' ? 'Le champ :attribute est obligatoire.' : 'The :attribute field is required.',
                'email'         => $locale == 'fr' ? 'Cet adresse email a été déjà utilisée.' : 'This email has already been used.',
                'string'        => $locale == 'fr' ? 'Le champ :attribute doit être une chaîne de caractères.' : 'The :attribute must be a string.',
                'exists'        => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'Invalid :attribute.',
                'in'            => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'Invalid :attribute.',
                'required_if'   => $locale == 'fr' ? 'Le champ :attribute est obligatoire.' : 'The :attribute field is required.',
                'mimes'         => $locale == 'fr' ? 'Le fichier :attribute doit être de type :values.' : 'The :attribute file must be :values.',
                'min'           => $locale == 'fr' ? 'Veuillez sélectionner au moins un jour.' : 'Select at least one day.',
            ];

            $attributes = [
                'title'             => 'civilité',
                'first_name'        => 'prénoms',
                'last_name'         => 'nom',
                'education'         => 'niveau d’étude',
                'gender'            => 'genre',
                'country'           => 'pays',
                'email'             => 'email',
                'type_organisation' => 'type organisation',
                'autre_type_org'    => 'autre organisation',
                'fonction'          => 'fonction',
                'categorie'         => 'catégorie',
                'membership'        => 'adhésion',
                'member_code'       => 'code membre',
                'passport_number'   => 'numéro passeport',
                'passport_date'     => 'date passeport',
                'pass_date'         => 'jours de pass',
                'ywp_or_student'    => 'type étudiant',
                'student_card'      => 'carte étudiant',
                'student_letter'    => 'lettre attestation',
            ];

            /* ============================================================
         * RÈGLES COMMUNES
         * ============================================================ */
            $rules = [
                'uuid'              => 'required|exists:participants,uuid',
                'title'             => 'required|exists:civilities,id',
                'first_name'        => 'required|string|max:255',
                'last_name'         => 'required|string|max:255',
                'education'         => 'required|exists:student_levels,id',
                'gender'            => 'required|exists:genders,id',
                'country'           => 'required|exists:countries,id',
                'email'             => 'required|email',
                'telephone'         => 'required|string|max:25',
                'organisation'      => 'required|string|max:255',
                'type_organisation' => 'required|exists:type_organisations,id',
                'autre_type_org'    => 'required_if:type_organisation,10|string|nullable|max:255',
                'fonction'          => 'required|string|max:255',
                'categorie'         => 'required|exists:category_participants,id',
                'dinner'            => 'required|in:oui,non',
                'visit'             => 'required|in:oui,non',
                'lettre_invitation' => 'required|in:oui,non',
                'passport_number'   => 'required|string|max:255',
                'passport_date'     => 'required|date|after:today',
                
            ];

            /* ============================================================
         * CATÉGORIE : DÉLÉGUÉ
         * ============================================================ */
            if ($request->categorie == 1) {
                $rules['pass_deleguate']  = 'required|in:oui,non';
                if ($request->pass_deleguate == 'oui') {
                    $rules['pass_date'] = 'required|array|min:1';
                } else {
                    $rules['membership'] = 'required|in:oui,non';
                    if ($request->membership == 'oui') {
                        $rules['member_code'] = 'required|string|max:255';
                    }
                }
            }

            /* ============================================================
         * CATÉGORIE : STUDENT
         * ============================================================ */
            if ($request->categorie == 4) {

                $rules['ywp_or_student'] = 'required|in:ywp,student';


                if ($request->hasFile('student_card')) {
                    $rules['student_card'] = 'mimes:jpeg,png,jpg|max:2048';
                }

                if ($request->hasFile('student_letter')) {
                    $rules['student_letter'] = 'mimes:jpeg,png,jpg,pdf|max:2048';
                }
            }

            /* ============================================================
         * VISITE
         * ============================================================ */
            if ($request->visit == 'oui') {
                $rules['site_visit'] = 'required|exists:site_visites,id';
            }

            /* ============================================================
         * VALIDATION
         * ============================================================ */
            $validated = $request->validate($rules, $messages, $attributes);

            /* ============================================================
         * CHECK EMAIL DUPLICATION SUR MÊME CONGRÈS
         * ============================================================ */
            $participant = Participant::where('uuid', $request->uuid)->firstOrFail();
            $congresId   = $participant->congres_id;

            $emailConflict = Participant::where('email', $request->email)
                ->where('congres_id', $congresId)
                ->where('id', '!=', $participant->id)
                ->exists();

            if ($emailConflict) {
                return response()->json([
                    'success' => false,
                    'message' => $locale == 'fr'
                        ? 'Cette adresse email est déjà utilisée pour un autre participant.'
                        : 'This email already exists for another participant.'
                ], 422);
            }

            /* ============================================================
         * MISE À JOUR
         * ============================================================ */
            $participant->update([
                'civility_id'             => $request->title,
                'gender_id'               => $request->gender,
                'type_member_id'          => $request->member_code ? $this->getMembershipTypeIdFromCode($request->member_code): null,
                'fname'                   => $request->first_name,
                'lname'                   => $request->last_name,
                'student_level_id'        => $request->education,
                'nationality_id'          => $request->country,
                'age_range_id'            => $request->age_range,
                'email'                   => $request->email,
                'phone'                   => $request->telephone_complet ?: $request->telephone,
                'organisation'            => $request->organisation,
                'organisation_type_id'    => $request->type_organisation,
                'organisation_type_other' => $request->autre_type_org,
                'job'                     => $request->fonction,
                'job_country_id'          => $request->job_country,
                'participant_category_id' => $request->categorie,
                'membre_aae'              => $request->membership,
                'membership_code'         => $request->member_code ?? null,
                'ywp_or_student'          => $request->ywp_or_student,
                'diner'                   => $request->dinner,
                'visite'                  => $request->visit,
                'site_visit_id'           => $request->site_visit,
                'passeport_number'        => $request->passport_number,
                'expiration_passeport_date' => $request->passport_date,
                'invitation_letter'       => $request->lettre_invitation,
                'pass_deleguate'          => $request->pass_deleguate,
                'deleguate_day'           => $request->pass_date ? json_encode($request->pass_date) : null,
            ]);

            /* ============================================================
         * UPLOAD FILES
         * ============================================================ */
            if ($request->hasFile('student_card')) {
                Storage::disk('public')->delete($participant->student_card);
                $participant->student_card = $request->file('student_card')
                    ->store('uploads/student_cards', 'public');
            }

            if ($request->hasFile('student_letter')) {
                Storage::disk('public')->delete($participant->student_letter);
                $participant->student_letter = $request->file('student_letter')
                    ->store('uploads/student_letters', 'public');
            }

            $participant->save();

            DB::commit();

            if ($participant->ywp_or_student == 'ywp' || $participant->ywp_or_student == 'student') {
                //Envoyyer un mail de traitement de dossier
                foreach (User::where('role_id', 6)->get() as $admin) {
                    $admin->notify(new StudentOrYwpregistrantNotification($participant));
                }

                $participant->validation_ywp_student()->create([
                    'status' => StudentYwpValidation::STATUS_PENDING
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $locale == 'fr'
                    ? 'Participant mis à jour avec succès !'
                    : 'Participant updated successfully!',
                'url' => route('participant.recap', $participant->uuid),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $locale == 'fr' ? 'Erreur de validation' : 'Validation error',
                'errors'  => $e->errors()
            ], 422);

            Log::error("UPDATE error participant {$request->uuid}: " . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();

            Log::error("UPDATE error participant {$request->uuid}: " . $ex->getMessage(), [
                'trace'   => $ex->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $locale == 'fr'
                    ? 'Une erreur est survenue lors de la mise à jour.'
                    : 'Update failed.',
            ], 500);
        }
    }


    public function destroy($uuid)
    {
        $locale = app()->getLocale();

        $participant = Participant::where('uuid', $uuid)->first();
        try {

            // Supprimer le fichier photo passeport s'il existe
            if ($participant->passeport_pdf && Storage::disk('public')->exists($participant->passeport_pdf)) {
                Storage::disk('public')->delete($participant->passeport_pdf);
            }

            // Supprimer le participant
            $participant->delete();

            return response()->json([
                'success' => true,
                'message' => $locale == 'fr'
                    ? 'Participant supprimé avec succès !'
                    : 'Participant deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $locale == 'fr'
                    ? 'Une erreur est survenue lors de la suppression.'
                    : 'An error occurred while deleting.'
            ], 500);
        }
    }

    public function recap($uuid)
    {
        $participant = Participant::where('uuid', $uuid)->first();
        $edit = 'recap';

        return view('voyager::view-group-registrations.browse', compact('participant', 'edit'));
    }
}
