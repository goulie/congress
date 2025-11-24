<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::user()->isAdmin() || Auth::user()->isFinance()) {
            $badges = Participant::with('badge_color')->where('congres_id', $congress->id)
            ->where('diner', '<>', '')
            ->where('visite', '<>', '')
            ->get();
        }

        return view('voyager::view-badges.browse', compact('badges'));
    }
}
