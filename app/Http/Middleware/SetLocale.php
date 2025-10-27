<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Si la langue est passée dans l'URL (ex: ?lang=fr)
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        }

        //Si l’utilisateur est connecté et a une préférence
        elseif (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
            Session::put('locale', $locale);
        }

        // Sinon, on récupère celle de la session
        $locale = Session::get('locale', config('app.locale'));

        // On applique la langue
        App::setLocale($locale);

        return $next($request);
    }
}
