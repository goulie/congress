<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Participant;
use Illuminate\Http\Request;

class AccompagningRegistrant extends Controller
{
    public function store(Request $request)
    {
        
        $user = Participant::where(['email'=>$request->email, 'congres_id'=>Congress::latest('id')->first()->id])->first();

        if ($user) {
            return redirect()->back()->with('error', 'Email already exists');
        }

        $participant = Participant::create([
            'gender_id' => $request->gender,
            'civility_id' => $request->title,
            'fname' => $request->first_name,
            'lname' => $request->last_name,
            'phone' => $request->telephone,
            'type_participant' => 'accompagnant',
            'type_accompagning_id' => $request->accompagnant,
            'congres_id' => Congress::latest('id')->first()->id,
            'type_participant' => 'accompagning',
            'email' => $request->email,
            'user_id' => auth()->user()->id,
            'registration_id' => auth()->user()->user_id,
            'passeport_number' => $request->num_passeport
        ]);
/* type_accompagning_id */
        return redirect()->back()->with('success', 'success');
    }
}
