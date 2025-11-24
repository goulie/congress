<div class="box-body">
    <!-- En-tête de confirmation -->
    <div class="alert alert-success text-center">
        <h2 style="margin-top: 0;">
            <i class="bi bi-check-circle"></i>
            @if (app()->getLocale() == 'fr')
                Inscription terminée avec succès !
            @else
                Registration completed successfully!
            @endif
        </h2>
        {{-- <p style="margin-bottom: 0;">
            @if (app()->getLocale() == 'fr')
                Un email de confirmation a été envoyé à votre adresse.
            @else
                A confirmation email has been sent to your address.
            @endif
        </p> --}}
    </div>

    @php
        $invoice = $participant->invoices()->latest()->first();
    @endphp

    <div class="row">
        <!-- Colonne de gauche - Informations personnelles -->
        <div class="col-md-6">
            <!-- Informations personnelles -->
            <div class="panel panel-default">
                <div class="panel-heading" style="background: #2c80ff; color: white; cursor: pointer;"
                    data-toggle="collapse" data-target="#personalInfoCollapse">
                    <h4 class="panel-title">
                        <i class="bi bi-person"></i>
                        @if (app()->getLocale() == 'fr')
                            Informations Personnelles
                        @else
                            Personal Information
                        @endif
                        <i class="bi bi-chevron-down pull-right"></i>
                    </h4>
                </div>
                <div id="personalInfoCollapse" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <table class="table table-condensed" style="margin-bottom: 0;">
                            <tr>
                                <td style="width: 40%; font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Civilité
                                    @else
                                        Title
                                    @endif
                                </td>
                                <td>{{ $participant->civility->libelle ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Nom complet
                                    @else
                                        Full Name
                                    @endif
                                </td>
                                <td>{{ $participant->fname }} {{ $participant->lname }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Genre
                                    @else
                                        Gender
                                    @endif
                                </td>
                                <td>{{ $participant->gender->libelle ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Niveau d'étude
                                    @else
                                        Education Level
                                    @endif
                                </td>
                                <td>{{ $participant->studentLevel->libelle ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Nationalité
                                    @else
                                        Nationality
                                    @endif
                                </td>
                                <td>
                                    {{ app()->getLocale() == 'fr'
                                        ? $participant->nationality->libelle_fr ?? 'N/A'
                                        : $participant->nationality->libelle_en ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Tranche d'âge
                                    @else
                                        Age Range
                                    @endif
                                </td>
                                <td>{{ $participant->ageRange->libelle ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Informations de contact -->
            <div class="panel panel-default">
                <div class="panel-heading" style="background: #2c80ff; color: white; cursor: pointer;"
                    data-toggle="collapse" data-target="#contactInfoCollapse">
                    <h4 class="panel-title">
                        <i class="bi bi-telephone"></i>
                        @if (app()->getLocale() == 'fr')
                            Informations de Contact
                        @else
                            Contact Information
                        @endif
                        <i class="bi bi-chevron-down pull-right"></i>
                    </h4>
                </div>
                <div id="contactInfoCollapse" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <table class="table table-condensed" style="margin-bottom: 0;">
                            <tr>
                                <td style="width: 40%; font-weight: bold;">Email</td>
                                <td>{{ $participant->email }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Téléphone
                                    @else
                                        Phone
                                    @endif
                                </td>
                                <td>{{ $participant->phone }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Organisation
                                    @else
                                        Organization
                                    @endif
                                </td>
                                <td>{{ $participant->organisation }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Type d'organisation
                                    @else
                                        Organization Type
                                    @endif
                                </td>
                                <td>
                                    @if ($participant->organisation_type_id == 15)
                                        {{ $participant->organisationType->translate(app()->getLocale(), 'fallbackLocale')->libelle . ' - ' . $participant->organisation_type_other }}
                                    @else
                                        {{ $participant->organisationType->translate(app()->getLocale(), 'fallbackLocale')->libelle ?? 'N/A' }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Fonction
                                    @else
                                        Position
                                    @endif
                                </td>
                                <td>{{ $participant->job }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Pays de travail
                                    @else
                                        Job Country
                                    @endif
                                </td>
                                <td>
                                    {{ app()->getLocale() == 'fr'
                                        ? $participant->jobCountry->libelle_fr ?? 'N/A'
                                        : $participant->jobCountry->libelle_en ?? 'N/A' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne de droite - Informations du congrès et facturation -->
        <div class="col-md-6">
            <!-- Informations du congrès -->
            <div class="panel panel-default">
                <div class="panel-heading" style="background: #2c80ff; color: white; cursor: pointer;"
                    data-toggle="collapse" data-target="#congressInfoCollapse">
                    <h4 class="panel-title">
                        <i class="bi bi-calendar-event"></i>
                        @if (app()->getLocale() == 'fr')
                            Informations du Congrès
                        @else
                            Congress Information
                        @endif
                        <i class="bi bi-chevron-down pull-right"></i>
                    </h4>
                </div>
                <div id="congressInfoCollapse" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <table class="table table-condensed" style="margin-bottom: 0;">
                            <tr>
                                <td style="width: 40%; font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Catégorie
                                    @else
                                        Category
                                    @endif

                                </td>
                                <td>{{ $participant->participantCategory->translate(app()->getLocale(), 'fallbackLocale')->libelle ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Adhésion
                                    @else
                                        Membership
                                    @endif
                                </td>
                                <td>
                                    @if ($participant->membre_aae == 'oui')
                                        <span class="label label-success">
                                            @if (app()->getLocale() == 'fr')
                                                Oui
                                            @else
                                                Yes
                                            @endif
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            @if (app()->getLocale() == 'fr')
                                                Non
                                            @else
                                                No
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if ($participant->membre_aae == 'oui' && $participant->membership_code)
                                <tr>
                                    <td style="font-weight: bold;">
                                        @if (app()->getLocale() == 'fr')
                                            Code membre
                                        @else
                                            Membership Code
                                        @endif
                                    </td>
                                    <td>{{ $participant->membership_code }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Dîner de gala
                                    @else
                                        Gala Dinner
                                    @endif
                                </td>
                                <td>
                                    @if ($participant->diner == 'oui')
                                        <span class="label label-success">
                                            @if (app()->getLocale() == 'fr')
                                                Oui
                                            @else
                                                Yes
                                            @endif
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            @if (app()->getLocale() == 'fr')
                                                Non
                                            @else
                                                No
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Visite technique
                                    @else
                                        Technical Visit
                                    @endif
                                </td>
                                <td>
                                    @if ($participant->visite == 'oui')
                                        <span class="label label-success">
                                            @if (app()->getLocale() == 'fr')
                                                Oui
                                            @else
                                                Yes
                                            @endif
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            @if (app()->getLocale() == 'fr')
                                                Non
                                            @else
                                                No
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if ($participant->visite == 'oui' && $participant->siteVisite)
                                <tr>
                                    <td style="font-weight: bold;">
                                        @if (app()->getLocale() == 'fr')
                                            Site de visite
                                        @else
                                            Visit Site
                                        @endif
                                    </td>
                                    <td>{{ $participant->siteVisite->libelle }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="font-weight: bold;">
                                    @if (app()->getLocale() == 'fr')
                                        Lettre d'invitation
                                    @else
                                        Invitation Letter
                                    @endif
                                </td>
                                <td>
                                    @if ($participant->invitation_letter == 'oui')
                                        <span class="label label-success">
                                            @if (app()->getLocale() == 'fr')
                                                Oui
                                            @else
                                                Yes
                                            @endif
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            @if (app()->getLocale() == 'fr')
                                                Non
                                            @else
                                                No
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if ($participant->participant_category_id == 1 && $participant->pass_deleguate == 'oui')
                                <tr>
                                    <td style="font-weight: bold;">
                                        @if (app()->getLocale() == 'fr')
                                            Pass délégué
                                        @else
                                            Delegate Pass
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($participant->deleguate_day))
                                            @php
                                                $days = json_decode($participant->deleguate_day);
                                                $passDays = \App\Models\JourPassDelegue::whereIn('id', $days)->get();
                                            @endphp
                                            {{ $passDays->count() }}
                                            @if (app()->getLocale() == 'fr')
                                                jour(s) sélectionné(s)
                                            @else
                                                day(s) selected
                                            @endif
                                        @else
                                            @if (app()->getLocale() == 'fr')
                                                Oui
                                            @else
                                                Yes
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif de facturation -->
            <div class="panel panel-primary">
                <div class="panel-heading" style="cursor: pointer;background: #2c80ff; color: white;"
                    data-toggle="collapse" data-target="#billingCollapse">
                    <h4 class="panel-title">
                        <i class="bi bi-receipt"></i>
                        @if (app()->getLocale() == 'fr')
                            Récapitulatif de Facturation
                        @else
                            Billing Summary
                        @endif
                        <i class="bi bi-chevron-down pull-right"></i>
                    </h4>
                </div>
                <div id="billingCollapse" class="panel-collapse collapse in">
                    <div class="panel-body text-center">
                        @if ($invoice)
                            <div style="margin-bottom: 20px;">
                                <h3 style="color: #2c80ff; font-weight: bold; margin: 10px 0;">
                                    {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
                                </h3>
                                <p style="margin: 5px 0;">
                                    <strong>
                                        @if (app()->getLocale() == 'fr')
                                            Facture N°:
                                        @else
                                            Invoice No:
                                        @endif
                                    </strong> {{ $invoice->invoice_number }}
                                </p>
                                <p style="margin: 5px 0;">
                                    <strong>
                                        @if (app()->getLocale() == 'fr')
                                            Statut:
                                        @else
                                            Status:
                                        @endif
                                    </strong>
                                    <span
                                        class="label {{ $invoice->status == 'paid' ? 'label-success' : 'label-warning' }}">
                                        {{ $invoice->status }}
                                    </span>
                                </p>
                            </div>

                            <!-- Détails de la facture -->
                            @if ($invoice->items->count() > 0)
                                <div style="text-align: left; margin-top: 15px;">
                                    <h5 style="color: #555; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                        @if (app()->getLocale() == 'fr')
                                            Détails de la facture
                                        @else
                                            Invoice Details
                                        @endif
                                    </h5>
                                    @foreach ($invoice->items as $item)
                                        <div
                                            style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f5f5f5;">
                                            <span>{{ app()->getLocale() == 'fr' ? $item->description_fr : $item->description_en }}</span>
                                            <span style="font-weight: bold;">{{ number_format($item->price, 2) }}
                                                {{ $invoice->currency }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <p class="text-muted">
                                @if (app()->getLocale() == 'fr')
                                    Aucune facture générée
                                @else
                                    No invoice generated
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fichiers téléchargés -->
    @if ($participant->passeport_pdf || $participant->student_card || $participant->student_letter)
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background: #393a3a; cursor: pointer;" data-toggle="collapse"
                        data-target="#filesCollapse">
                        <h4 class="panel-title">
                            <i class="bi bi-files"></i>
                            @if (app()->getLocale() == 'fr')
                                Fichiers Téléchargés
                            @else
                                Uploaded Files
                            @endif
                            <i class="bi bi-chevron-down pull-right"></i>
                        </h4>
                    </div>
                    <div id="filesCollapse" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="row">
                                @if ($participant->passeport_pdf)
                                    <div class="col-md-4">
                                        <a href="{{ Voyager::image($participant->passeport_pdf) }}"
                                            class="btn btn-default btn-block" download>
                                            <i class="bi bi-download"></i>
                                            @if (app()->getLocale() == 'fr')
                                                Passeport
                                            @else
                                                Passport
                                            @endif
                                        </a>
                                    </div>
                                @endif
                                @if ($participant->student_card)
                                    <div class="col-md-4">
                                        <a href="{{ Voyager::image($participant->student_card) }}" target="_blank"
                                            class="btn btn-default btn-block" download>
                                            <i class="bi bi-download"></i>
                                            @if (app()->getLocale() == 'fr')
                                                Carte étudiante
                                            @else
                                                Student Card
                                            @endif
                                        </a>
                                    </div>
                                @endif
                                @if ($participant->student_letter)
                                    <div class="col-md-4">
                                        <a href="{{ Voyager::image($participant->student_letter) }}" target="_blank"
                                            class="btn btn-default btn-block" download>
                                            <i class="bi bi-download"></i>
                                            @if (app()->getLocale() == 'fr')
                                                Lettre d'attestation
                                            @else
                                                Attestation Letter
                                            @endif
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Options de paiement -->
    <div class="row">
        <div class="col-md-12">
            @if (
                $participant->participant_category_id == 4 &&
                    !auth()->user()->isAdmin() &&
                    $participant->isYwpOrStudent == false)
                <div class="panel panel-default">
                    <div class="panel-heading" style="background: #28a745; color: white; cursor: pointer;"
                        data-toggle="collapse" data-target="#paymentCollapse">
                        <h4 class="panel-title">
                            <i class="bi bi-credit-card"></i>
                            @if (app()->getLocale() == 'fr')
                                Options de Paiement
                            @else
                                Payment Options
                            @endif
                            <i class="bi bi-chevron-down pull-right"></i>
                        </h4>
                    </div>
                    <div id="paymentCollapse" class="panel-collapse collapse in">
                        <div class="panel-body text-center">
                            <div class="row">

                                <div class="col-md-12">
                                    <p class="text-danger">
                                        {{ app()->getLocale() == 'fr'
                                            ? 'Votre facture sera accessible une fois votre inscription validée. '
                                            : 'Your invoice will be accessible once your registration is validated.' }}
                                    </p>
                                    <a href="/get_register/admin" class="btn btn-warning btn-lg btn-block"
                                        style="padding: 15px; margin-bottom: 10px;">
                                        <i class="bi bi-clock"></i><br>
                                        @if (app()->getLocale() == 'fr')
                                            Quitter le formulaire
                                        @else
                                            Exit Form
                                        @endif
                                    </a>


                                </div>



                            </div>
                        </div>
                    </div>
                </div>
        </div>
    @else
        <div class="panel panel-default">
            <div class="panel-heading" style="background: #28a745; color: white; cursor: pointer;"
                data-toggle="collapse" data-target="#paymentCollapse">
                <h4 class="panel-title">
                    <i class="bi bi-credit-card"></i>
                    @if (app()->getLocale() == 'fr')
                        Options de Paiement
                    @else
                        Payment Options
                    @endif
                    <i class="bi bi-chevron-down pull-right"></i>
                </h4>
            </div>
            <div id="paymentCollapse" class="panel-collapse collapse in">
                <div class="panel-body text-center">
                    <div class="row">
                        <div class="col-md-4">

                            <form action="#" method="POST" style="display: inline-block; width: 100%;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg btn-block"
                                    style="padding: 15px; margin-bottom: 10px;">
                                    <i class="bi bi-credit-card"></i><br>
                                    @if (app()->getLocale() == 'fr')
                                        Payer Maintenant
                                    @else
                                        Pay Now
                                    @endif
                                </button>
                            </form>

                            <small class="text-muted">
                                @if (app()->getLocale() == 'fr')
                                    Paiement sécurisé en ligne
                                @else
                                    Secure online payment
                                @endif
                            </small>



                        </div>
                        <div class="col-md-4">

                            <a href="/get_register/admin" class="btn btn-warning btn-lg btn-block"
                                style="padding: 15px; margin-bottom: 10px;">
                                <i class="bi bi-clock"></i><br>
                                @if (app()->getLocale() == 'fr')
                                    Payer Plus Tard
                                @else
                                    Pay Later
                                @endif
                            </a>

                            {{--  <small class="text-muted">
                                    @if (app()->getLocale() == 'fr')
                                        Vous recevrez un rappel
                                    @else
                                        You will receive a reminder
                                    @endif
                                </small> --}}
                        </div>
                        <div class="col-md-4">
                            @if ($invoice)
                                <a href="{{ route('invoices.download.participant', $participant->id) }}"
                                    class="btn btn-info btn-lg btn-block" style="padding: 15px; margin-bottom: 10px;">
                                    <i class="bi bi-download"></i><br>
                                    @if (app()->getLocale() == 'fr')
                                        Télécharger Facture
                                    @else
                                        Download Invoice
                                    @endif
                                </a>
                            @else
                                <button class="btn btn-info btn-lg btn-block"
                                    style="padding: 15px; margin-bottom: 10px;" disabled>
                                    <i class="bi bi-download"></i><br>
                                    @if (app()->getLocale() == 'fr')
                                        Télécharger Facture
                                    @else
                                        Download Invoice
                                    @endif
                                </button>
                            @endif
                            <small class="text-muted">
                                @if (app()->getLocale() == 'fr')
                                    PDF de votre facture
                                @else
                                    PDF of your invoice
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Actions supplémentaires -->
<div class="row">
    <div class="col-md-12 text-center">
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            @if ($participant->type_participant == 'individual')

                <a href="{{ route('form.edit.step') }}" class="btn btn-default">
                    <i class="bi bi-pencil"></i>
                    @if (app()->getLocale() == 'fr')
                        Modifier l'inscription
                    @else
                        Edit Registration
                    @endif
                </a>
            @else
                <a href="{{ route('participant.edit', $participant->uuid) }}" class="btn btn-default">
                    <i class="bi bi-pencil"></i>
                    @if (app()->getLocale() == 'fr')
                        Modifier l'inscription
                    @else
                        Edit Registration
                    @endif
                </a>
            @endif

            {{-- <a href="{{ url('/') }}" class="btn btn-default">
                    <i class="bi bi-house"></i>
                    @if (app()->getLocale() == 'fr')
                        Retour à l'accueil
                    @else
                        Return to Home
                    @endif
                </a> --}}
            <button onclick="window.print()" class="btn btn-default">
                <i class="bi bi-printer"></i>
                @if (app()->getLocale() == 'fr')
                    Imprimer
                @else
                    Print
                @endif
            </button>
        </div>
    </div>
</div>
</div>
@section('javascript')
    <script>
        function redirectToForm() {
            window.location.href = "{{ route('form.edit.step') }}";
        }
    </script>
@endsection
