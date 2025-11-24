@php
    $categories = App\Models\CategorieRegistrant::forCongress($congres->id);
    $dinner = App\Models\CategorieRegistrant::DinnerforCongress($congres->id);
    $accompanying = App\Models\CategorieRegistrant::accompanyingPersonForCongress($congres->id);
    $tours = App\Models\CategorieRegistrant::ToursforCongress($congres->id);
    $passDeleguate = App\Models\CategorieRegistrant::PassDeleguateforCongress($congres->id);
    $non_member = App\Models\CategorieRegistrant::NonMemberPriceforCongress($congres->id);
    $student_ywp = App\Models\CategorieRegistrant::studentForCongress($congres->id);
    $deleguate = App\Models\CategorieRegistrant::deleguateForCongress($congres->id);
@endphp

<form method="POST" action="{{ route('participants.store.group') }}" enctype="multipart/form-data">
    @csrf

    <div class="box-body">

        {{-- Affichage des erreurs générales --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Étape 1 : Renseignements personnels --}}
        <div class="row">
            <div class="col-md-2">
                <label class="control-label font-weight-bold font-weight-bold text-dark required">
                    <i class="bi bi-person-vcard"></i>
                    {{ __('registration.step1.fields.title') }}
                </label>
                <select class="form-control @error('title') is-invalid @enderror" name="title" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\Civility::get()->translate(app()->getLocale(), 'fallbackLocale') as $civility)
                        <option value="{{ $civility->id }}"
                            {{ old('title') == $civility->id ? 'selected' : (session('participant_data.title') == $civility->id ? 'selected' : '') }}>
                            {{ $civility->libelle }}
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.first_name') }}
                </label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name"
                    placeholder="{{ __('registration.step1.placeholders.first_name') }}"
                    value="{{ old('first_name', session('participant_data.first_name')) }}" required>
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold font-weight-bold text-dark required">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step1.fields.last_name') }}
                </label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name"
                    placeholder="{{ __('registration.step1.placeholders.last_name') }}"
                    value="{{ old('last_name', session('participant_data.last_name')) }}" required>
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-mortarboard"></i>
                    {{ __('registration.step1.fields.education') }}
                </label>
                <select class="form-control @error('education') is-invalid @enderror" name="education" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\StudentLevel::all()->translate(app()->getLocale(), 'fallbackLocale') as $studentLevel)
                        <option value="{{ $studentLevel->id }}"
                            {{ old('education') == $studentLevel->id ? 'selected' : (session('participant_data.education') == $studentLevel->id ? 'selected' : '') }}>
                            {{ $studentLevel->libelle }}
                        </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
                @error('education')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold font-weight-bold text-dark required">
                    <i class="bi bi-gender-ambiguous"></i>
                    {{ __('registration.step1.fields.gender') }}
                </label>
                <select class="form-control @error('gender') is-invalid @enderror" name="gender" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\Gender::get()->translate(app()->getLocale(), 'fallbackLocale') as $gender)
                        <option value="{{ $gender->id }}"
                            {{ old('gender') == $gender->id ? 'selected' : (session('participant_data.gender') == $gender->id ? 'selected' : '') }}>
                            {{ $gender->libelle }}
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
                @error('gender')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark font-weight-bold required">
                    <i class="bi bi-globe"></i>
                    {{ __('registration.step1.fields.country') }}
                </label>
                <select class="form-control @error('country') is-invalid @enderror" name="country" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                        <option value="{{ $country->id }}"
                            {{ old('country') == $country->id ? 'selected' : (session('participant_data.country') == $country->id ? 'selected' : '') }}>
                            {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                        </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Étape 2 : Coordonnées --}}
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-envelope"></i>
                    {{ __('registration.step2.fields.email') }}
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="{{ __('registration.step2.placeholders.email') }}" name="email"
                    value="{{ old('email', session('participant_data.email')) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-telephone"></i>
                    {{ __('registration.step2.fields.telephone') }}
                </label>
                <input type="tel" class="form-control telephone-input @error('telephone') is-invalid @enderror" id="telephone-input"
                    name="telephone" placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                    value="{{ old('telephone', session('participant_data.telephone')) }}" required>
                <input type="hidden" id="telephone" name="telephone_complet"
                    value="{{ old('telephone_complet', session('participant_data.telephone_complet')) }}">
                @error('telephone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-building"></i>
                    {{ __('registration.step2.fields.organisation') }}
                </label>
                <input type="text" class="form-control @error('organisation') is-invalid @enderror"
                    name="organisation" placeholder="{{ __('registration.step2.placeholders.organisation') }}"
                    value="{{ old('organisation', session('participant_data.organisation')) }}" required>
                @error('organisation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-card-checklist"></i>
                    {{ __('registration.step2.fields.type_organisation') }}
                </label>
                <select class="form-control @error('type_organisation') is-invalid @enderror" name="type_organisation"
                    id="type_organisation" required>
                    <option selected value="" disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\TypeOrganisation::get()->translate(app()->getLocale(), 'fallbackLocale') as $typeOrganisation)
                        <option value="{{ $typeOrganisation->id }}"
                            {{ old('type_organisation') == $typeOrganisation->id ? 'selected' : (session('participant_data.type_organisation') == $typeOrganisation->id ? 'selected' : '') }}>
                            {{ $typeOrganisation->libelle }}
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
                @error('type_organisation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2 d-none" id="autre_type_org_div">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-pencil-square"></i>
                    {{ __('registration.step2.fields.autre_type_org') }}
                </label>
                <input type="text" class="form-control" name="autre_type_org"
                    placeholder="{{ __('registration.step2.placeholders.autre_type_org') }}"
                    @isset($participant) value="{{ $participant->organisation_type_other }}" @endisset
                    {{ isset($participant) && $participant->organisation_type_id == 10 ? 'required' : '' }}>
            </div>


            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person-badge"></i>
                    {{ __('registration.step2.fields.fonction') }}
                </label>
                <input type="text" class="form-control @error('fonction') is-invalid @enderror" name="fonction"
                    placeholder="{{ __('registration.step2.placeholders.fonction') }}"
                    value="{{ old('fonction', session('participant_data.fonction')) }}" required>
                @error('fonction')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Étape 3 : Détails du congrès --}}
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person-badge"></i>
                    {{ __('registration.step3.fields.category') }}
                </label>
                <select class="form-control @error('category') is-invalid @enderror" name="category" id="category"
                    required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\CategoryParticipant::get()->translate(app()->getLocale(), 'fallbackLocale') as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category') == $category->id ? 'selected' : (session('participant_data.category') == $category->id ? 'selected' : '') }}>
                            {{ $category->libelle }}
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step3.fields.membership') }}
                </label>

                <select class="form-control" name="membership" id="membership" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>

                    @forelse ($categories as $typeMember)
                        <option data-amount="{{ $typeMember->montant }}" data-currency="{{ $congres->currency }}"
                            data-requirecode="{{ $typeMember->require_code ?? 0 }}" value="{{ $typeMember->id }}"
                            {{ old('membership', $participant->type_member_id ?? '') == $typeMember->id ? 'selected' : '' }}>
                            {{ $typeMember->libelle }} -
                            <strong>{{ $typeMember->montant }} {{ $congres->currency }}</strong>
                            ({{ $typeMember->periode }})
                        </option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>

                @error('membership')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>



            <div class="col-md-2 hidden" id="membershipcode_div">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-key"></i>
                    {{ __('registration.step3.fields.membershipcode') }}
                </label>

                <input type="text" id="membershipcode" class="form-control" name="membershipcode"
                    placeholder="{{ __('registration.step3.placeholders.membershipcode') }}">
                <div class="invalid-feedback" id="membershipcode_error"></div>
            </div>


            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-egg-fried"></i>
                    {{ __('registration.step3.fields.diner_gala') }}
                    @if ($congres->currency == 'EUR')
                        <strong style="font-weight: bold"> {{ $dinner->montant ?? 0 }} € </strong>
                    @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                        <strong style="font-weight: bold"> ${{ $dinner->montant ?? 0 }}</strong>
                    @else
                        <strong style="font-weight: bold"> {{ $dinner->montant ?? 0 }}
                            {{ $congres->currency }}</strong>
                    @endif
                </label>
                <select id="diner_gala" class="form-control @error('diner_gala') is-invalid @enderror"
                    name="diner_gala" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option data-amount="{{ $dinner->montant ?? 0 }}" value="oui"
                        {{ old('diner_gala') == 'oui' ? 'selected' : (session('participant_data.diner_gala') == 'oui' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                    </option>
                    <option value="non"
                        {{ old('diner_gala') == 'non' ? 'selected' : (session('participant_data.diner_gala') == 'non' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}
                    </option>
                </select>
                @error('diner_gala')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-binoculars"></i>
                    {{ __('registration.step3.fields.visite_touristique') }}
                    @if ($congres->currency == 'EUR')
                        <strong style="font-weight: bold"> {{ $tours->montant ?? 0 }} € </strong>
                    @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                        <strong style="font-weight: bold"> ${{ $tours->montant ?? 0 }}</strong>
                    @else
                        <strong style="font-weight: bold"> {{ $tours->montant ?? 0 }}
                            {{ $congres->currency }}</strong>
                    @endif
                </label>
                <select class="form-control @error('visite_touristique') is-invalid @enderror"
                    id="visite_touristique" name="visite_touristique" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option value="oui" data-amount="{{ $tours->montant ?? 0 }}"
                        {{ old('visite_touristique') == 'oui' ? 'selected' : (session('participant_data.visite_touristique') == 'oui' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                    </option>
                    <option value="non"
                        {{ old('visite_touristique') == 'non' ? 'selected' : (session('participant_data.visite_touristique') == 'non' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}
                    </option>
                </select>
                @error('visite_touristique')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-envelope-check"></i>
                    {{ __('registration.step3.fields.lettre_invitation') }}
                </label>
                <select class="form-control @error('lettre_invitation') is-invalid @enderror"
                    name="lettre_invitation" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option value="oui"
                        {{ old('lettre_invitation') == 'oui' ? 'selected' : (session('participant_data.lettre_invitation') == 'oui' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                    </option>
                    <option value="non"
                        {{ old('lettre_invitation') == 'non' ? 'selected' : (session('participant_data.lettre_invitation') == 'non' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}
                    </option>
                </select>
                @error('lettre_invitation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-pencil-square"></i>
                    {{ __('registration.step3.fields.auteur') }}
                </label>
                <select class="form-control @error('auteur') is-invalid @enderror" name="auteur" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option value="oui"
                        {{ old('auteur') == 'oui' ? 'selected' : (session('participant_data.auteur') == 'oui' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                    </option>
                    <option value="non"
                        {{ old('auteur') == 'non' ? 'selected' : (session('participant_data.auteur') == 'non' ? 'selected' : '') }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}
                    </option>
                </select>
                @error('auteur')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-passport"></i>
                    {{ __('registration.step3.fields.num_passeport') }}
                </label>
                <input type="text" class="form-control @error('num_passeport') is-invalid @enderror"
                    name="num_passeport" placeholder="{{ __('registration.step3.placeholders.num_passeport') }}"
                    value="{{ old('num_passeport', session('participant_data.num_passeport')) }}" required>
                @error('num_passeport')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2 text-center">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-image"></i>
                    {{ __('registration.step3.fields.photo_passeport') }}
                </label>
                <input type="file" class="form-control @error('photo_passeport') is-invalid @enderror"
                    name="photo_passeport" accept="image/*" required>
                @error('photo_passeport')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2" style="float:right">
                <div class="alert text-center" style="border: 2px solid #0121a0">
                    <p>
                        <span style="font-size: 1.5em;font-weight: bold;color:black">Amount to pay :</span><br>
                        <span id="amount2" class="text-danger" style="font-size: 1.7em;font-weight: bold">
                            {{ old('total_amount', session('participant_data.total_amount', 0)) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Montant total -->
    <input type="hidden" name="total_amount" id="total_amount"
        value="{{ old('total_amount', session('participant_data.total_amount', 0)) }}">

    <div class="box-footer">
        <div class="navigation-buttons">
            <a href="{{ route('voyager.dashboard') }}" class="btn btn-outline btn-danger">
                <i class="bi bi-arrow-left"></i> {{ app()->getLocale() == 'fr' ? 'Annuler' : 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary">
                {{ app()->getLocale() == 'fr' ? 'Enregistrer ' : 'Save' }} <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</form>



<!-- Table -->

<div class="table-responsive">
    <table class="table table-striped table-hover table-condensed">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>#</th>
                <th style="width:50px;">#</th>
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
                        <input type="checkbox" name="participant_ids[]" value="{{ $participant->id }}">
                    </td>
                    <td style="font-wheight: bold;font-size:14px">
                        {{ $loop->iteration }}
                    </td>
                    <td>{{ $participant->fname }}</td>
                    <td>{{ $participant->lname }}</td>
                    <td>{{ $participant->gender->translate(app()->getLocale(), 'fallbackLocale')->libelle ?? '' }}
                    </td>
                    <td>{{ $participant->email ?? '' }}</td>
                    <td>{{ $participant->organisation ?? '' }}</td>
                    <td>{{ $participant->job ?? '' }}</td>

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
                            @if ($congres->currency == 'EUR')
                                {{ $invoice->total_amount ?? 0 . ' €' }}
                            @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                                {{ '$ ' . $invoice->total_amount ?? 0 }}
                            @else
                                {{ $invoice->total_amount ?? 0 . ' ' . $congres->currency }}
                            @endif
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
                    <td class="text-right">
                        <button class="btn btn-info btn-xs view-details" data-id="{{ $participant->id }}"
                            data-fname="{{ $participant->fname }}" data-lname="{{ $participant->lname }}"
                            data-email="{{ $participant->email }}" data-phone="{{ $participant->phone }}"
                            data-passeport="{{ $participant->passeport_number }}"
                            data-photo="{{ $participant->passeport_pdf }}"
                            data-title="{{ $participant->civility->libelle ?? '' }}"
                            data-gender="{{ $participant->gender->libelle ?? '' }}"
                            data-education="{{ $participant->studentLevel->libelle ?? '' }}"
                            data-country="{{ app()->getLocale() == 'fr' ? $participant->nationality->libelle_fr ?? '' : $participant->nationality->libelle_en ?? '' }}"
                            data-organisation="{{ $participant->organisation }}"
                            data-type-organisation="{{ $participant->organisation_type_other ?: $participant->organisation_type->libelle ?? '' }}"
                            data-fonction="{{ $participant->job }}"
                            data-category="{{ $participant->participantCategory->libelle ?? '' }}"
                            data-membership="{{ $participant->typeMember->libelle ?? '' }}"
                            data-membershipcode="{{ $participant->membership_code }}"
                            data-diner="{{ $participant->diner }}" data-visite="{{ $participant->visite }}"
                            data-lettre="{{ $participant->invitation_letter }}"
                            data-auteur="{{ $participant->author }}"
                            data-total-amount="{{ $invoice->total_amount ?? 0 }}"
                            data-status="{{ $invoice->status ?? $participant->status }}"
                            data-edit-url="{{ route('participant.edit', $participant->uuid ?? '') }}"
                            title="{{ app()->getLocale() == 'fr' ? 'Voir les détails' : 'View details' }}">
                            <i class="glyphicon glyphicon-eye-open"></i>
                            {{ app()->getLocale() == 'fr' ? 'Détails' : 'Details' }}
                        </button>

                        <a href="{{ route('participant.edit', $participant->uuid ?? '') }}"
                            class="btn btn-warning btn-xs">
                            <i class="glyphicon glyphicon-edit"></i>
                            {{ app()->getLocale() == 'fr' ? 'Modifier' : 'Edit' }}
                        </a>
                        <button class="btn btn-danger btn-xs delete-participant" data-id="{{ $participant->id }}"
                            data-name="{{ $participant->fname }} {{ $participant->lname }}"
                            data-url="{{ route('participant.destroy', $participant->uuid ?? '') }}"
                            title="{{ app()->getLocale() == 'fr' ? 'Supprimer ce participant' : 'Delete this participant' }}">
                            <i class="glyphicon glyphicon-trash"></i>
                            {{ app()->getLocale() == 'fr' ? 'Supprimer' : 'Delete' }}
                        </button>
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

    {{-- <button type="submit" class="btn btn-primary mt-3">
        <i class="bi bi-file-earmark-pdf"></i> Générer la facture groupée (PDF)
    </button> --}}
</div>

{{-- Modal for participant --}}
<!-- Modal pour les détails du participant -->
<div class="modal fade" id="participantDetailsModal" tabindex="-1" role="dialog"
    aria-labelledby="participantDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="participantDetailsModalLabel">
                    <i class="bi bi-person-badge"></i>
                    {{ app()->getLocale() == 'fr' ? 'Détails du participant' : 'Participant Details' }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- En-tête avec photo et info basique -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div id="detail-photo" class="participant-photo-container mb-3">
                                <img id="detail-photo-img" src="" alt="Photo passeport"
                                    class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <div id="detail-no-photo" class="no-photo-placeholder hidden">
                                    <i class="bi bi-camera" style="font-size: 3rem; color: #6c757d;"></i>
                                    <p class="text-muted mt-2">
                                        {{ app()->getLocale() == 'fr' ? 'Aucune photo' : 'No photo' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h4 id="detail-fullname" class="text-primary mb-2"></h4>
                            <p class="mb-1">
                                <strong><i class="bi bi-envelope"></i> Email:</strong>
                                <span id="detail-email" class="text-muted"></span>
                            </p>
                            <p class="mb-1">
                                <strong><i class="bi bi-telephone"></i> Téléphone:</strong>
                                <span id="detail-phone" class="text-muted"></span>
                            </p>
                            <p class="mb-1">
                                <strong><i class="bi bi-passport"></i> Passeport:</strong>
                                <span id="detail-passeport" class="text-muted"></span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Colonne 1: Informations personnelles -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-person-lines-fill"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Informations personnelles' : 'Personal Information' }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="font-weight-bold" style="width: 40%">
                                                {{ app()->getLocale() == 'fr' ? 'Civilité' : 'Title' }}:
                                            </td>
                                            <td id="detail-title" class="text-muted"></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ app()->getLocale() == 'fr' ? 'Genre' : 'Gender' }}:
                                            </td>
                                            <td id="detail-gender" class="text-muted"></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ app()->getLocale() == 'fr' ? 'Niveau d\'étude' : 'Education Level' }}:
                                            </td>
                                            <td id="detail-education" class="text-muted"></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ app()->getLocale() == 'fr' ? 'Nationalité' : 'Nationality' }}:
                                            </td>
                                            <td id="detail-country" class="text-muted"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne 2: Informations professionnelles -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-briefcase"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Informations professionnelles' : 'Professional Information' }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="font-weight-bold" style="width: 40%">
                                                {{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}:
                                            </td>
                                            <td id="detail-organisation" class="text-muted"></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ app()->getLocale() == 'fr' ? 'Type d\'organisation' : 'Organization Type' }}:
                                            </td>
                                            <td id="detail-type-organisation" class="text-muted"></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ app()->getLocale() == 'fr' ? 'Fonction' : 'Position' }}:
                                            </td>
                                            <td id="detail-fonction" class="text-muted"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations du congrès -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Informations du congrès' : 'Congress Information' }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        {{ app()->getLocale() == 'fr' ? 'Catégorie' : 'Category' }}:
                                                    </td>
                                                    <td id="detail-category" class="text-muted"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        {{ app()->getLocale() == 'fr' ? 'Adhésion' : 'Membership' }}:
                                                    </td>
                                                    <td id="detail-membership" class="text-muted"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        {{ app()->getLocale() == 'fr' ? 'Code adhésion' : 'Membership Code' }}:
                                                    </td>
                                                    <td id="detail-membershipcode" class="text-muted">
                                                        <span class="badge badge-secondary"
                                                            id="detail-membershipcode-badge">
                                                            {{ app()->getLocale() == 'fr' ? 'Non renseigné' : 'Not provided' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-4">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        {{ app()->getLocale() == 'fr' ? 'Dîner gala' : 'Gala Dinner' }}:
                                                    </td>
                                                    <td>
                                                        <span id="detail-diner" class="badge badge-success">
                                                            <i class="bi bi-check-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Oui' : 'Yes' }}
                                                        </span>
                                                        <span id="detail-no-diner" class="badge badge-danger hidden">
                                                            <i class="bi bi-x-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Non' : 'No' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        {{ app()->getLocale() == 'fr' ? 'Visite touristique' : 'Tourist Visit' }}:
                                                    </td>
                                                    <td>
                                                        <span id="detail-visite" class="badge badge-success">
                                                            <i class="bi bi-check-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Oui' : 'Yes' }}
                                                        </span>
                                                        <span id="detail-no-visite" class="badge badge-danger hidden">
                                                            <i class="bi bi-x-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Non' : 'No' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-4">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        {{ app()->getLocale() == 'fr' ? 'Lettre invitation' : 'Invitation Letter' }}:
                                                    </td>
                                                    <td>
                                                        <span id="detail-lettre" class="badge badge-success">
                                                            <i class="bi bi-check-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Oui' : 'Yes' }}
                                                        </span>
                                                        <span id="detail-no-lettre" class="badge badge-danger hidden">
                                                            <i class="bi bi-x-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Non' : 'No' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        {{ app()->getLocale() == 'fr' ? 'Auteur' : 'Author' }}:
                                                    </td>
                                                    <td>
                                                        <span id="detail-auteur" class="badge badge-success">
                                                            <i class="bi bi-check-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Oui' : 'Yes' }}
                                                        </span>
                                                        <span id="detail-no-auteur" class="badge badge-danger hidden">
                                                            <i class="bi bi-x-circle"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Non' : 'No' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de facturation -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="bi bi-credit-card"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Informations de facturation' : 'Billing Information' }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong>{{ app()->getLocale() == 'fr' ? 'Montant total:' : 'Total Amount:' }}</strong>
                                                <span id="detail-total-amount" class="h5 text-success ml-2"></span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">
                                                <strong>{{ app()->getLocale() == 'fr' ? 'Statut:' : 'Status:' }}</strong>
                                                <span id="detail-status" class="badge badge-success ml-2"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i>
                    {{ app()->getLocale() == 'fr' ? 'Fermer' : 'Close' }}
                </button>
                <a id="detail-edit-link" href="#" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i>
                    {{ app()->getLocale() == 'fr' ? 'Modifier' : 'Edit' }}
                </a>
            </div>
        </div>
    </div>
</div>
{{-- End modal --}}

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Configuration globale de SweetAlert
            const SwalTheme = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary mx-2'
                },
                buttonsStyling: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });

            // =============================================
            // GESTION DES MESSAGES DE SESSION (SUCCESS, ERROR, etc.)
            // =============================================

            // Messages de session pour les retours de création/édition
            @if (session('success'))
                SwalTheme.fire({
                    icon: 'success',
                    title: '{{ app()->getLocale() == 'fr' ? 'Succès !' : 'Success!' }}',
                    text: '{{ session('success') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'OK' : 'OK' }}',
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            @if (session('error'))
                SwalTheme.fire({
                    icon: 'error',
                    title: '{{ app()->getLocale() == 'fr' ? 'Erreur !' : 'Error!' }}',
                    text: '{{ session('error') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'Compris' : 'Understood' }}',
                });
            @endif

            @if (session('warning'))
                SwalTheme.fire({
                    icon: 'warning',
                    title: '{{ app()->getLocale() == 'fr' ? 'Attention !' : 'Warning!' }}',
                    text: '{{ session('warning') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'Compris' : 'Understood' }}',
                });
            @endif

            @if (session('info'))
                SwalTheme.fire({
                    icon: 'info',
                    title: '{{ app()->getLocale() == 'fr' ? 'Information' : 'Information' }}',
                    text: '{{ session('info') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'OK' : 'OK' }}',
                });
            @endif

            // Messages SweetAlert personnalisés depuis la session
            @if (session('swal'))
                SwalTheme.fire({
                    icon: '{{ session('swal.icon') }}',
                    title: '{{ session('swal.title') }}',
                    text: '{{ session('swal.text') }}',
                    confirmButtonText: '{{ session('swal.confirmButtonText', app()->getLocale() == 'fr' ? 'OK' : 'OK') }}',
                    @if (session('swal.showCancelButton'))
                        showCancelButton: true,
                        cancelButtonText: '{{ session('swal.cancelButtonText', app()->getLocale() == 'fr' ? 'Annuler' : 'Cancel') }}',
                    @endif
                    @if (session('swal.timer'))
                        timer: {{ session('swal.timer') }},
                        timerProgressBar: true,
                    @endif
                    @if (session('swal.html'))
                        html: {!! session('swal.html') !!},
                    @endif
                });
            @endif

            // =============================================
            // GESTION DE LA SUPPRESSION DES PARTICIPANTS
            // =============================================

            $('.delete-participant').on('click', function(e) {
                e.preventDefault();

                const participantId = $(this).data('id');
                const participantName = $(this).data('name');
                const deleteUrl = $(this).data('url');
                const isFrench = '{{ app()->getLocale() }}' === 'fr';

                SwalTheme.fire({
                    title: isFrench ? 'Êtes-vous sûr ?' : 'Are you sure?',
                    html: isFrench ?
                        `Vous êtes sur le point de supprimer le participant :<br><strong>"${participantName}"</strong><br><span class="text-danger">Cette action est irréversible !</span>` :
                        `You are about to delete participant:<br><strong>"${participantName}"</strong><br><span class="text-danger">This action cannot be undone!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: isFrench ? 'Oui, supprimer !' : 'Yes, delete it!',
                    cancelButtonText: isFrench ? 'Annuler' : 'Cancel',
                    reverseButtons: true,
                    focusCancel: true,
                    width: '500px',
                    padding: '2em'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Afficher l'indicateur de chargement
                        SwalTheme.fire({
                            title: isFrench ? 'Suppression en cours...' : 'Deleting...',
                            text: isFrench ? 'Veuillez patienter.' : 'Please wait.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            didOpen: () => {
                                SwalTheme.showLoading();
                            }
                        });

                        // Envoyer la requête DELETE
                        $.ajax({
                            url: deleteUrl,
                            type: 'GET',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Succès - recharger la page
                                    SwalTheme.fire({
                                        icon: 'success',
                                        title: isFrench ? 'Supprimé !' :
                                            'Deleted!',
                                        text: response.message,
                                        confirmButtonText: isFrench ? 'OK' :
                                            'OK',
                                        timer: 3000,
                                        timerProgressBar: true,
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    // Erreur métier
                                    SwalTheme.fire({
                                        icon: 'error',
                                        title: isFrench ? 'Erreur !' : 'Error!',
                                        text: response.message,
                                        confirmButtonText: isFrench ?
                                            'Compris' : 'Understood'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = isFrench ?
                                    'Une erreur est survenue lors de la suppression.' :
                                    'An error occurred while deleting.';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                SwalTheme.fire({
                                    icon: 'error',
                                    title: isFrench ? 'Erreur !' : 'Error!',
                                    text: errorMessage,
                                    confirmButtonText: isFrench ? 'Compris' :
                                        'Understood'
                                });
                            }
                        });
                    }
                });
            });

            // =============================================
            // GESTION DU FORMULAIRE ET CALCUL DU TOTAL
            // =============================================

            // Fonction pour obtenir le symbole selon la devise
            function getCurrencySymbol(currency) {
                if (!currency) return '';
                currency = currency.toUpperCase();
                switch (currency) {
                    case 'EUR':
                        return '€';
                    case 'USD':
                    case 'US':
                        return '$';
                    default:
                        return ' ' + currency;
                }
            }

            // Fonction de calcul du total
            function calculateTotal() {
                let total = 0;
                let selectedCurrency = null;

                // Membership
                let membershipOption = $('#membership option:selected');
                let membershipAmount = parseFloat(membershipOption.data('amount')) || 0;
                let membershipCurrency = membershipOption.data('currency');
                total += membershipAmount;
                selectedCurrency = membershipCurrency || selectedCurrency;

                // Dîner gala
                let dinerOption = $('#diner_gala option:selected');
                let dinerAmount = parseFloat(dinerOption.data('amount')) || 0;
                let dinerCurrency = dinerOption.data('currency');
                total += dinerAmount;
                selectedCurrency = dinerCurrency || selectedCurrency;

                // Visite touristique
                let visiteOption = $('#visite_touristique option:selected');
                let visiteAmount = parseFloat(visiteOption.data('amount')) || 0;
                let visiteCurrency = visiteOption.data('currency');
                total += visiteAmount;
                selectedCurrency = visiteCurrency || selectedCurrency;

                // Affichage dans le span et champ hidden
                if (total > 0) {
                    const symbol = getCurrencySymbol(selectedCurrency);
                    $('#amount2').text(total.toLocaleString('fr-FR') + ' ' + symbol);
                    $('#total_amount').val(total);
                } else {
                    $('#amount2').text('0');
                    $('#total_amount').val(0 + ' ' + symbol);
                }
            }

            // Gestion du type d'organisation "autre"
            function toggleAutreTypeOrg() {
                const typeOrg = $('#type_organisation').val();
                if (typeOrg == 10) {
                    $('#autre_type_org_div').removeClass('hidden');
                    $('#autre_type_org').attr('required', true);
                } else {
                    $('#autre_type_org_div').addClass('hidden');
                    $('#autre_type_org').removeAttr('required').val('');
                }
            }


            // Afficher ou masquer le code d'adhésion
            function toggleMembershipCode() {
                const requireCode = $('#membership').val();
                const $codeRow = $('#membershipcode_div');
                const $codeInput = $('#membershipcode');

                if (requireCode == 1) {
                    $codeRow.removeClass('hidden').slideDown(200);
                    $codeInput.attr('required', true);
                } else {
                    $codeRow.slideUp(200);
                    $codeInput.removeAttr('required').val('');
                }
            }

            // Événements pour le calcul automatique
            $('#membership, #diner_gala, #visite_touristique').on('change', function() {
                calculateTotal();
            });

            $('#type_organisation').on('change', function() {
                toggleAutreTypeOrg();
            });

            $('#membership').on('change', function() {
                toggleMembershipCode();
            });

            // Initialisation au chargement de la page
            calculateTotal();
            toggleAutreTypeOrg();
            toggleMembershipCode();

            // =============================================
            // VALIDATION DU FORMULAIRE AVEC SWEETALERT
            // =============================================

            $('form').on('submit', function(e) {
               
                // Vérification des champs requis

                let valid = true;

                // vider les erreurs précédentes
                $(".is-invalid").removeClass("is-invalid");
                $(".error-text").remove();

                // Vérifier tous les champs required
                $(this).find("[required]").each(function() {
                    if ($(this).val() === "" || $(this).val() === null) {
                        valid = false;
                        $(this).addClass("is-invalid");

                        // Ajouter un message d'erreur si pas encore
                        $(this).after(
                            '<small class="text-danger error-text">Ce champ est obligatoire</small>'
                        );
                    }
                });

                if (!valid) {
                    e.preventDefault(); // bloque la soumission
                    alert("Veuillez remplir tous les champs obligatoires.");
                }

                // Vérification spécifique pour le type d'organisation "autre"
                if ($('#type_organisation').val() == '10' && !$('#autre_type_org').val().trim()) {
                    e.preventDefault();
                    const isFrench = '{{ app()->getLocale() }}' === 'fr';

                    SwalTheme.fire({
                        icon: 'warning',
                        title: isFrench ? 'Champ manquant' : 'Missing field',
                        text: isFrench ?
                            'Veuillez spécifier le type d\'organisation.' :
                            'Please specify the organization type.',
                        confirmButtonText: isFrench ? 'Compris' : 'Understood'
                    });
                    return false;
                }

                // Vérification du montant total
                const totalAmount = parseFloat($('#total_amount').val()) || 0;
                if (totalAmount <= 0) {
                    e.preventDefault();
                    const isFrench = '{{ app()->getLocale() }}' === 'fr';

                    SwalTheme.fire({
                        icon: 'warning',
                        title: isFrench ? 'Montant invalide' : 'Invalid amount',
                        text: isFrench ?
                            'Le montant total doit être supérieur à 0.' :
                            'Total amount must be greater than 0.',
                        confirmButtonText: isFrench ? 'Compris' : 'Understood'
                    });
                    return false;
                }

                return true;
            });

            // =============================================
            // GESTION DU TÉLÉPHONE INTERNATIONAL
            // =============================================

            // Initialisation du input téléphone si vous utilisez intl-tel-input
            if (typeof intlTelInput !== 'undefined') {
                const phoneInput = document.querySelector(".telephone-input");
                if (phoneInput) {
                    const iti = intlTelInput(phoneInput, {
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                        separateDialCode: true,
                        preferredCountries: ['fr', 'us', 'gb'],
                        initialCountry: "auto",
                        geoIpLookup: function(callback) {
                            fetch("https://ipapi.co/json")
                                .then(function(res) {
                                    return res.json();
                                })
                                .then(function(data) {
                                    callback(data.country_code);
                                })
                                .catch(function() {
                                    callback("fr");
                                });
                        }
                    });

                    phoneInput.addEventListener("countrychange", function() {
                        $('#telephone').val(iti.getNumber());
                    });

                    phoneInput.addEventListener("input", function() {
                        $('#telephone').val(iti.getNumber());
                    });
                }
            }
        });
    </script>

    <script>
        // =============================================
        // GESTION DU MODAL DE DÉTAILS AVEC DATA-ATTRIBUTES
        // =============================================

        $('.view-details').on('click', function(e) {
            e.preventDefault();

            const button = $(this);
            const isFrench = '{{ app()->getLocale() }}' === 'fr';

            // Récupérer toutes les données depuis les attributs data-
            const participantData = {
                id: button.data('id'),
                fname: button.data('fname'),
                lname: button.data('lname'),
                email: button.data('email'),
                phone: button.data('phone'),
                passeport_number: button.data('passeport'),
                passeport_pdf: button.data('photo'),
                civility: {
                    libelle: button.data('title')
                },
                gender: {
                    libelle: button.data('gender')
                },
                student_level: {
                    libelle: button.data('education')
                },
                nationality: {
                    libelle_fr: isFrench ? button.data('country') : '',
                    libelle_en: !isFrench ? button.data('country') : ''
                },
                organisation: button.data('organisation'),
                organisation_type_other: button.data('type-organisation'),
                organisation_type: {
                    libelle: button.data('type-organisation')
                },
                job: button.data('fonction'),
                participant_category: {
                    libelle: button.data('category')
                },
                type_member: {
                    libelle: button.data('membership')
                },
                membership_code: button.data('membershipcode'),
                diner: button.data('diner'),
                visite: button.data('visite'),
                invitation_letter: button.data('lettre'),
                author: button.data('auteur'),
                status: button.data('status')
            };

            const invoiceData = {
                total_amount: button.data('total-amount'),
                status: button.data('status')
            };

            // Remplir les informations du modal
            fillParticipantDetails(participantData, invoiceData, isFrench, button.data('edit-url'));

            // Afficher le modal
            $('#participantDetailsModal').modal('show');
        });

        function fillParticipantDetails(participant, invoice, isFrench, editUrl) {
            // Informations basiques
            $('#detail-fullname').text(`${participant.fname} ${participant.lname}`);
            $('#detail-email').text(participant.email || '-');
            $('#detail-phone').text(participant.phone || '-');
            $('#detail-passeport').text(participant.passeport_number || '-');

            // Photo du passeport
            if (participant.passeport_pdf) {
                $('#detail-photo-img').attr('src', `/storage/${participant.passeport_pdf}`).removeClass('hidden');
                $('#detail-no-photo').addClass('hidden');
            } else {
                $('#detail-photo-img').addClass('hidden');
                $('#detail-no-photo').removeClass('hidden');
            }

            // Informations personnelles
            $('#detail-title').text(participant.civility?.libelle || '-');
            $('#detail-gender').text(participant.gender?.libelle || '-');
            $('#detail-education').text(participant.student_level?.libelle || '-');
            $('#detail-country').text(participant.nationality ?
                (isFrench ? participant.nationality.libelle_fr : participant.nationality.libelle_en) : '-');

            // Informations professionnelles
            $('#detail-organisation').text(participant.organisation || '-');
            $('#detail-type-organisation').text(participant.organisation_type_other || participant.organisation_type
                ?.libelle || '-');
            $('#detail-fonction').text(participant.job || '-');

            // Informations du congrès
            $('#detail-category').text(participant.participant_category?.libelle || '-');
            $('#detail-membership').text(participant.type_member?.libelle || '-');

            // Code d'adhésion
            if (participant.membership_code) {
                $('#detail-membershipcode').text(participant.membership_code);
                $('#detail-membershipcode-badge').addClass('hidden');
            } else {
                $('#detail-membershipcode').text('');
                $('#detail-membershipcode-badge').removeClass('hidden').text(isFrench ? 'Non renseigné' : 'Not provided');
            }

            // Options Oui/Non
            toggleYesNoField('diner', participant.diner, isFrench);
            toggleYesNoField('visite', participant.visite, isFrench);
            toggleYesNoField('lettre', participant.invitation_letter, isFrench);
            toggleYesNoField('auteur', participant.author, isFrench);

            // Informations de facturation
            if (invoice && invoice.total_amount > 0) {
                const currency = '{{ $congres->currency }}' || 'EUR';
                const amount = formatCurrency(invoice.total_amount, currency);
                $('#detail-total-amount').text(amount);

                // Statut
                const status = invoice.status || participant.status;
                const statusText = isFrench ? getFrenchStatus(status) : getEnglishStatus(status);
                const statusClass = getStatusClass(status);
                $('#detail-status').text(statusText).removeClass('badge-success badge-warning badge-danger badge-secondary')
                    .addClass(statusClass);
            } else {
                $('#detail-total-amount').text(isFrench ? 'Non facturé' : 'Not invoiced');
                $('#detail-status').text(isFrench ? 'En attente' : 'Pending').removeClass(
                    'badge-success badge-warning badge-danger badge-secondary').addClass('badge-warning');
            }

            // Lien d'édition
            if (editUrl) {
                $('#detail-edit-link').attr('href', editUrl).removeClass('hidden');
            } else {
                $('#detail-edit-link').addClass('hidden');
            }
        }

        function toggleYesNoField(field, value, isFrench) {
            const yesText = isFrench ? 'Oui' : 'Yes';
            const noText = isFrench ? 'Non' : 'No';

            if (value === 'oui') {
                $(`#detail-${field}`).removeClass('hidden').html(
                    `<i class="glyphicon glyphicon-ok-circle"></i> ${yesText}`);
                $(`#detail-no-${field}`).addClass('hidden');
            } else {
                $(`#detail-${field}`).addClass('hidden');
                $(`#detail-no-${field}`).removeClass('hidden').html(
                    `<i class="glyphicon glyphicon-remove-circle"></i> ${noText}`);
            }
        }

        function formatCurrency(amount, currency) {
            amount = parseFloat(amount) || 0;
            switch (currency.toUpperCase()) {
                case 'EUR':
                    return `${amount.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €`;
                case 'USD':
                case 'US':
                    return `$${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                default:
                    return `${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${currency}`;
            }
        }

        function getFrenchStatus(status) {
            if (!status) return 'Inconnu';

            switch (status.toLowerCase()) {
                case 'paid':
                case 'payé':
                    return 'Payé';
                case 'pending':
                case 'en attente':
                    return 'En attente';
                case 'cancelled':
                case 'annulé':
                    return 'Annulé';
                case 'draft':
                case 'brouillon':
                    return 'Brouillon';
                default:
                    return status;
            }
        }

        function getEnglishStatus(status) {
            if (!status) return 'Unknown';

            switch (status.toLowerCase()) {
                case 'paid':
                case 'payé':
                    return 'Paid';
                case 'pending':
                case 'en attente':
                    return 'Pending';
                case 'cancelled':
                case 'annulé':
                    return 'Cancelled';
                case 'draft':
                case 'brouillon':
                    return 'Draft';
                default:
                    return status;
            }
        }

        function getStatusClass(status) {
            if (!status) return 'badge-secondary';

            switch (status.toLowerCase()) {
                case 'paid':
                case 'payé':
                    return 'badge-success';
                case 'pending':
                case 'en attente':
                    return 'badge-warning';
                case 'cancelled':
                case 'annulé':
                    return 'badge-danger';
                case 'draft':
                case 'brouillon':
                    return 'badge-info';
                default:
                    return 'badge-secondary';
            }
        }
    </script>
@endsection
