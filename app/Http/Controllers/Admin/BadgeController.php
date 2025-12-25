<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BadgeController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    public function index(Request $request)
    {
        $congress = Congress::latest()->first();
        $badges = Participant::with('badge_color')->where('congres_id', $congress->id)
            ->where('diner', '<>', '')
            ->where('visite', '<>', '')
            ->where('user_id', Auth::user()->id)
            ->get();

        if (Auth::user()->isAdmin() || Auth::user()->isFinance() || Auth::user()->isSecretary()) {
            $badges = Participant::with('badge_color')->where('congres_id', $congress->id)
                ->whereNotNull('email')->get();
            /* 
                ->where('diner', '<>', '')
                ->where('visite', '<>', '') 
                */
        }

        return view('voyager::view-badges.browse', compact('badges'));
    }

    public function view($id)
    {
        $participants = Participant::get();
        //return view('voyager::view-badges.partials.badge',compact('participant'));
        return view('voyager::view-badges.partials.pdfbadge', compact('participants'));
    }

    public function ajaxUpdate(Request $request)
    {
        $participant = Participant::find($request->participant_id);

        try {
            $participant->updateQuietly([
                'badge_full_name' => $request->badge_full_name,
                'organisation' => $request->organisation,
                'role_badge_congres' => $request->role_badge_congres,
                'badge_color_id' => $request->badge_color_id,
                'nationality_id' => $request->nationality_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Badge mis à jour avec succès'
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du badge'
            ]);
        }
    }

    public function printSelected(Request $request)
    {
        $request->validate([
            'badge_ids' => 'required|array',
            'badge_ids.*' => 'exists:participants,id',
        ]);

        $participants = Participant::whereIn('id', $request->badge_ids)->get();

        return view('voyager::view-badges.partials.pdfbadge', compact('participants'));
    }
}
