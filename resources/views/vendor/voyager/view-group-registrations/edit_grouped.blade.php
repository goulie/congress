@extends('layouts.app')

@section('content')
    @php
        $categories = App\Models\CategorieRegistrant::forCongress($congres->id);
        $dinner = App\Models\CategorieRegistrant::DinnerforCongress($congres->id);
        $accompanying = App\Models\CategorieRegistrant::accompanyingPersonForCongress($congres->id);
        $tours = App\Models\CategorieRegistrant::ToursforCongress($congres->id);
    @endphp

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="bi bi-pencil-square"></i>
                            {{ app()->getLocale() == 'fr' ? 'Modifier le participant' : 'Edit Participant' }} -
                            {{ $participant->fname }} {{ $participant->lname }}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ route('participant.update') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="uuid" value="{{ $participant->uuid }}">

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
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-person-vcard"></i>
                                            {{ __('registration.step1.fields.title') }}
                                        </label>
                                        <select class="form-control @error('title') is-invalid @enderror" name="title"
                                            required>
                                            <option value="" disabled>{{ __('registration.choose') ?? 'Select' }}
                                            </option>
                                            @forelse (App\Models\Civility::get()->translate(app()->getLocale(), 'fallbackLocale') as $civility)
                                                <option value="{{ $civility->id }}"
                                                    {{ old('title', $participant->civility_id) == $civility->id ? 'selected' : '' }}>
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
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                            name="first_name"
                                            placeholder="{{ __('registration.step1.placeholders.first_name') }}"
                                            value="{{ old('first_name', $participant->fname) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-person"></i>
                                            {{ __('registration.step1.fields.last_name') }}
                                        </label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                            name="last_name"
                                            placeholder="{{ __('registration.step1.placeholders.last_name') }}"
                                            value="{{ old('last_name', $participant->lname) }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-mortarboard"></i>
                                            {{ __('registration.step1.fields.education') }}
                                        </label>
                                        <select class="form-control @error('education') is-invalid @enderror"
                                            name="education" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (App\Models\StudentLevel::all()->translate(app()->getLocale(), 'fallbackLocale') as $studentLevel)
                                                <option value="{{ $studentLevel->id }}"
                                                    {{ old('education', $participant->student_level_id) == $studentLevel->id ? 'selected' : '' }}>
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
                                        <label class="control-label font-weight-bold text-dark">
                                            <i class="bi bi-gender-ambiguous"></i>
                                            {{ __('registration.step1.fields.gender') }}
                                        </label>
                                        <select class="form-control @error('gender') is-invalid @enderror" name="gender"
                                            required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (App\Models\Gender::get()->translate(app()->getLocale(), 'fallbackLocale') as $gender)
                                                <option value="{{ $gender->id }}"
                                                    {{ old('gender', $participant->gender_id) == $gender->id ? 'selected' : '' }}>
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
                                        <label class="control-label font-weight-bold text-dark">
                                            <i class="bi bi-globe"></i>
                                            {{ __('registration.step1.fields.country') }}
                                        </label>
                                        <select class="form-control @error('country') is-invalid @enderror" name="country"
                                            required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country', $participant->nationality_id) == $country->id ? 'selected' : '' }}>
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
                                            value="{{ old('email', $participant->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-telephone"></i>
                                            {{ __('registration.step2.fields.telephone') }}
                                        </label>
                                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                            id="telephone-input" name="telephone"
                                            placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                                            value="{{ old('telephone', $participant->phone) }}" required>
                                        <input type="hidden" id="telephone" name="telephone_complet"
                                            value="{{ old('telephone_complet', $participant->phone) }}">
                                        @error('telephone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-building"></i>
                                            {{ __('registration.step2.fields.organisation') }}
                                        </label>
                                        <input type="text"
                                            class="form-control @error('organisation') is-invalid @enderror"
                                            name="organisation"
                                            placeholder="{{ __('registration.step2.placeholders.organisation') }}"
                                            value="{{ old('organisation', $participant->organisation) }}" required>
                                        @error('organisation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-card-checklist"></i>
                                            {{ __('registration.step2.fields.type_organisation') }}
                                        </label>
                                        <select class="form-control @error('type_organisation') is-invalid @enderror"
                                            name="type_organisation" id="type_organisation" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (App\Models\TypeOrganisation::get()->translate(app()->getLocale(), 'fallbackLocale') as $typeOrganisation)
                                                <option value="{{ $typeOrganisation->id }}"
                                                    {{ old('type_organisation', $participant->organisation_type_id) == $typeOrganisation->id ? 'selected' : '' }}>
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

                                    <div class="col-md-2 {{ old('type_organisation', $participant->organisation_type_id) == 'autre' ? '' : 'd-none' }}"
                                        id="autre_type_org_div">
                                        <label class="control-label font-weight-bold text-dark">
                                            <i class="bi bi-pencil-square"></i>
                                            {{ __('registration.step2.fields.autre_type_org') }}
                                        </label>
                                        <input type="text"
                                            class="form-control @error('autre_type_org') is-invalid @enderror"
                                            name="autre_type_org"
                                            placeholder="{{ __('registration.step2.placeholders.autre_type_org') }}"
                                            value="{{ old('autre_type_org', $participant->organisation_type_other) }}">
                                        @error('autre_type_org')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-person-badge"></i>
                                            {{ __('registration.step2.fields.fonction') }}
                                        </label>
                                        <input type="text"
                                            class="form-control @error('fonction') is-invalid @enderror" name="fonction"
                                            placeholder="{{ __('registration.step2.placeholders.fonction') }}"
                                            value="{{ old('fonction', $participant->job) }}" required>
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
                                        <select class="form-control @error('category') is-invalid @enderror"
                                            name="category" id="category" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (App\Models\CategoryParticipant::get()->translate(app()->getLocale(), 'fallbackLocale') as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category', $participant->participant_category_id) == $category->id ? 'selected' : '' }}>
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
                                            <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>

                                            @forelse ($categories as $typeMember)
                                                <option data-amount="{{ $typeMember->montant }}"
                                                    data-currency="{{ $congres->currency }}"
                                                    value="{{ $typeMember->id }}"
                                                    {{ isset($participant) && $participant->type_member_id == $typeMember->id ? 'selected' : '' }}>

                                                    {{ $typeMember->libelle }} -
                                                    <strong>{{ $typeMember->montant }} {{ $congres->currency }}</strong>
                                                    ({{ $typeMember->periode }})
                                                </option>
                                            @empty
                                                <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                                            @endforelse
                                        </select>
                                        {{-- <select class="form-control @error('membership') is-invalid @enderror"
                                            name="membership" id="membership" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (App\Models\TypeMember::get()->translate(app()->getLocale(), 'fallbackLocale') as $typeMember)
                                                <option data-amount="{{ $typeMember->amount }}"
                                                    data-currency="{{ $typeMember->currency }}"
                                                    value="{{ $typeMember->id }}"
                                                    {{ old('membership', $participant->type_member_id) == $typeMember->id ? 'selected' : '' }}>
                                                    {{ $typeMember->libelle . ' - ' }}
                                                    @if ($typeMember->currency == 'EUR')
                                                        <strong>{{ $typeMember->amount }} €</strong>
                                                    @elseif (in_array($typeMember->currency, ['US', 'USD']))
                                                        <strong>${{ $typeMember->amount }}</strong>
                                                    @else
                                                        <strong>{{ $typeMember->amount }}
                                                            {{ $typeMember->currency }}</strong>
                                                    @endif
                                                </option>
                                            @empty
                                                <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                                            @endforelse
                                        </select> --}}
                                        @error('membership')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="bi bi-egg-fried"></i>
                                            {{ __('registration.step3.fields.diner_gala') }}
                                            @if ($congres->currency == 'EUR')
                                                <strong style="font-weight: bold"> {{ $dinner->montant }} €
                                                </strong>
                                            @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                                                <strong style="font-weight: bold"> ${{ $dinner->montant }}</strong>
                                            @else
                                                <strong style="font-weight: bold"> {{ $dinner->montant }}
                                                    {{ $congres->currency }}</strong>
                                            @endif
                                        </label>
                                        <select id="diner_gala"
                                            class="form-control @error('diner_gala') is-invalid @enderror"
                                            name="diner_gala" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            <option data-amount="{{ $dinner->montant }}" value="oui"
                                                {{ old('diner_gala', $participant->diner) == 'oui' ? 'selected' : '' }}>
                                                {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                                            </option>
                                            <option value="non"
                                                {{ old('diner_gala', $participant->diner) == 'non' ? 'selected' : '' }}>
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
                                                <strong style="font-weight: bold"> {{ $tours->montant }} €
                                                </strong>
                                            @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                                                <strong style="font-weight: bold"> ${{ $tours->montant }}</strong>
                                            @else
                                                <strong style="font-weight: bold"> {{ $tours->montant }}
                                                    {{ $congres->currency }}</strong>
                                            @endif
                                        </label>
                                        <select class="form-control @error('visite_touristique') is-invalid @enderror"
                                            id="visite_touristique" name="visite_touristique" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            <option value="oui" data-amount="{{ $tours->montant }}"
                                                {{ old('visite_touristique', $participant->visite) == 'oui' ? 'selected' : '' }}>
                                                {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                                            </option>
                                            <option value="non"
                                                {{ old('visite_touristique', $participant->visite) == 'non' ? 'selected' : '' }}>
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
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            <option value="oui"
                                                {{ old('lettre_invitation', $participant->invitation_letter) == 'oui' ? 'selected' : '' }}>
                                                {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                                            </option>
                                            <option value="non"
                                                {{ old('lettre_invitation', $participant->invitation_letter) == 'non' ? 'selected' : '' }}>
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
                                        <select class="form-control @error('auteur') is-invalid @enderror" name="auteur"
                                            required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            <option value="oui"
                                                {{ old('auteur', $participant->author) == 'oui' ? 'selected' : '' }}>
                                                {{ __('registration.step3.fields.oui') ?? 'Oui' }}
                                            </option>
                                            <option value="non"
                                                {{ old('auteur', $participant->author) == 'non' ? 'selected' : '' }}>
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
                                        <input type="text"
                                            class="form-control @error('num_passeport') is-invalid @enderror"
                                            name="num_passeport"
                                            placeholder="{{ __('registration.step3.placeholders.num_passeport') }}"
                                            value="{{ old('num_passeport', $participant->passeport_number) }}" required>
                                        @error('num_passeport')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2 text-center">
                                        <label class="control-label font-weight-bold text-dark">
                                            <i class="bi bi-image"></i>
                                            {{ __('registration.step3.fields.photo_passeport') }}
                                        </label>
                                        <input type="file"
                                            class="form-control @error('photo_passeport') is-invalid @enderror"
                                            name="photo_passeport" accept="image/*">
                                        @error('photo_passeport')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if ($participant->passeport_pdf)
                                            <small class="text-muted">
                                                {{ app()->getLocale() == 'fr' ? 'Fichier actuel:' : 'Current file:' }}
                                                <a href="{{ asset('storage/' . $participant->passeport_pdf) }}"
                                                    target="_blank">
                                                    {{ app()->getLocale() == 'fr' ? 'Voir' : 'View' }}
                                                </a>
                                            </small>
                                        @endif
                                    </div>

                                    <div class="col-md-8 text-center">
                                        <div class="alert text-center" style="border: 2px solid #0121a0">
                                            <p>
                                                <span style="font-size: 1.5em;font-weight: bold;color:black">Amount to pay
                                                    :</span><br>
                                                <span id="amount2" class="text-danger"
                                                    style="font-size: 1.7em;font-weight: bold">
                                                    {{ old('total_amount', 0) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Montant total -->
                            <input type="hidden" name="total_amount" id="total_amount"
                                value="{{ old('total_amount', 0) }}">

                            <div class="box-footer">
                                <div class="navigation-buttons">
                                    <a href="#" class="btn btn-outline btn-danger">
                                        <i class="bi bi-arrow-left"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Annuler' : 'Cancel' }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Mettre à jour' : 'Update' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
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

                // Affichage
                if (total > 0) {
                    const symbol = getCurrencySymbol(selectedCurrency);
                    $('#amount2').text(total.toLocaleString('fr-FR') + ' ' + symbol);
                } else {
                    $('#amount2').text('0');
                }

                $('#total_amount').val(total);
            }

            // Gestion du type d'organisation "autre"
            function toggleAutreTypeOrg() {
                const typeOrg = $('#type_organisation').val();
                if (typeOrg == 'autre') {
                    $('#autre_type_org_div').removeClass('d-none');
                    $('#autre_type_org').attr('required', true);
                } else {
                    $('#autre_type_org_div').addClass('d-none');
                    $('#autre_type_org').removeAttr('required');
                }
            }

            // Événements
            $('#type_organisation').on('change', toggleAutreTypeOrg);
            $('#membership, #diner_gala, #visite_touristique').on('change', calculateTotal);

            // Initialisation
            toggleAutreTypeOrg();
            calculateTotal();
        });
    </script>
@endsection
