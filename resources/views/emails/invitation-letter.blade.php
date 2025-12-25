@component('mail::message')
@if($lang === 'fr')
# Lettre d'Invitation

Bonjour cher participant,

Vous trouverez ci-joint votre lettre d'invitation pour l'événement : **{{ $event }}**.

Nous sommes ravis de vous compter parmi nous et nous réjouissons de votre participation.

Pour toute information complémentaire, n'hésitez pas à nous contacter.

Le code **ICE-AfWASA 2026** vous donne droit à une réduction sur vos frais d'hébergement dans les hôtels partenaires mentionnés sur le site internet du congrès <a href="https://congress.afwasa.org/fr/accomodation/">https://congress.afwasa.org/fr/accomodation</a>.

Cordialement,  

L'équipe d'organisation

@else
# Invitation Letter

Hello dear participant,

Please find attached your invitation letter for the event: **{{ $event }}**.

We are delighted to have you with us and look forward to your participation.

If you need any additional information, please do not hesitate to contact us.

The code **ICE-AfWASA 2026** entitles you to a discount on your accommodation fees at the partner hotels listed on the congress website <a href="https://congress.afwasa.org/accomodation/">https://congress.afwasa.org/accomodation/</a>.

Best regards,  
The organizing team
@endif

@endcomponent