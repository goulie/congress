@php
    $categories = App\Models\CategorieRegistrant::forCongress($congres->id);
    $dinner = App\Models\CategorieRegistrant::DinnerforCongress($congres->id);
    $accompanying = App\Models\CategorieRegistrant::accompanyingPersonForCongress($congres->id);
    $tours = App\Models\CategorieRegistrant::ToursforCongress($congres->id);
    $passDeleguate = App\Models\CategorieRegistrant::PassDeleguateforCongress($congres->id);
    $non_member = App\Models\CategorieRegistrant::NonMemberPriceforCongress($congres->id);
    $student_ywp = App\Models\CategorieRegistrant::studentForCongress($congres->id);
    $student_ywp_member = App\Models\CategorieRegistrant::student_ywp_memberForCongress($congres->id);
    $deleguate = App\Models\CategorieRegistrant::deleguateForCongress($congres->id);
@endphp
@if ($edit)
    <form class="ajax-form" id="form_update" method="POST" action="{{ route('participant.update.group') }}"
        enctype="multipart/form-data">
        <input type="hidden" name="uuid" value="{{ $participant->uuid }}">
    @else
        <form class="ajax-form" id="form_create" method="POST" action="{{ route('participants.store.group') }}"
            enctype="multipart/form-data">
@endif
@csrf

