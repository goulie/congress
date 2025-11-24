<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Http;


class ReCaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function passes($attribute, $value)

    {

        $response = Http::get("https://www.google.com/recaptcha/api/siteverify", [

            'secret' => env('GOOGLE_RECAPTCHA_SECRET'),

            'response' => $value

        ]);



        return $response->json()["success"];
    }

    /**

     * Get the validation error message.

     *

     * @return string

     */

    public function message()

    {

        return app()->getLocale() == 'fr' ? 'Le google recaptcha est obligatoire.' : 'The google recaptcha is required.';
    }
}
