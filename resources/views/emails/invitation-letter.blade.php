@component('mail::message')
@if($lang === 'fr')
# Lettre d'Invitation

Bonjour cher participant,

Vous trouverez ci-joint votre lettre d'invitation pour l'événement : **{{ $event }}**.

Nous sommes ravis de vous compter parmi nous et nous réjouissons de votre participation.

Pour toute information complémentaire, n'hésitez pas à nous contacter.

Code Hotel **ICE2026HEBERGEMENT** Ce code vous donne droit à une réduction de 10% sur vos frais d'hébergement.

Cordialement,  

L'équipe d'organisation

@else
# Invitation Letter

Hello dear participant,

Please find attached your invitation letter for the event: **{{ $event }}**.

We are delighted to have you with us and look forward to your participation.

If you need any additional information, please do not hesitate to contact us.


Code Hotel **ICE2026HEBERGEMENT** This code gives you a discount of 10% on your accommodation fees.

Best regards,  
The organizing team
@endif

@endcomponent