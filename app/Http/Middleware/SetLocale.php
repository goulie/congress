<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Si la langue est passée dans l'URL (ex: ?lang=fr)
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        }
        // Si la langue est déjà en session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Si l'utilisateur est connecté et a une préférence
        elseif (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
            Session::put('locale', $locale);
        }
        // Sinon, on utilise la langue du navigateur
        else {
            $locale = $this->getBrowserLocale($request);
            Session::put('locale', $locale);
        }

        // On applique la langue
        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Détecte la langue du navigateur
     */
    private function getBrowserLocale($request)
    {
        $acceptableLanguages = $request->getLanguages();
        $supportedLocales = ['fr', 'en']; // Langues supportées par votre application

        if (!empty($acceptableLanguages)) {
            foreach ($acceptableLanguages as $language) {
                // Extraire le code de langue principal (ex: 'fr' depuis 'fr-FR')
                $primaryLanguage = strtolower(explode('-', $language)[0]);

                if (in_array($primaryLanguage, $supportedLocales)) {
                    return $primaryLanguage;
                }
            }
        }

        // Langue par défaut si aucune correspondance
        return config('app.locale', 'fr');
    }
}
