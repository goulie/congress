<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Country;
use App\Models\Participant;
use App\Traits\GenerateCodeQrTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterInviteController extends Controller
{
    public function listCountries()
    {
        $countries = Country::all();

        return response()->json($countries);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'genre' => 'required|in:1,2',
            'nationalite' => 'required|exists:countries,id',
            'email' => 'required|email',
            'telephone' => 'required|string|max:20',
            'organisation' => 'required|string|max:255',
            'job' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $code = (string) Str::uuid();
        $congresId = Congress::latest()->value('id');

        Participant::withoutEvents(function () use ($request, $code, $congresId) {

            $participant = Participant::create([
                'gender_id'        => $request->genre,
                'fname'            => $request->prenoms,
                'lname'            => $request->nom,
                'nationality_id'   => $request->nationalite,
                'email'            => $request->email,
                'phone'            => $request->telephone,
                'organisation'     => $request->organisation,
                'job'              => $request->job,
                'type_participant' => 'invite',
                'langue'           => app()->getLocale(),
                'congres_id'       => $congresId,
                'user_id'          => auth()->id(),

                // champ critique
                'uuid' => $code,
            ]);

            // Génération du QR (logique métier explicite)
            $participant->code_path = $participant->generateAndStoreQrCode($code);

            // Toujours dans withoutEvents → aucun observer déclenché
            $participant->save();
        });


        return response()->json([
            'success' => true,
            'message' => app()->getLocale() == 'fr' ? 'Invité créé avec succès' : 'Invite created successfully',
        ]);
    }

    

    public function destroy($id)
    {
        $invite = Participant::find($id);

        if (!$invite) {
            return response()->json([
                'success' => false,
                'message' => app()->getLocale() == 'fr' ? 'Invité non trouvé' : 'Invite not found'
            ], 404);
        }

        $invite->delete();

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() == 'fr' ? 'Invité supprimé avec succès' : 'Invite deleted successfully'
        ]);
    }
}
