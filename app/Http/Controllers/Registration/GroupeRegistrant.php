<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Imports\ParticipantsImport;
use App\Models\Congress;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GroupeRegistrant extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validation selon la langue
            $locale = app()->getLocale();

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
                'required_if' => $locale == 'fr' ? 'Le champ :attribute est obligatoire lorsque :other est :value.' : 'The :attribute field is required when :other is :value.',
                'mimes' => $locale == 'fr' ? 'Le fichier :attribute doit être de type :values.' : 'The :attribute file must be a :values.',
            ];

            // Attributs personnalisés pour les noms de champs
            $attributes = [
                'title' => $locale == 'fr' ? 'civilité' : 'title',
                'first_name' => $locale == 'fr' ? 'prénom' : 'first name',
                'last_name' => $locale == 'fr' ? 'nom' : 'last name',
                'education' => $locale == 'fr' ? 'niveau d\'étude' : 'education level',
                'gender' => $locale == 'fr' ? 'genre' : 'gender',
                'country' => $locale == 'fr' ? 'pays' : 'country',
                'email' => $locale == 'fr' ? 'email' : 'email',
                'telephone' => $locale == 'fr' ? 'téléphone' : 'telephone',
                'organisation' => $locale == 'fr' ? 'organisation' : 'organization',
                'type_organisation' => $locale == 'fr' ? 'type d\'organisation' : 'organization type',
                'autre_type_org' => $locale == 'fr' ? 'autre type d\'organisation' : 'other organization type',
                'fonction' => $locale == 'fr' ? 'fonction' : 'function',
                'category' => $locale == 'fr' ? 'catégorie' : 'category',
                'membership' => $locale == 'fr' ? 'adhésion' : 'membership',
                'membershipcode' => $locale == 'fr' ? 'code d\'adhésion' : 'membership code',
                'diner_gala' => $locale == 'fr' ? 'dîner de gala' : 'gala dinner',
                'visite_touristique' => $locale == 'fr' ? 'visite touristique' : 'tourist visit',
                'num_passeport' => $locale == 'fr' ? 'numéro de passeport' : 'passport number',
                'lettre_invitation' => $locale == 'fr' ? 'lettre d\'invitation' : 'invitation letter',
                'auteur' => $locale == 'fr' ? 'auteur' : 'author',
                'photo_passeport' => $locale == 'fr' ? 'photo de passeport' : 'passport photo',
            ];

            $validated = $request->validate([
                'title' => 'required|exists:civilities,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'education' => 'required|exists:student_levels,id',
                'gender' => 'required|exists:genders,id',
                'country' => 'required|exists:countries,id',
                'email' => 'required|email|unique:participants,email',
                'telephone' => 'required|string|max:25',
                'organisation' => 'required|string|max:255',
                'type_organisation' => 'required|exists:type_organisations,id',
                'autre_type_org' => 'required_if:type_organisation,autre|string|max:255|nullable',
                'fonction' => 'required|string|max:255',
                'category' => 'required|exists:category_participants,id',
                'membership' => 'required|exists:type_members,id',
                'membershipcode' => 'required_if:membership,1|string|max:255|nullable',
                'diner_gala' => 'required|in:oui,non',
                'visite_touristique' => 'required|in:oui,non',
                'num_passeport' => 'required|string|max:50',
                'lettre_invitation' => 'required|in:oui,non',
                'auteur' => 'required|in:oui,non',
                'photo_passeport' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            ], $messages, $attributes);

            // Vérification supplémentaire pour le type d'organisation "autre"
            if ($request->type_organisation == 'autre' && empty($request->autre_type_org)) {
                return back()->withErrors([
                    'autre_type_org' => $locale == 'fr'
                        ? 'Veuillez spécifier le type d\'organisation.'
                        : 'Please specify the organization type.'
                ])->withInput();
            }

            // Vérification supplémentaire pour le code d'adhésion
            if ($request->membership == 1 && empty($request->membershipcode)) {
                return back()->withErrors([
                    'membershipcode' => $locale == 'fr'
                        ? 'Le code d\'adhésion est requis pour ce type de membre.'
                        : 'Membership code is required for this member type.'
                ])->withInput();
            }

            // Stockage du fichier
            $path = null;
            if ($request->hasFile('photo_passeport')) {
                $path = $request->file('photo_passeport')->store('passeports', 'public');
            }

            // Création du participant
            $participant = Participant::create([
                'civility_id' => $request->title,
                'fname' => $request->first_name,
                'lname' => $request->last_name,
                'student_level_id' => $request->education,
                'gender_id' => $request->gender,
                'nationality_id' => $request->country,
                'user_id' => auth()->id(),
                'registration_id' => auth()->user()->user_id ?? null,
                'congres_id' => Congress::latest('id')->first()->id ?? null,
                'email' => $request->email,
                'phone' => $request->telephone,
                'organisation' => $request->organisation,
                'organisation_type_id' => $request->type_organisation,
                'organisation_type_other' => $request->autre_type_org,
                'job' => $request->fonction,
                'participant_category_id' => $request->category,
                'type_member_id' => $request->membership,
                'membership_code' => $request->membershipcode,
                'diner' => $request->diner_gala,
                'visite' => $request->visite_touristique,
                'passeport_number' => $request->num_passeport,
                'invitation_letter' => $request->lettre_invitation,
                'author' => $request->auteur,
                'passeport_pdf' => $path,
                'type_participant' => 'grouped'
            ]);

            return redirect()->back()->with('success', $locale == 'fr'
                ? 'Participant créé avec succès !'
                : 'Participant created successfully!');
        } catch (\Exception $ex) {
            return back()->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => $locale == 'fr' ? 'Erreur !' : 'Error!',
                    'text' => $locale == 'fr' ? 'Une erreur est survenue lors de la création.' .$ex->getMessage() : $ex->getMessage(),
                    'confirmButtonText' => $locale == 'fr' ? 'Compris' : 'Understood',
                ]);
        }
    }

    public function edit($uuid)
    {
        $participant = Participant::where('uuid', $uuid)->first();
        $edit = 'edit';
        return view('vendor.voyager.view-group-registrations.browse', compact('participant', 'edit'));
    }

    public function update(Request $request)
    {
        $participant = Participant::where('uuid', $request->uuid)->first();
        $locale = app()->getLocale();

        // Messages d'erreur personnalisés selon la langue
        $messages = [
            'required' => $locale == 'fr' ? 'Le champ :attribute est obligatoire.' : 'The :attribute field is required.',
            'email' => $locale == 'fr' ? 'Le champ :attribute doit être une adresse email valide.' : 'The :attribute must be a valid email address.',
            'image' => $locale == 'fr' ? 'Le champ :attribute doit être une image.' : 'The :attribute must be an image.',
            'max' => $locale == 'fr' ? 'Le champ :attribute ne doit pas dépasser :max caractères.' : 'The :attribute must not exceed :max characters.',
            'string' => $locale == 'fr' ? 'Le champ :attribute doit être une chaîne de caractères.' : 'The :attribute must be a string.',
            'exists' => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'The selected :attribute is invalid.',
            'in' => $locale == 'fr' ? 'La valeur sélectionnée pour :attribute est invalide.' : 'The selected :attribute is invalid.',
            'required_if' => $locale == 'fr' ? 'Le champ :attribute est obligatoire lorsque :other est :value.' : 'The :attribute field is required when :other is :value.',
            'mimes' => $locale == 'fr' ? 'Le fichier :attribute doit être de type :values.' : 'The :attribute file must be a :values.',
        ];

        // Attributs personnalisés
        $attributes = [
            'title' => $locale == 'fr' ? 'civilité' : 'title',
            'first_name' => $locale == 'fr' ? 'prénom' : 'first name',
            'last_name' => $locale == 'fr' ? 'nom' : 'last name',
            // ... autres attributs comme dans la méthode store
        ];

        $validated = $request->validate([
            'title' => 'required|exists:civilities,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'education' => 'required|exists:student_levels,id',
            'gender' => 'required|exists:genders,id',
            'country' => 'required|exists:countries,id',
            'email' => 'required|email',
            'telephone' => 'required|string|max:25',
            'organisation' => 'required|string|max:255',
            'type_organisation' => 'required|exists:type_organisations,id',
            'autre_type_org' => 'required_if:type_organisation,autre|string|max:255|nullable',
            'fonction' => 'required|string|max:255',
            'category' => 'required|exists:category_participants,id',
            'membership' => 'required|exists:type_members,id',
            'membershipcode' => 'required_if:membership,1|string|max:255|nullable',
            'diner_gala' => 'required|in:oui,non',
            'visite_touristique' => 'required|in:oui,non',
            'num_passeport' => 'required|string|max:50',
            'lettre_invitation' => 'required|in:oui,non',
            'auteur' => 'required|in:oui,non',
            'photo_passeport' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages, $attributes);

        try {
            // Mise à jour des données
            $updateData = [
                'civility_id' => $request->title,
                'fname' => $request->first_name,
                'lname' => $request->last_name,
                'student_level_id' => $request->education,
                'gender_id' => $request->gender,
                'nationality_id' => $request->country,
                'email' => $request->email,
                'phone' => $request->telephone,
                'organisation' => $request->organisation,
                'organisation_type_id' => $request->type_organisation,
                'organisation_type_other' => $request->autre_type_org,
                'job' => $request->fonction,
                'participant_category_id' => $request->category,
                'type_member_id' => $request->membership,
                'membership_code' => $request->membershipcode,
                'diner' => $request->diner_gala,
                'visite' => $request->visite_touristique,
                'passeport_number' => $request->num_passeport,
                'invitation_letter' => $request->lettre_invitation,
                'author' => $request->auteur,
            ];

            // Gestion du fichier photo passeport
            if ($request->hasFile('photo_passeport')) {
                // Supprimer l'ancien fichier si existe
                if ($participant->passeport_pdf && Storage::disk('public')->exists($participant->passeport_pdf)) {
                    Storage::disk('public')->delete($participant->passeport_pdf);
                }

                $path = $request->file('photo_passeport')->store('passeports', 'public');
                $updateData['passeport_pdf'] = $path;
            }

            $participant->update($updateData);

            return redirect()->back()->with('success', $locale == 'fr' ? 'Le participant a bien été mis à jour.' : 'The participant has been successfully updated.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => $locale == 'fr' ? 'Erreur !' : 'Error!',
                    'text' => $locale == 'fr' ? 'Une erreur est survenue lors de la mise à jour.' : 'An error occurred while updating.',
                    'confirmButtonText' => $locale == 'fr' ? 'Compris' : 'Understood',
                ]);
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
}
