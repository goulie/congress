@php
    use App\Models\JourPassDelegue;
    use Carbon\Carbon;
@endphp

@if($locale === 'fr')
<x-mail::message>
@if ($participant->ywp_or_student)
# 🎉 Inscription en attente de validation !
@else
# 🎉 Confirmation d'Inscription Réussie !
@endif


Cher/Chère **{{ $participant->fname }} {{ $participant->lname }}**,

@if ($participant->ywp_or_student)
Nous avons le plaisir de vous informer que votre inscription aux **{{ $participant->congres->translate($locale,'fallbackLocale')->title ?? config('app.name') }}**  est en attente de validation par l'administrateur du site.
@else
Nous avons le plaisir de vous confirmer votre inscription au **{{ $participant->congres->translate($locale,'fallbackLocale')->title ?? config('app.name') }}**. {{ $participant->ywp_or_student ? 'Votre inscription est en attente de validation par l\'administrateur du site.' : 'Votre inscription a été enregistrée avec succès.' }}
@endif

 

## Détails de Votre Inscription

### Informations Personnelles
- **Nom complet** : {{ $participant->fname }} {{ $participant->lname }}
- **Email** : {{ $participant->email }}
- **Téléphone** : {{ $participant->phone ?? 'Non renseigné' }}
- **Organisation** : {{ $participant->organisation ?? 'Non renseignée' }}
- **Fonction** : {{ $participant->job ?? 'Non renseignée' }}

### Catégorie et Options
- **Catégorie** : {{ $participant->participantCategory->translate($locale,'fallbackLocale')->libelle ?? 'Non spécifiée' }}
- **Statut membre** : {{ $participant->membre_aae == 'oui' ? 'Membre' : 'Non-membre' }}
- **Dîner de gala** : {{ $participant->diner == 'oui' ? '✅ Oui' : '❌ Non' }}
<p style="color:red">
    {{ $participant->diner == 'oui' ? 'Votre place au diner gala ne sera garantie qu\'après le paiement de vos frais de participation.' : '' }}
</p>

- **Visite technique** : {{ $participant->visite == 'oui' ? '✅ Oui' : '❌ Non' }}
- **Pass journalier** : {{ $participant->pass_deleguate == 'oui' ? '✅ Oui' : '❌ Non' }}

@if($participant->pass_deleguate == 'oui' && !empty($participant->deleguate_day))
- **Dates de pass sélectionnées** : 
@php
    $dates = json_decode($participant->deleguate_day);
    if (is_array($dates) && count($dates) > 0) {
        foreach ($dates as $dateId) {
            $date = JourPassDelegue::find($dateId);
            if ($date) {
                echo "  - " . Carbon::parse($date->date)->translatedFormat('d F Y') . "\n";
            }
        }
    }
@endphp
@endif

### Informations de Facturation
- **Numéro d'inscription** : #{{ $participant->id }}
- **Date d'inscription** : {{ $participant->created_at->translatedFormat('d F Y à H:i') }}
- **Langue** : Français

## Prochaines Étapes

### 1. **Lettre d'Invitation**
@if($participant->invitation_letter == 'oui')
✅ Votre lettre d'invitation officielle vous sera transmise par email après paiement de vos frais de participation.
@else
❌ Vous n'avez pas demandé de lettre d'invitation. Contactez-nous si nécessaire si vous souhaitez en obtenir une .
@endif

### 2. **Paiement**
@if ($participant->ywp_or_student == 'ywp'|| $participant->ywp_or_student == 'student')
- Votre facture sera disponible après la validation de votre dossier d'inscription.  
- Vous recevrez une notification par email dès qu’elle sera prête.
@else
- Veuillez consulter votre espace personnel, dans le menu **Factures**, pour accéder aux détails concernant le paiement de vos frais de participation.
@endif

## Support et Contact

**Notre équipe est à votre disposition pour toute question :**

- **Email** : event@afwasa.org
- **Horaires** : Lundi - Vendredi, 8h00 - 17h00


