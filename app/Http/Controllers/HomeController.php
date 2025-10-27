<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function switch($lang)
    {
        // Vérifie que la langue est supportée
        $available = ['en', 'fr'];
        if (!in_array($lang, $available)) {
            abort(400, 'Langue non supportée');
        }

        // Enregistre la langue dans la session
        Session::put('locale', $lang);

        // Change la langue immédiatement
        App::setLocale($lang);

        // Redirige vers la page précédente
        return redirect()->back();
    }
}
