@php
$isStudent = $participant->ywp_or_student !== null && $participant->isYwpOrStudent == false;
$locale = $participant->langue == 'fr';
$paid = $invoice->status === App\Models\Invoice::PAYMENT_STATUS_PAID;
$periode = App\Models\Periode::PeriodeActive(App\Models\Congress::latest()->first()->id);
$end = \Carbon\Carbon::parse($periode->end_date);
$dateFormattedEnd = $locale ? $end->translatedFormat('d F Y') : $end->translatedFormat('F d, Y');
@endphp

@component('mail::message')
{{-- =====================================================
TITRE DU MAIL
===================================================== --}}
@if ($paid)
# {{ $locale ? 'Votre facture a été réglée' : 'Your invoice has been paid' }}
@else
# {{ $locale ? 'Votre facture est disponible' : 'Your invoice is available' }}
@endif

{{-- =====================================================
INTRODUCTION
===================================================== --}}
@if ($locale)
Bonjour {{ $participant->fname }} {{ $participant->lname }},

Merci pour votre inscription au **{{ $participant->congres->translate('fr', 'fallbackLocale')->title }}**.
Votre facture vient d’être générée et est disponible en pièce jointe.
@else
Hello {{ $participant->fname }} {{ $participant->lname }},

Thank you for registering to the **{{ $participant->congres->translate('en', 'fallbackLocale')->title }}**.
Your invoice has just been generated and is available as an attachment.
@endif



@component('mail::panel')
<table width="100%" cellspacing="0" cellpadding="6" style="font-size:14px;">
<tr>
<th align='left'>{{ $locale ? 'Désignation' : 'Description' }}</th>
<th align='right'>{{ $locale ? 'Montant' : 'Amount' }}</th>
</tr>

@foreach ($invoice->items as $item)
<tr>
<td>{{ $item->description_fr }}</td>
<td align='right'>
@if ($item->currency == 'EUR')
{{ number_format($item->price, 0, ',', ' ') }} €
@elseif (in_array($item->currency, ['USD', 'US']))
${{ number_format($item->price, 0, ',', ' ') }}
@else
{{ number_format($item->price, 0, ',', ' ') }} {{ $item->currency }}
@endif
</td>
</tr>
@endforeach
<tr>
<td><strong>{{ $locale ? 'Total' : 'Total Amount' }}</strong></td>
<td align='right'><strong>
@if ($invoice->currency == 'EUR')
{{ number_format($invoice->total_amount, 0, ',', ' ') }} €
@elseif (in_array($invoice->currency, ['USD', 'US']))
${{ number_format($invoice->total_amount, 0, ',', ' ') }}
@else
{{ number_format($invoice->total_amount, 0, ',', ' ') }} {{ $invoice->currency }}
@endif
</strong>
</td>
</tr>
</table>
@endcomponent


@component('mail::button', ['url' => route('participant.recap', $participant->uuid)])
@if ($locale)
Voir ma facture en ligne
@else
View my invoice online
@endif
@endcomponent


@if (!$paid)
@if ($locale)
### Date limite de paiement : **{{ $dateFormattedEnd }}**

Merci de régler cette facture **avant la date limite indiquée**.
Passé cette date, **les frais du package en cours pendant cette période seront automatiquement appliqués**,
et remplaceront les montants affichés dans cette facture.

Veuillez effectuer le paiement à temps pour éviter tout surcoût.
@else
### Payment deadline: **{{ $dateFormattedEnd }}**

Please make sure to pay this invoice **before the indicated deadline**.
After this date, **current package fees applicable during this period will automatically apply**,
replacing the amounts currently listed on this invoice.

Please make your payment on time to avoid additional charges.
@endif
@endif


@if ($paid)
@if ($locale)
### Paiement confirmé

Nous confirmons que cette facture a été **entièrement réglée**.
Aucune action supplémentaire n'est requise.
@else
### Payment confirmed

We confirm that this invoice has been **fully paid**.
No further action is required.
@endif
@endif

@if ($locale)
Si vous avez des questions, n'hésitez pas à nous contacter.

Cordialement,
**L’équipe AAE**
@else
If you have any questions, feel free to contact us.

Best regards,
**AfWASA Team**
@endif
@endcomponent
