<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Imports\ParticipantsImport;
use App\Models\Participant;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GroupeRegistrant extends Controller
{
    public function storeMultiple(Request $request)
    {
        $participants = $request->input('participants', []);

        foreach ($participants as $data) {
            Participant::create([
                'civility_id' => $data['title'],
                'fname' => $data['first_name'],
                'lname' => $data['last_name'],
                'phone' => $data['telephone'],
                'organisation' => $data['organisation'],
                'job' => $data['fonction'],
                'participant_category_id' => $data['category'],
                'type_member_id' => $data['membership'],
                'passeport_number' => $data['num_passeport'],
            ]);
        }

        return redirect()->back()->with('success', 'Participants enregistrés avec succès !');
    }
}
