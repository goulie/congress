@component('mail::message')
@if($lang === 'fr')
# Lettre d'Invitation

Bonjour,

Vous trouverez ci-joint votre lettre d'invitation pour l'événement : **{{ $event }}**.

Nous sommes ravis de vous compter parmi nous et nous réjouissons de votre participation.

Pour toute information complémentaire, n'hésitez pas à nous contacter.

Cordialement,  
L'équipe d'organisation

@else
# Invitation Letter

Hello,

Please find attached your invitation letter for the event: **{{ $event }}**.

We are delighted to have you with us and look forward to your participation.

If you need any additional information, please do not hesitate to contact us.

Best regards,  
The organizing team
@endif

@endcomponent