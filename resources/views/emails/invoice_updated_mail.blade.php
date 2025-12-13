@php
    $isFr = ($locale === 'fr');
@endphp

@component('mail::message')

{{-- TITRE --}}
# {{ $isFr ? 'Votre facture a été mise à jour' : 'Your invoice has been updated' }}

{{-- MESSAGE PRINCIPAL --}}
@if($isFr)
Bonjour {{ $participant->fname }} {{ $participant->lname }},

Votre précédente facture est arrivée à expiration selon la période tarifaire en vigueur.  
Une **nouvelle facture mise à jour** a été générée automatiquement avec les tarifs actuels.

Veuillez trouver ci-joint votre nouvelle facture au format PDF.

@else

Hello {{ $participant->fname }} {{ $participant->lname }},

Your previous invoice has expired according to the current pricing period.  
A **new updated invoice** has been automatically generated with the current rates.

Please find your new invoice attached as a PDF file.

@endif

{{-- BOUTON LIEN FACTURE --}}
@component('mail::button', ['url' => route('participant.recap', $participant->uuid)])
{{ $isFr ? 'Voir la facture' : 'View Invoice' }}
@endcomponent

{{-- PIED DE PAGE --}}
@if($isFr)
Merci pour votre confiance,<br>
{{ config('app.name') }}
@else
Thank you for your trust,<br>
{{ config('app.name') }}
@endif

@endcomponent
