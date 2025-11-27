@php
    $congres = App\Models\Congress::latest('id')->first();
    $categories = App\Models\CategorieRegistrant::forCongress($congres->id);
    $dinner = App\Models\CategorieRegistrant::DinnerforCongress($congres->id);
    $accompanying = App\Models\CategorieRegistrant::accompanyingPersonForCongress($congres->id);
    $tours = App\Models\CategorieRegistrant::ToursforCongress($congres->id);
    $passDeleguate = App\Models\CategorieRegistrant::PassDeleguateforCongress($congres->id);
    $non_member = App\Models\CategorieRegistrant::NonMemberPriceforCongress($congres->id);
    $student_ywp = App\Models\CategorieRegistrant::studentForCongress($congres->id);
    $deleguate = App\Models\CategorieRegistrant::deleguateForCongress($congres->id);
@endphp

<!-- Navigation par onglets -->
<ul class="nav nav-tabs" id="registrationTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="participants-tab" data-toggle="tab" href="#participants" role="tab">
            <i class="bi bi-people"></i> {{ __('Participants') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="exhibitors-tab" data-toggle="tab" href="#exhibitors" role="tab">
            <i class="bi bi-shop"></i> {{ __('Exposants') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="sponsors-tab" data-toggle="tab" href="#sponsors" role="tab">
            <i class="bi bi-award"></i> {{ __('Sponsors') }}
        </a>
    </li>
</ul>

<div class="tab-content" id="registrationTabsContent">

    <!-- ONGLET PARTICIPANTS (votre formulaire actuel) -->
    

    <!-- ONGLET EXPOSANTS -->
    <div class="tab-pane fade" id="exhibitors" role="tabpanel">
        @if ($edit ??  false)
            <form class="ajax-form" method="POST" action="{{ route('exhibitor.update.group') }}"
                enctype="multipart/form-data">
                <input type="hidden" name="uuid" value="{{ $exhibitor->uuid ?? '' }}">
            @else
                <form class="ajax-form" method="POST" action="{{ route('exhibitors.store.group') }}"
                    enctype="multipart/form-data">
        @endif
        @csrf

        <div class="box-body">
            <div class="row">
                <!-- Informations de l'entreprise -->
                <div class="col-md-12">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-building"></i> {{ __('Informations de l\'entreprise') }}
                    </h4>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-building"></i> {{ __('Nom de l\'entreprise') }}
                    </label>
                    <input type="text" class="form-control" name="company_name"
                        value="{{ $exhibitor->company_name ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-globe"></i> {{ __('Pays') }}
                    </label>
                    <select class="form-control" name="country" required>
                        <option value="">{{ __('Choisir') }}</option>
                        @foreach (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                            <option value="{{ $country->id }}"
                                {{ isset($exhibitor) && $exhibitor->country_id == $country->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-envelope"></i> {{ __('Email professionnel') }}
                    </label>
                    <input type="email" class="form-control" name="company_email"
                        value="{{ $exhibitor->company_email ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-telephone"></i> {{ __('Téléphone') }}
                    </label>
                    <input type="tel" class="form-control" name="company_phone"
                        value="{{ $exhibitor->company_phone ?? '' }}" required>
                </div>

                <div class="col-md-12">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-link"></i> {{ __('Site web') }}
                    </label>
                    <input type="url" class="form-control" name="website" value="{{ $exhibitor->website ?? '' }}">
                </div>

                <div class="col-md-12">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-text-paragraph"></i> {{ __('Description de l\'entreprise') }}
                    </label>
                    <textarea class="form-control" name="company_description" rows="3">{{ $exhibitor->company_description ?? '' }}</textarea>
                </div>

                <!-- Stand et équipements -->
                <div class="col-md-12 mt-4">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-layout-wtf"></i> {{ __('Stand et équipements') }}
                    </h4>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-arrows-angle-expand"></i> {{ __('Type de stand') }}
                    </label>
                    <select class="form-control" name="stand_type" required>
                        <option value="">{{ __('Choisir') }}</option>
                        <option value="9m2" data-amount="500"
                            {{ isset($exhibitor) && $exhibitor->stand_type == '9m2' ? 'selected' : '' }}>
                            9m² - 500 {{ $congres->currency }}
                        </option>
                        <option value="12m2" data-amount="700"
                            {{ isset($exhibitor) && $exhibitor->stand_type == '12m2' ? 'selected' : '' }}>
                            12m² - 700 {{ $congres->currency }}
                        </option>
                        <option value="18m2" data-amount="1000"
                            {{ isset($exhibitor) && $exhibitor->stand_type == '18m2' ? 'selected' : '' }}>
                            18m² - 1000 {{ $congres->currency }}
                        </option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-plus-circle"></i> {{ __('Équipements supplémentaires') }}
                    </label>
                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input equipment-checkbox" type="checkbox" name="equipments[]"
                                value="table" data-amount="50"
                                {{ isset($exhibitor) && in_array('table', json_decode($exhibitor->equipments ?? '[]')) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                Table supplémentaire (+50 {{ $congres->currency }})
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input equipment-checkbox" type="checkbox" name="equipments[]"
                                value="chair" data-amount="20"
                                {{ isset($exhibitor) && in_array('chair', json_decode($exhibitor->equipments ?? '[]')) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                Chaises supplémentaires (+20 {{ $congres->currency }})
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input equipment-checkbox" type="checkbox" name="equipments[]"
                                value="electricity" data-amount="100"
                                {{ isset($exhibitor) && in_array('electricity', json_decode($exhibitor->equipments ?? '[]')) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                Accès électrique (+100 {{ $congres->currency }})
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Personne de contact -->
                <div class="col-md-12 mt-4">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-person-lines-fill"></i> {{ __('Personne de contact') }}
                    </h4>
                </div>

                <div class="col-md-4">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person-vcard"></i> {{ __('Civilité') }}
                    </label>
                    <select class="form-control" name="contact_title" required>
                        <option value="">{{ __('Choisir') }}</option>
                        @foreach (App\Models\Civility::get()->translate(app()->getLocale(), 'fallbackLocale') as $civility)
                            <option value="{{ $civility->id }}"
                                {{ isset($exhibitor) && $exhibitor->contact_title == $civility->id ? 'selected' : '' }}>
                                {{ $civility->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person"></i> {{ __('Prénom') }}
                    </label>
                    <input type="text" class="form-control" name="contact_first_name"
                        value="{{ $exhibitor->contact_first_name ?? '' }}" required>
                </div>

                <div class="col-md-4">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person"></i> {{ __('Nom') }}
                    </label>
                    <input type="text" class="form-control" name="contact_last_name"
                        value="{{ $exhibitor->contact_last_name ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-envelope"></i> {{ __('Email du contact') }}
                    </label>
                    <input type="email" class="form-control" name="contact_email"
                        value="{{ $exhibitor->contact_email ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-telephone"></i> {{ __('Téléphone du contact') }}
                    </label>
                    <input type="tel" class="form-control" name="contact_phone"
                        value="{{ $exhibitor->contact_phone ?? '' }}" required>
                </div>

                <div class="col-md-12">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person-badge"></i> {{ __('Fonction') }}
                    </label>
                    <input type="text" class="form-control" name="contact_position"
                        value="{{ $exhibitor->contact_position ?? '' }}" required>
                </div>

                <!-- Badges supplémentaires -->
                <div class="col-md-12 mt-4">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-person-badge"></i> {{ __('Badges supplémentaires') }}
                    </h4>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark">
                        {{ __('Nombre de badges supplémentaires') }}
                    </label>
                    <input type="number" class="form-control" name="additional_badges" min="0"
                        max="10" value="{{ $exhibitor->additional_badges ?? 0 }}" data-price="50">
                    <small class="text-muted">50 {{ $congres->currency }} par badge supplémentaire</small>
                </div>

                <!-- Logo -->
                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-image"></i> {{ __('Logo de l\'entreprise') }}
                    </label>
                    <input type="file" class="form-control" name="company_logo" accept="image/*">
                    @if (isset($exhibitor) && $exhibitor->company_logo)
                        <div class="mt-2">
                            <img src="{{ Voyager::image($exhibitor->company_logo) }}" alt="Logo"
                                style="max-height: 80px;">
                        </div>
                    @endif
                </div>

                <!-- Total -->
                <div class="col-md-12 mt-4">
                    <div class="alert alert-success text-center fs-5 fw-bold" id="exhibitor-total-box">
                        {{ __('Total à payer') }}: <span id="exhibitor-total-amount">0</span>
                        {{ $congres->currency }}
                    </div>
                </div>
            </div>
        </div>

        @include('voyager::forms.btn_save_continue')
        </form>
    </div>

    <!-- ONGLET SPONSORS -->
    <div class="tab-pane fade" id="sponsors" role="tabpanel">
        @if ($edit ??  false)
            <form class="ajax-form" method="POST" action="{{ route('sponsor.update.group') }}"
                enctype="multipart/form-data">
                <input type="hidden" name="uuid" value="{{ $sponsor->uuid ?? '' }}">
            @else
                <form class="ajax-form" method="POST" action="{{ route('sponsors.store.group') }}"
                    enctype="multipart/form-data">
        @endif
        @csrf

        <div class="box-body">
            <div class="row">
                <!-- Informations du sponsor -->
                <div class="col-md-12">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-award"></i> {{ __('Informations du sponsor') }}
                    </h4>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-building"></i> {{ __('Nom de l\'organisation') }}
                    </label>
                    <input type="text" class="form-control" name="organization_name"
                        value="{{ $sponsor->organization_name ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-trophy"></i> {{ __('Type de sponsorship') }}
                    </label>
                    <select class="form-control" name="sponsorship_type" id="sponsorship_type" required>
                        <option value="">{{ __('Choisir') }}</option>
                        <option value="platinum" data-amount="5000"
                            {{ isset($sponsor) && $sponsor->sponsorship_type == 'platinum' ? 'selected' : '' }}>
                            Platine - 5,000 {{ $congres->currency }}
                        </option>
                        <option value="gold" data-amount="3000"
                            {{ isset($sponsor) && $sponsor->sponsorship_type == 'gold' ? 'selected' : '' }}>
                            Or - 3,000 {{ $congres->currency }}
                        </option>
                        <option value="silver" data-amount="1500"
                            {{ isset($sponsor) && $sponsor->sponsorship_type == 'silver' ? 'selected' : '' }}>
                            Argent - 1,500 {{ $congres->currency }}
                        </option>
                        <option value="bronze" data-amount="800"
                            {{ isset($sponsor) && $sponsor->sponsorship_type == 'bronze' ? 'selected' : '' }}>
                            Bronze - 800 {{ $congres->currency }}
                        </option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-globe"></i> {{ __('Pays') }}
                    </label>
                    <select class="form-control" name="country" required>
                        <option value="">{{ __('Choisir') }}</option>
                        @foreach (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                            <option value="{{ $country->id }}"
                                {{ isset($sponsor) && $sponsor->country_id == $country->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-envelope"></i> {{ __('Email') }}
                    </label>
                    <input type="email" class="form-control" name="email" value="{{ $sponsor->email ?? '' }}"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-telephone"></i> {{ __('Téléphone') }}
                    </label>
                    <input type="tel" class="form-control" name="phone" value="{{ $sponsor->phone ?? '' }}"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-link"></i> {{ __('Site web') }}
                    </label>
                    <input type="url" class="form-control" name="website"
                        value="{{ $sponsor->website ?? '' }}">
                </div>

                <!-- Personne de contact -->
                <div class="col-md-12 mt-4">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-person-lines-fill"></i> {{ __('Personne de contact') }}
                    </h4>
                </div>

                <div class="col-md-4">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person-vcard"></i> {{ __('Civilité') }}
                    </label>
                    <select class="form-control" name="contact_title" required>
                        <option value="">{{ __('Choisir') }}</option>
                        @foreach (App\Models\Civility::get()->translate(app()->getLocale(), 'fallbackLocale') as $civility)
                            <option value="{{ $civility->id }}"
                                {{ isset($sponsor) && $sponsor->contact_title == $civility->id ? 'selected' : '' }}>
                                {{ $civility->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person"></i> {{ __('Prénom') }}
                    </label>
                    <input type="text" class="form-control" name="contact_first_name"
                        value="{{ $sponsor->contact_first_name ?? '' }}" required>
                </div>

                <div class="col-md-4">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person"></i> {{ __('Nom') }}
                    </label>
                    <input type="text" class="form-control" name="contact_last_name"
                        value="{{ $sponsor->contact_last_name ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark required">
                        <i class="bi bi-person-badge"></i> {{ __('Fonction') }}
                    </label>
                    <input type="text" class="form-control" name="contact_position"
                        value="{{ $sponsor->contact_position ?? '' }}" required>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-envelope"></i> {{ __('Email du contact') }}
                    </label>
                    <input type="email" class="form-control" name="contact_email"
                        value="{{ $sponsor->contact_email ?? '' }}">
                </div>

                <!-- Options supplémentaires -->
                <div class="col-md-12 mt-4">
                    <h4 class="text-primary mb-3">
                        <i class="bi bi-plus-circle"></i> {{ __('Options supplémentaires') }}
                    </h4>
                </div>

                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_stand" value="1"
                            data-amount="500" {{ isset($sponsor) && $sponsor->include_stand ? 'checked' : '' }}>
                        <label class="form-check-label">
                            Inclure un stand (+500 {{ $congres->currency }})
                        </label>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="speaking_opportunity" value="1"
                            {{ isset($sponsor) && $sponsor->speaking_opportunity ? 'checked' : '' }}>
                        <label class="form-check-label">
                            Opportunité de prise de parole
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-image"></i> {{ __('Logo') }}
                    </label>
                    <input type="file" class="form-control" name="logo" accept="image/*">
                    @if (isset($sponsor) && $sponsor->logo)
                        <div class="mt-2">
                            <img src="{{ Voyager::image($sponsor->logo) }}" alt="Logo"
                                style="max-height: 80px;">
                        </div>
                    @endif
                </div>

                <!-- Total -->
                <div class="col-md-12 mt-4">
                    <div class="alert alert-success text-center fs-5 fw-bold" id="sponsor-total-box">
                        {{ __('Total à payer') }}: <span id="sponsor-total-amount">0</span>
                        {{ $congres->currency }}
                    </div>
                </div>
            </div>
        </div>

        @include('voyager::forms.btn_save_continue')
        </form>
    </div>
</div>

<!-- JavaScript pour gérer les calculs -->
@section('javascript')
    <script>
        $(document).ready(function() {
            // Calcul pour les exposants
            function calculerTotalExposant() {
                let total = 0;

                // Prix du stand
                const standType = $('select[name="stand_type"]').find('option:selected');
                if (standType.length && standType.data('amount')) {
                    total += parseFloat(standType.data('amount'));
                }

                // Équipements supplémentaires
                $('.equipment-checkbox:checked').each(function() {
                    total += parseFloat($(this).data('amount'));
                });

                // Badges supplémentaires
                const additionalBadges = parseInt($('input[name="additional_badges"]').val()) || 0;
                const badgePrice = parseFloat($('input[name="additional_badges"]').data('price')) || 0;
                total += additionalBadges * badgePrice;

                $('#exhibitor-total-amount').text(total.toFixed(2));
            }

            // Calcul pour les sponsors
            function calculerTotalSponsor() {
                let total = 0;

                // Type de sponsorship
                const sponsorshipType = $('#sponsorship_type').find('option:selected');
                if (sponsorshipType.length && sponsorshipType.data('amount')) {
                    total += parseFloat(sponsorshipType.data('amount'));
                }

                // Stand inclus
                if ($('input[name="include_stand"]').is(':checked')) {
                    total += parseFloat($('input[name="include_stand"]').data('amount'));
                }

                $('#sponsor-total-amount').text(total.toFixed(2));
            }

            // Événements pour exposants
            $('select[name="stand_type"], input[name="additional_badges"]').on('change', calculerTotalExposant);
            $(document).on('change', '.equipment-checkbox', calculerTotalExposant);

            // Événements pour sponsors
            $('#sponsorship_type, input[name="include_stand"]').on('change', calculerTotalSponsor);

            // Initialisation
            calculerTotalExposant();
            calculerTotalSponsor();
        });
    </script>
@endsection
