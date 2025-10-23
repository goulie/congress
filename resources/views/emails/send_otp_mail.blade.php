<x-mail::message>
@if ($lang == 'fr')
# Vérification de votre compte

Bonjour,

Votre code de vérification (OTP) est :

# <span style="font-size: 28px; font-weight: bold; color: #2d3748;">{{ $otp }}</span>

Ce code est valable pendant **15 minutes**.  
Veuillez ne pas le partager avec d'autres personnes.

{{-- <x-mail::button :url="config('app.url')">
Accéder à {{ config('app.name') }}
</x-mail::button>
 --}}
Merci,  
L’équipe **{{ config('app.name') }}**
@else
# Verify Your Account

Hello,

Your One-Time Password (OTP) is:

# <span style="font-size: 28px; font-weight: bold; color: #2d3748;">{{ $otp }}</span>

This code is valid for **15 minutes**.  
Please do not share it with anyone.

{{-- <x-mail::button :url="config('app.url')">
Go to {{ config('app.name') }}
</x-mail::button>
 --}}
Thank you,  
The **{{ config('app.name') }}** team
@endif
</x-mail::message>