Nous sommes impatients de vous accueillir et vous remercions de votre confiance.

Cordialement,<br>
**L'Équipe d'Organisation**<br>
{{ $participant->congres->title ?? config('app.name') }}

---

<small style="color: #666;">
Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.<br>
Numéro de référence : #{{ $participant->id }}-{{ strtoupper(Str::random(6)) }}
</small>
</x-mail::message>

@else

<x-mail::message>

@if ($participant->ywp_or_student)
# 🎉 Registration pending approval !
@else
# 🎉 Registration Successfully Confirmed !
@endif
Dear **{{ $participant->fname }} {{ $participant->lname }}**,

We are pleased to confirm your registration for the **{{ $participant->congres->translate($participant->langue,'fallbackLocale')->title ?? config('app.name') }}**. {{ $participant->ywp_or_student ? 'Your registration is pending approval by the site administrator.' : 'Your registration has been successfully recorded.' }}

## Your Registration Details

### Personal Information
- **Full Name**: {{ $participant->fname }} {{ $participant->lname }}
- **Email**: {{ $participant->email }}
- **Phone**: {{ $participant->phone ?? 'Not provided' }}
- **Organization**: {{ $participant->organisation ?? 'Not provided' }}
- **Position**: {{ $participant->job ?? 'Not provided' }}

### Category and Options
- **Category**: {{ $participant->participantCategory->name ?? 'Not specified' }}
- **Member status**: {{ $participant->membre_aae == 'oui' ? 'Member' : 'Non-member' }}
- **Gala Dinner**: {{ $participant->diner == 'oui' ? '✅ Yes' : '❌ No' }}
<p style="color:red">
    {{ $participant->diner == 'oui' ? 'Your seat for the gala dinner will only be guaranteed after the payment of your participation fees.' : '' }}
</p>
- **Technical Visit**: {{ $participant->visite == 'oui' ? '✅ Yes' : '❌ No' }}
- **Daily Pass**: {{ $participant->pass_deleguate == 'oui' ? '✅ Yes' : '❌ No' }}

@if($participant->pass_deleguate == 'oui' && !empty($participant->deleguate_day))
- **Selected pass dates**:
@php
    $dates = json_decode($participant->deleguate_day);
    if (is_array($dates) && count($dates) > 0) {
        foreach ($dates as $dateId) {
            $date = JourPassDelegue::find($dateId);
            if ($date) {
                echo "  - " . Carbon::parse($date->date)->translatedFormat('F d, Y') . "\n";
            }
        }
    }
@endphp
@endif

### Billing Information
- **Registration Number**: #{{ $participant->id }}
- **Registration Date**: {{ $participant->created_at->translatedFormat('F d, Y at H:i') }}
- **Language**: English

## Next Steps

### 1. **Invitation Letter**
@if($participant->invitation_letter == 'oui')
✅ Your official invitation letter will be sent to you by email after payment of your participation fees.
@else
❌ You did not request an invitation letter. Contact us if you need one.
@endif

### 2. **Payment**
@if ($participant->ywp_or_student == 'ywp'|| $participant->ywp_or_student == 'student')
- Your invoice will be available after your registration file has been validated.  
- You will receive an email notification as soon as it is ready.
@else
- Please check your personal space, under the **Invoices** menu, to access the details regarding your participation fee payment.
@endif

## Support & Contact

**Our team is available to assist you with any questions:**

- **Email**: event@afwasa.org  
- **Working hours**: Monday - Friday, 8:00 AM - 5:00 PM

We look forward to welcoming you and thank you for your trust.

Best regards,<br>
**The Organizing Team**<br>
{{ $participant->congres->title ?? config('app.name') }}

---

<small style="color: #666;">
This email was sent automatically. Please do not reply directly.<br>
Reference Number: #{{ $participant->id }}-{{ strtoupper(Str::random(6)) }}
</small>
</x-mail::message>
@endif