<div class="box-body">
    <div class="row">
        <!-- Catégorie -->
        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-person-badge"></i> {{ __('registration.step3.fields.category') }}
            </label>
            <select class="form-control" name="categorie" id="categorie" required>
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                @php

                    $categories = App\Models\CategoryParticipant::where(['status' => 'isActive'])->get();

                @endphp

                @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}"
                        data-amount="{{ $categorie->libelle == 'Student' ? $student_ywp->montant : $deleguate->montant }}"
                        {{ isset($participant) && $participant->participant_category_id == $categorie->id ? 'selected' : '' }}>
                        {{ $categorie->translate(app()->getLocale(), 'fallbackLocale')->libelle }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Êtes-vous ywp or student   -->
        <div class="col-md-4 hidden" id="ywp_student-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-user"></i> {{ __('registration.step3.fields.type_ywp_student') }}

            </label>
            <select class="form-control" name="ywp_or_student" id="ywp_student">
                <option value="" @empty($participant) selected @endempty disabled>
                    {{ __('registration.choose') }}</option>
                <option value="ywp"
                    {{ isset($participant) && $participant->ywp_or_student == 'ywp' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.ywp') }}
                </option>
                <option value="student"
                    {{ isset($participant) && $participant->ywp_or_student == 'student' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.student') }}
                </option>
            </select>
        </div>
        <!-- Passe 1 Jour -->
        <div class="col-md-4 hidden" id="pass-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-ticket-detailed"></i> {{ __('registration.step3.fields.day_pass') }}
                <span class="text-danger">({{ $passDeleguate->montant . ' ' . $congres->currency }})</span>
            </label>
            <select class="form-control" name="pass_deleguate" id="pass">
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                <option value="oui"
                    {{ isset($participant) && $participant->pass_deleguate == 'oui' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.oui') }}
                </option>
                <option value="non"
                    {{ isset($participant) && $participant->pass_deleguate == 'non' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.non') }}
                </option>
            </select>
        </div>

        <!-- Choix de la date du pass -->
        <div class="col-md-4 hidden" id="pass-date-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-calendar-event"></i> {{ __('registration.step3.fields.choose_pass_dates') }}
            </label>

            @php
                use Carbon\Carbon;
                Carbon::setLocale('fr');
                $dates = App\Models\JourPassDelegue::where('congres_id', $congres->id)->select('date', 'id')->get();
                // Récupérer les dates déjà sélectionnées
                $selectedDates =
                    isset($participant) && $participant->deleguate_day
                        ? json_decode($participant->deleguate_day, true)
                        : [];
            @endphp

            <div class="mt-3">
                @forelse ($dates as $jour)
                    <div class="form-check">
                        <input class="form-check-input pass-date-checkbox" type="checkbox" name="pass_date[]"
                            value="{{ $jour->id }}" data-amount="{{ $passDeleguate->montant ?? 0 }}"
                            id="pass_{{ $jour->id }}"
                            {{ $selectedDates && in_array($jour->id, $selectedDates) ? 'checked' : '' }}>

                        <label class="form-check-label" for="pass_{{ $jour->id }}">
                            {{ ucfirst(Carbon::parse($jour->date)->translatedFormat('l d F Y')) }}
                            <span class="text-danger">({{ $passDeleguate->montant . ' ' . $congres->currency }})</span>
                        </label>
                    </div>
                @empty
                    <p class="text-muted">{{ __('registration.step3.fields.no_pass_dates') }}</p>
                @endforelse
            </div>
        </div>
        <!-- Êtes-vous membre -->
        <div class="col-md-4 hidden" id="membership-code-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-person-check"></i> {{ __('registration.step3.fields.membership') }}
            </label>
            <select class="form-control" name="membership" id="membership">
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                <option value="oui"
                    {{ isset($participant) && $participant->membre_aae == 'oui' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.oui') }}
                </option>
                <option value="non" data-amount="{{ $non_member->montant ?? 0 }}"
                    {{ isset($participant) && $participant->membre_aae == 'non' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.non') }}
                </option>
            </select>
        </div>

        <!-- Code membre -->
        <div class="col-md-4 hidden" id="member-code-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-upc"></i> {{ __('registration.step3.fields.membershipcode') }}
            </label>
            <input type="text" class="form-control" name="member_code" id="member_code"
                placeholder="{{ __('registration.step3.placeholders.membershipcode') }}"
                value="{{ $participant->membership_code ?? '' }}">
        </div>

        <!-- Dîner -->
        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-cup-straw"></i> {{ __('registration.step3.fields.diner_gala') }} <span
                    class="text-danger">({{ $dinner->montant . ' ' . $congres->currency . ' - ' . $congres->nbre_place_dinner }}
                    {{ app()->getLocale() == 'fr' ? 'Places' : 'seats' }} ) </span>
            </label>
            <select class="form-control" name="dinner" id="dinner" required>
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                <option value="oui" data-amount="{{ $dinner->montant }}"
                    {{ isset($participant) && $participant->diner == 'oui' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.oui') }}
                </option>
                <option value="non" {{ isset($participant) && $participant->diner == 'non' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.non') }}
                </option>
            </select>
        </div>

        <!-- Visite -->
        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-geo-alt"></i> {{ __('registration.step3.fields.visite_technical') }} <span
                    class="text-danger">({{ $tours->montant . ' ' . $congres->currency }})</span></span>
            </label>
            <select class="form-control" name="visit" id="visit" required>
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                <option value="oui" data-amount="{{ $tours->montant }}"
                    {{ isset($participant) && $participant->visite == 'oui' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.oui') }}
                </option>
                <option value="non" {{ isset($participant) && $participant->visite == 'non' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.non') }}
                </option>
            </select>
        </div>


        <!-- Choix du site de visite -->
        <div class="col-md-4 hidden" id="site-visit-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-building"></i> {{ __('registration.step3.fields.choose_visit_site') }}
            </label>
            <select class="form-control" name="site_visit" id="site_visit">
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                @foreach (App\Models\SiteVisite::where('congres_id', $congres->id)->get() as $site)
                    <option value="{{ $site->id }}"
                        {{ isset($participant) && $participant->site_visit_id == $site->id ? 'selected' : '' }}>
                        {{ $site->translate(app()->getLocale(), 'fallbackLocale')->libelle }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- lettre_invitation --}}
        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-envelope-check"></i>
                {{ __('registration.step3.fields.lettre_invitation') }}
            </label>
            <select class="form-control" name="lettre_invitation" required>
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                <option value="oui"
                    {{ isset($participant) && $participant->invitation_letter == 'oui' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.oui') }}
                </option>
                <option value="non"
                    {{ isset($participant) && $participant->invitation_letter == 'non' ? 'selected' : '' }}>
                    {{ __('registration.step3.fields.non') }}
                </option>
            </select>
        </div>
        {{-- passport-number --}}
        <div class="col-md-4 hidden" id="passport-number-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-upc"></i> {{ __('registration.step3.fields.num_passeport') }}
            </label>
            <input type="text" class="form-control" name="passport_number" id="passport_number"
                placeholder="{{ __('registration.step3.placeholders.num_passeport') }}"
                value="{{ $participant->passeport_number ?? '' }}">
        </div>
        {{-- passeport expiry date --}}
        <div class="col-md-4 hidden" id="passport-date-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-calendar"></i> {{ __('registration.step3.fields.date_passeport') }}
            </label>

            <div class="input-group date" id="passport_date_group">
                <input type="text" class="form-control" name="passport_date" id="passport_date"
                    placeholder="{{ __('registration.step3.placeholders.date_passeport') }}"
                    value="{{ $participant->expiration_passeport_date ?? '' }}" readonly>
                <span class="input-group-addon">
                    <i class="bi bi-calendar"></i>
                </span>
            </div>

            <span class="text-danger" id="passport_date_error" style="display:none;"></span>
        </div>


        <!-- Upload Carte étudiant -->
        <div class="col-md-4 hidden" id="student-card-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-card-image"></i> {{ __('registration.step3.fields.student_card') }}
                <span class="text-danger">Max: 2Mo</span>
            </label>
            <input type="file" class="form-control" name="student_card" id="student_card" accept="image/*"
                {{ isset($participant) && $participant->student_card ? '' : 'required' }}>
            @if (isset($participant) && $participant->student_card)
                <a class="btn btn-primary btn-sm" href="{{ Voyager::image($participant->student_card) }}"
                    target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> {{ __('registration.step3.buttons.open') }}
                </a>
                <div class="form-check mt-2">
                    <label class="form-check-label text-danger" for="remove_student_card">
                        {{ __('registration.step3.fields.upload_to_replace') }}
                    </label>
                </div>
            @endif
        </div>

        <!-- Upload Lettre d'attestation -->
        <div class="col-md-4 hidden" id="student-letter-box">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-file-earmark-text"></i> {{ __('registration.step3.fields.attestation_letter') }}
                <span class="text-danger">Max: 2Mo</span>
            </label>
            <input type="file" class="form-control" name="student_letter" id="student_letter"
                accept="image/*,application/pdf"
                {{ !isset($participant) || !$participant->student_letter ? 'required' : '' }}>
            @if (isset($participant) && $participant->student_letter)
                <a class="btn btn-primary btn-sm" href="{{ Voyager::image($participant->student_letter) }}"
                    target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> {{ __('registration.step3.buttons.open') }}
                </a>
                <div class="form-check mt-2">
                    <label class="form-check-label text-danger" for="remove_student_letter">
                        {{ __('registration.step3.fields.upload_to_replace') }}
                    </label>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-person-vcard"></i>
                {{ __('registration.step1.fields.title') }}
            </label>
            <select class="form-control" name="title" required>
                <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                @forelse (App\Models\Civility::get()->translate(app()->getLocale(), 'fallbackLocale') as $civility)
                    <option value="{{ $civility->id }}"
                        {{ isset($participant) && $participant->civility_id == $civility->id ? 'selected' : '' }}>
                        {{ $civility->libelle }}
                    </option>
                @empty
                    <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                @endforelse
            </select>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-gender-ambiguous"></i>
                {{ __('registration.step1.fields.gender') }}
            </label>
            <select class="form-control" name="gender" required>
                <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                @forelse (App\Models\Gender::get()->translate(app()->getLocale(), 'fallbackLocale') as $gender)
                    <option value="{{ $gender->id }}"
                        {{ isset($participant) && $participant->gender_id == $gender->id ? 'selected' : '' }}>
                        {{ $gender->libelle }}
                    </option>
                @empty
                    <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                @endforelse
            </select>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-person"></i>
                {{ __('registration.step1.fields.first_name') }}
            </label>
            <input type="text" class="form-control" name="first_name"
                placeholder="{{ __('registration.step1.placeholders.first_name') }}"
                @isset($participant) value="{{ $participant->fname }}" @endisset required>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-person"></i>
                {{ __('registration.step1.fields.last_name') }}
            </label>
            <input type="text" class="form-control" name="last_name"
                placeholder="{{ __('registration.step1.placeholders.last_name') }}"
                @isset($participant) value="{{ $participant->lname }}" @endisset required>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-mortarboard"></i>
                {{ __('registration.step1.fields.education') }}
            </label>
            <select class="form-control" name="education" required>
                <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                @forelse (App\Models\StudentLevel::all()->translate(app()->getLocale(), 'fallbackLocale')->sortBy('order') as $studentLevel)
                    <option value="{{ $studentLevel->id }}"
                        {{ isset($participant) && $participant->student_level_id == $studentLevel->id ? 'selected' : '' }}>
                        {{ $studentLevel->libelle }}
                    </option>
                @empty
                    <option disabled>No data</option>
                @endforelse
            </select>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-globe"></i>
                {{ __('registration.step1.fields.country') }}
            </label>
            <select class="form-control" name="country" required>
                <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                    <option value="{{ $country->id }}"
                        {{ isset($participant) && $participant->nationality_id == $country->id ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                    </option>
                @empty
                    <option disabled>No data</option>
                @endforelse
            </select>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-hourglass-split"></i>
                {{ __('registration.step1.fields.age_range') }}
            </label>
            <select class="form-control" name="age_range" id="age" required>
                <option value="" selected disabled>{{ __('registration.choose') }}</option>
                @foreach (App\Models\AgeRange::all()->translate(app()->getLocale(), 'fallbackLocale') as $ageRange)
                    <option value="{{ $ageRange->id }}"
                        {{ isset($participant) && $participant->age_range_id == $ageRange->id ? 'selected' : '' }}>
                        {{ $ageRange->libelle }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-envelope"></i>
                {{ __('registration.step2.fields.email') }}
            </label>
            <input type="email" class="form-control"
                placeholder="{{ __('registration.step2.placeholders.email') }}" name="email"
                @isset($participant) value="{{ $participant->email }}" @endisset required>
        </div>
        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark">
                <i class="bi bi-telephone"></i>
                {{ __('registration.step2.fields.telephone') }}
            </label>

            <input type="number" class="form-control" id="telephone-input" minlength="8" maxlength="15"
                name="telephone" placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                @isset($participant) value="{{ $participant->phone }}" @endisset required>

            <input type="hidden" id="telephone" name="telephone_complet">
        </div>
        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-building"></i>
                {{ __('registration.step2.fields.organisation') }}
            </label>
            <input type="text" class="form-control" name="organisation"
                placeholder="{{ __('registration.step2.placeholders.organisation') }}"
                @isset($participant) value="{{ $participant->organisation }}" @endisset required>
        </div>

        @php
            $levels = App\Models\TypeOrganisation::get()
                ->translate(app()->getLocale(), 'fallbackLocale')
                ->sortBy(function ($level) {
                    // Met "Autre" à la fin du tri alphabétique
                    return $level->libelle === 'Autre' || $level->libelle === 'Other' ? 'ZZZZZZZZ' : $level->libelle;
                });
        @endphp
        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-card-checklist"></i>
                {{ __('registration.step2.fields.type_organisation') }}
            </label>
            <select class="form-control" name="type_organisation" id="type_organisation" required>
                <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                @forelse ($levels as $typeOrganisation)
                    <option value="{{ $typeOrganisation->id }}"
                        {{ isset($participant) && $participant->organisation_type_id == $typeOrganisation->id ? 'selected' : '' }}>
                        {{ $typeOrganisation->libelle }}</option>
                @empty
                    <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                @endforelse
            </select>
        </div>

        <div class="col-md-4 {{ isset($participant) && $participant->organisation_type_id == 10 ? '' : 'hidden' }}"
            id="autre_type_org_div">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-pencil-square"></i>
                {{ __('registration.step2.fields.autre_type_org') }}
            </label>
            <input type="text" class="form-control" id="autre_type_org" name="autre_type_org"
                placeholder="{{ __('registration.step2.placeholders.autre_type_org') }}"
                @isset($participant) value="{{ $participant->organisation_type_other }}" @endisset
                {{ isset($participant) && $participant->organisation_type_id == 10 ? 'required' : '' }}>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-person-badge"></i>
                {{ __('registration.step2.fields.fonction') }}
            </label>
            <input type="text" class="form-control" name="fonction"
                placeholder="{{ __('registration.step2.placeholders.fonction') }}"
                @isset($participant) value="{{ $participant->job }}" @endisset required required>
        </div>

        <div class="col-md-4">
            <label class="control-label font-weight-bold text-dark">
                <i class="bi bi-globe-americas"></i>
                {{ __('registration.step2.fields.job_country') }}
            </label>
            <select class="form-control" name="job_country">
                <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                    <option value="{{ $country->id }}"
                        {{ isset($participant) && $participant->job_country_id == $country->id ? 'selected' : '' }}>
                        {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                    </option>
                @empty
                    <option disabled>No data</option>
                @endforelse
            </select>
        </div>
    </div>










    <div class="row">
        <div class="col-md-12 mt-4" style="font-weight: bold;font-size: 1.5em">
            <div class="alert alert-info text-center fs-5 fw-bold" id="total-box">
                {{ __('registration.step3.fields.total_to_pay') }}: <span id="total-amount">0</span>
                {{ $congres->currency }}
            </div>
        </div>
    </div>
</div>

<div class="box-footer">
    <div class="navigation-buttons mt-3">

        <button type="button" id="submit" class="btn btn-primary">
            {{ __('registration.step1.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>

</form>
<div class="table-responsive">
    <table class="table table-striped table-hover table-condensed">
        <thead>
            <tr>
                <th>{{ app()->getLocale() == 'fr' ? 'Catégorie' : 'Category#' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Nom' : 'Name' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Prénoms' : 'First Name' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Genre' : 'Gender' }}</th>
                <th>Email</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Fonction' : 'Position' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Visite' : 'Visit' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Dîner Gala' : 'Gala Dinner' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Total à payer' : 'Total to pay' }}</th>
                <th>{{ app()->getLocale() == 'fr' ? 'Statut' : 'Status' }}</th>
                <th class="text-right">{{ app()->getLocale() == 'fr' ? 'Actions' : 'Actions' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($participants as $participant)
                @php
                    $invoice = App\Models\Invoice::where('participant_id', $participant->id)->first();
                @endphp
                <tr>
                    <td>
                        {{ $participant->participantCategory->translate(app()->getLocale(), 'fallbackLocale')->libelle ?? '-' }}
                    </td>

                    <td>{{ $participant->fname }}</td>
                    <td>{{ $participant->lname }}</td>
                    <td>{{ $participant->gender->translate(app()->getLocale(), 'fallbackLocale')->libelle ?? '' }}
                    </td>
                    <td>{{ $participant->email ?? '' }}</td>
                    <td>{{ $participant->organisation ?? '' }}</td>
                    <td>{{ $participant->job ?? '' }}</td>

                    <td>
                        {!! $participant->visite == 'oui'
                            ? (app()->getLocale() == 'fr'
                                ? 'Oui <i class="bi bi-check-circle-fill" style="color:rgb(7, 161, 7);"></i>'
                                : 'Yes <i class="bi bi-check-circle-fill" style="color:rgb(7, 161, 7);"></i>')
                            : (app()->getLocale() == 'fr'
                                ? 'Non <i class="bi bi-x-circle-fill" style="color:rgb(255, 0, 0);"></i>'
                                : 'No <i class="bi bi-x-circle-fill" style="color:rgb(255, 0, 0);"></i>') !!}
                    </td>
                    <td>
                        {!! $participant->diner == 'oui'
                            ? (app()->getLocale() == 'fr'
                                ? 'Oui <i class="bi bi-check-circle-fill" style="color:rgb(7, 161, 7);"></i>'
                                : 'Yes <i class="bi bi-check-circle-fill" style="color:rgb(7, 161, 7);"></i>')
                            : (app()->getLocale() == 'fr'
                                ? 'Non <i class="bi bi-x-circle-fill" style="color:rgb(255, 0, 0);"></i>'
                                : 'No <i class="bi bi-x-circle-fill" style="color:rgb(255, 0, 0);"></i>') !!}
                    </td>
                    <td>
                        <span class="label label-dark" style="font-size:14px">

                            @if ($congres->currency === 'EUR')
                                {{ $invoice->total_amount ?? 0 . ' €' }}
                            @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                                {{ '$ ' . $invoice->total_amount ?? 0 }}
                            @else
                                {{ $invoice->total_amount ?? 0 . ' ' . $congres->currency }}
                            @endif
                            {{ $congres->currency }}
                        </span>
                    </td>

                    <td class="text-center">
                        @php
                            switch ($invoice->status) {
                                case 'paid':
                                case 'Payé':
                                    $badgeClass = 'label label-success';
                                    break;
                                case 'pending':
                                case 'En attente':
                                    $badgeClass = 'label label-warning';
                                    break;
                                case 'cancelled':
                                case 'Annulé':
                                    $badgeClass = 'label label-danger';
                                    break;
                                default:
                                    $badgeClass = 'label label-secondary';
                                    break;
                            }
                        @endphp

                        <span class="badge {{ $badgeClass }}" style="font-size:14px">
                            {{ app()->getLocale() == 'fr' ? $invoice->status : ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('participant.recap', $participant->uuid) }}"
                            class="btn btn_link btn-xs view-details">
                            <i class="glyphicon glyphicon-eye-open"></i>
                            {{-- {{ app()->getLocale() == 'fr' ? 'Détails' : 'Details' }} --}}
                        </a>

                        <a href="{{ route('participant.edit', $participant->uuid ?? '') }}"
                            class="btn btn-warning btn-xs">
                            <i class="glyphicon glyphicon-edit"></i>
                            {{-- {{ app()->getLocale() == 'fr' ? 'Modifier' : 'Edit' }} --}}
                        </a>
                        <a href="javascript:void(0);" class="btn btn-danger btn-xs delete-participant"
                            data-id="{{ $participant->id }}"
                            data-name="{{ $participant->fname }} {{ $participant->lname }}"
                            data-url="{{ route('participant.destroy', $participant->uuid ?? '') }}"
                            title="{{ app()->getLocale() == 'fr' ? 'Supprimer ce participant' : 'Delete this participant' }}">
                            <i class="glyphicon glyphicon-trash"></i>
                            {{-- {{ app()->getLocale() == 'fr' ? 'Supprimer' : 'Delete' }} --}}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">
                        {{ app()->getLocale() == 'fr' ? 'Aucun participant' : 'No participant' }}
                    </td>
                </tr>
            @endforelse


        </tbody>
    </table>
</div>




@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- BOOTSTRAP 3 DATEPICKER -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js">
    </script>

    <script>
        $(function() {
            // ============================
            // CONFIG / CONSTANTES (serveur -> JS)
            // ============================
            const DELEGUE_ID = '1'; // tu as confirmé : délégué = 1
            const STUDENT_ID = '4'; // tu as confirmé : student = 4

            const congresEndDate = "{{ $congres->end_date }}";

            const montant = {
                passDelegue: parseFloat("{{ $passDeleguate->montant ?? 0 }}") || 0,
                nonMembre: parseFloat("{{ $non_member->montant ?? 0 }}") || 0,
                dinner: parseFloat("{{ $dinner->montant ?? 0 }}") || 0,
                visite: parseFloat("{{ $tours->montant ?? 0 }}") || 0,
                delegue: parseFloat("{{ $deleguate->montant ?? 0 }}") || 0,
                student: parseFloat("{{ $student_ywp->montant ?? 0 }}") || 0,
                student_member: parseFloat("{{ $student_ywp_member->montant ?? 0 }}") || 0
            };

            // Flags based on existing participant files (server side)
            const hasStudentCard = @json(isset($participant) && $participant->student_card ? true : false);
            const hasStudentLetter = @json(isset($participant) && $participant->student_letter ? true : false);

            // ============================
            // Datepicker
            // ============================
            $('#passport_date_group').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
                orientation: 'bottom',
                language: 'fr',
                startDate: congresEndDate,

            });

            // ============================
            // HELPERS show/hide + required
            // ============================
            function show(el, setRequired = false, requiredSelector = null) {
                $(el).removeClass('hidden').slideDown(120)
                    .find('input,select,textarea').prop('disabled', false);

                if (setRequired) {
                    if (requiredSelector) $(el).find(requiredSelector).prop('required', true);
                    else $(el).find('input,select,textarea').prop('required', true);
                }
            }

            function hide(el, clearValue = true) {
                $(el).slideUp(120).addClass('hidden')
                    .find('input,select,textarea')
                    .prop('disabled', true)
                    .prop('required', false)
                    .each(function() {
                        if (clearValue) {
                            if ($(this).is(':checkbox') || $(this).is(':radio')) $(this).prop('checked', false);
                            else $(this).val('');
                        }
                    });
            }

            // ============================
            // Calcul du total
            // ============================
            function calculerTotal() {
                let total = 0;
                const cat = $('#categorie').val();
                const membership = $('#membership').val();
                const dinner = $('#dinner').val();
                const visit = $('#visit').val();
                const pass = $('#pass').val();
                const nbPass = $('.pass-date-checkbox:checked').length;

                if (!cat) {
                    $('#total-amount').text('0.00');
                    return;
                }

                if (cat === DELEGUE_ID) {
                    if (pass === 'oui' && nbPass > 0) {
                        total += nbPass * montant.passDelegue;
                    } else {
                        total += (membership === 'oui') ? montant.delegue : montant.nonMembre;
                    }
                } else if (cat === STUDENT_ID) {
                    total += (membership === 'oui') ? montant.student_member : montant.student;
                } else {
                    const montantCategorie = parseFloat($('#categorie option:selected').data('amount')) || 0;
                    total += (membership === 'oui') ? montantCategorie : montant.nonMembre;
                }

                if (dinner === 'oui') total += montant.dinner;
                if (visit === 'oui') total += montant.visite;

                $('#total-amount').text(total.toFixed(2));
            }

            // ============================
            // VALIDATION PASSEPORT (client)
            // ============================
            function validerDatePasseport() {
                if (!$('#passport_date').is(':visible')) return true;

                const dateStr = $('#passport_date').val();
                if (!dateStr) {
                    $('#passport_date_error').text(
                            "{{ __('registration.step3.fields.date_required') ?? 'La date est obligatoire' }}")
                        .show();
                    return false;
                }
                if (dateStr <= congresEndDate) {
                    $('#passport_date_error').text(
                        "{{ __('registration.step3.fields.date_must_be_after') ?? 'La date doit être supérieure au' }} "
                    ).show();
                    return false;
                }
                $('#passport_date_error').hide();
                return true;
            }

            // ============================
            // LOGIQUE : DÉLÉGUÉ
            // ============================
            function logiqueDelegue() {
                // Passeport visibles et required unless file exists
                show('#passport-number-box');
                $('#passport_number').prop('required', true);

                show('#passport-date-box');
                $('#passport_date').prop('required', true);

                // Pass field
                show('#pass-box');
                $('#pass').prop('required', true);

                const pass = $('#pass').val();

                if (pass === 'oui') {
                    // Show pass date choices
                    show('#pass-date-box');
                    /*  $('.pass-date-checkbox').prop('required', true); */

                    // Hide membership when pass=yes
                    hide('#membership-code-box');
                    hide('#member-code-box');
                    $('#membership').val('');
                    $('#member_code').val('');
                    $('#membership').prop('required', false);
                    $('#member_code').prop('required', false);
                } else {
                    // pass != oui => ask membership
                    hide('#pass-date-box',
                        false); // keep checkbox values if there are pre-checked ones, but remove required
                    /* $('.pass-date-checkbox').prop('required', false); */

                    show('#membership-code-box');
                    $('#membership').prop('required', true);

                    if ($('#membership').val() === 'oui') {
                        show('#member-code-box');
                        $('#member_code').prop('required', true);
                    } else {
                        hide('#member-code-box');
                        $('#member_code').prop('required', false);
                    }
                }
            }

            // ============================
            // LOGIQUE : STUDENT
            // ============================
            function logiqueStudent() {
                // Passeport visibles et required unless file exists
                show('#passport-number-box');
                $('#passport_number').prop('required', true);

                show('#passport-date-box');
                $('#passport_date').prop('required', true);

                // Student field
                show('#ywp_student-box');
                $('#ywp_student').prop('required', true);

                // membership always asked for students
                show('#membership-code-box');
                $('#membership').prop('required', true);

                if ($('#membership').val() === 'oui') {
                    show('#member-code-box');
                    $('#member_code').prop('required', true);
                } else {
                    hide('#member-code-box');
                    $('#member_code').prop('required', false);
                }

                const type = $('#ywp_student').val(); // ywp | student

                if (type === 'ywp') {
                    show('#student-letter-box');
                    $('#student_letter').prop('required', !hasStudentLetter);
                    hide('#student-card-box');
                    $('#student_card').prop('required', false);
                } else if (type === 'student') {
                    show('#student-card-box');
                    $('#student_card').prop('required', !hasStudentCard);
                    hide('#student-letter-box');
                    $('#student_letter').prop('required', false);
                } else {
                    hide('#student-letter-box');
                    hide('#student-card-box');
                    $('#student_card').prop('required', false);
                    $('#student_letter').prop('required', false);
                }
            }

            // ============================
            // LOGIQUE : VISITE
            // ============================
            function logiqueVisite() {
                if ($('#visit').val() === 'oui') {
                    show('#site-visit-box');
                    $('#site_visit').prop('required', true);
                } else {
                    hide('#site-visit-box');
                    $('#site_visit').prop('required', false);
                }
            }

            // ============================
            // RENDER PRINCIPAL
            // ============================
            function renderForm() {
                // hide everything first (but do not clear pass-date checkboxes if user had checked)
                hide('#pass-date-box',
                    false); // don't clear pass_date checkboxes on reset (so edit keeps selections)
                hide('#pass-box', false);
                hide('#membership-code-box', false);
                hide('#member-code-box', false);
                hide('#ywp_student-box', false);
                hide('#student-card-box', false);
                hide('#student-letter-box', false);
                hide('#site-visit-box', false);
                hide('#passport-number-box', false);
                hide('#passport-date-box', false);

                // Now apply logic per category
                const cat = $('#categorie').val();

                if (cat === DELEGUE_ID) logiqueDelegue();
                else if (cat === STUDENT_ID) logiqueStudent();

                logiqueVisite();
                calculerTotal();
            }

            // ============================
            // EVENTS
            // ============================
            $('#categorie').on('change', function() {
                renderForm();
            });

            $('#pass').on('change', function() {
                // when pass changes we may need to clear membership fields
                renderForm();
            });

            $('#membership').on('change', function() {
                renderForm();
            });

            $('#ywp_student').on('change', function() {
                renderForm();
            });

            $('#visit').on('change', function() {
                logiqueVisite();
                calculerTotal();
            });

            $(document).on('change', '.pass-date-checkbox', function() {
                calculerTotal();
            });

            $('#dinner, #membership').on('change', calculerTotal);


            function validatePassDays() {
                const pass = $('#pass').val();
                const cat = $('#categorie').val();
                //Validation des pass
                if (cat === DELEGUE_ID && pass === 'oui') {
                    if ($('.pass-date-checkbox:checked').length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: "Veuillez sélectionner au moins un jour de pass."
                        });
                        return false;
                    }
                }
                return true;
            }

            $('#submit').on('click', () => $('form.ajax-form').submit());

            // ============================
            // SUBMIT AJAX with passport validation
            // ============================
            $('form.ajax-form').on('submit', function(e) {
                e.preventDefault();

                if (!validerDatePasseport()) return;
                if (!validatePassDays()) return;

                const form = $(this);
                const formData = new FormData(this);

                $('#loader').removeClass('hidden').show();
                //submit the form


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#loader').hide();
                        Swal.fire({
                            icon: 'success',
                            title: "{{ __('registration.success') }}",
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            if (response.redirect) window.location.href = response
                                .redirect;
                            else window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        $('#loader').hide();
                        let msg = "{{ __('registration.error_occurred') }}";
                        if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON
                            .errors).flat().join('<br>');
                        else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: "{{ __('registration.error') }}",
                            html: msg
                        });
                    }
                });
            });

            // ============================
            // INITIAL LOAD
            // ============================
            // If server-side selected values exist, renderForm will pick them
            renderForm();

        }); // end jQuery ready
    </script>
@endsection
