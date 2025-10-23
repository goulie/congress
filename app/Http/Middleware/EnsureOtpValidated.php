<?php

namespace App\Http\Middleware;

use App\Services\OtpMailService;
use Closure;
use Illuminate\Http\Request;
use App\Traits\GeneratesOtp;
use App\Traits\ValidOtp;

class EnsureOtpValidated
{
    use ValidOtp;

    protected $otpMailService;
   /*  public function __construct(OtpMailService $otpMailService)
    {
        $this->otpMailService = $otpMailService;
    } */
    public function handle(Request $request, Closure $next)
    {
        /* // Récupération de l'email depuis l'utilisateur connecté
        $email = $request->user()->email ?? $request->input('email');

        if (!$email) {
            // Pas d'email → redirection vers le formulaire OTP
            return redirect()->route('otp.verify.form')
                ->with('error', __('Veuillez fournir votre email pour vérifier l’OTP.'));
        }

        // Vérification de la présence d'un OTP valide
        if (!$this->hasValidOtp($email)) {
            return redirect()->route('otp.verify.form')
                ->with('error', __('Vous devez valider votre OTP avant de continuer.'));
        } */

        // OTP valide → continuer la requête
        return $next($request);
    }
}
