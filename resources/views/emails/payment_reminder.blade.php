@php
    use Carbon\Carbon;

    $today = Carbon::now();
    $deadline = Carbon::parse($invoice->deadline ?? now()->endOfYear());
    $daysRemaining = $today->diffInDays($deadline, false);
@endphp

<x-mail::message>

@if ($locale === 'fr')
# 🔔 Rappel important – Paiement de votre participation au {{ $invoice->congres->translate($locale, 'fallbackLocale')->title }}

Bonjour **{{ $participant->fname }} {{ $participant->lname }}**,

Nous vous rappelons que **la facture relative à votre participation au congrès n’a pas encore été réglée**.

### 🧾 Détails de la facture
- **Numéro :** {{ $invoice->invoice_number }}
- **Montant :** {{ number_format($invoice->total_amount, 0, ',', ' ') }} {{ $invoice->currency }}
- **Statut :** En attente de paiement
- **Date limite :** **{{ $deadline->format('d/m/Y') }}**

@if ($daysRemaining > 0)
⏳ **Il vous reste {{ $daysRemaining }} jour{{ $daysRemaining > 1 ? 's' : '' }} pour effectuer le paiement.**
@else
⚠️ **La date limite est dépassée. Les nouveaux tarifs sont désormais applicables.**
@endif

---

### ⚠️ Informations importantes
- Le **paiement par carte Visa est temporairement indisponible**.
- Le **paiement par virement bancaire est toujours possible**, en utilisant **les coordonnées figurant sur la facture**.
- **Après le 31 décembre**, les **nouveaux tarifs en vigueur s’appliqueront automatiquement**.

<x-mail::button :url="route('invoices.download.participant', $participant->id)">
📄 Télécharger la facture
</x-mail::button>

Si vous avez déjà effectué le paiement par virement, merci d’ignorer ce message.

Cordialement,  
**L’équipe organisatrice du congrès**

@else
# 🔔 Important Reminder – Registration Payment for the {{ $invoice->congres->translate($locale, 'fallbackLocale')->title }}

Dear **{{ $participant->fname }} {{ $participant->lname }}**,

This is a reminder that **your invoice related to your congress registration has not yet been paid**.

### 🧾 Invoice details
- **Invoice number:** {{ $invoice->invoice_number }}
- **Amount:** {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
- **Payment deadline:** **{{ $deadline->format('F d, Y') }}**

@if ($daysRemaining > 0)
⏳ **You have {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }} remaining to complete the payment.**
@else
⚠️ **The payment deadline has passed. Updated pricing now applies.**
@endif

---

### ⚠️ Important information
- **Visa card payments are temporarily unavailable**.
- **Bank transfer payments remain available** using **the bank details provided on the invoice**.
- **After December 31**, **updated pricing will automatically apply**.

<x-mail::button :url="route('invoices.download.participant', $participant->id)">
📄 Download invoice
</x-mail::button>

If you have already completed the payment by bank transfer, please disregard this message.

Best regards,  
**The Congress Organizing Team**
@endif

</x-mail::message>
