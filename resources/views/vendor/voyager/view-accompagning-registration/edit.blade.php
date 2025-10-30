@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="glyphicon glyphicon-pencil"></i>
                            {{ app()->getLocale() == 'fr' ? 'Modifier la personne accompagnante' : 'Edit Accompanying Person' }}
                            - {{ $participant->fname }} {{ $participant->lname }}
                        </h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{ route('accompagning.update.participant') }}" enctype="multipart/form-data">
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
                                            <i class="glyphicon glyphicon-user"></i>
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
                                            <i class="glyphicon glyphicon-font"></i>
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
                                            <i class="glyphicon glyphicon-bold"></i>
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
                                        <label class="control-label font-weight-bold text-dark">
                                            <i class="glyphicon glyphicon-adjust"></i>
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
                                            <i class="glyphicon glyphicon-globe"></i>
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
                                            <i class="glyphicon glyphicon-envelope"></i>
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
                                            <i class="glyphicon glyphicon-phone"></i>
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

                                    {{-- Étape 3 : Détails du congrès --}}
                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="glyphicon glyphicon-tag"></i>
                                            {{ app()->getLocale() == 'fr' ? 'Type de personne' : 'Person type' }}
                                        </label>
                                        <select class="form-control @error('type_accompanying') is-invalid @enderror"
                                            name="type_accompanying" id="category" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (App\Models\AccompanyingPersonType::get()->translate(app()->getLocale(), 'fallbackLocale') as $category)
                                                <option data-amount="{{ $congres->accompagning_amount }}"
                                                    value="{{ $category->id }}"
                                                    {{ old('type_accompanying', $participant->type_accompanying_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->libelle }}
                                                </option>
                                            @empty
                                                <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                                            @endforelse
                                        </select>
                                        @error('type_accompanying')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="glyphicon glyphicon-cutlery"></i>
                                            {{ __('registration.step3.fields.diner_gala') }}
                                            @if ($congres->currency == 'EUR')
                                                <strong style="font-weight: bold"> {{ $dinner->montant }} € </strong>
                                            @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                                                <strong style="font-weight: bold"> ${{ $dinner->montant }}</strong>
                                            @else
                                                <strong style="font-weight: bold"> {{ $dinner->montant }}
                                                    {{ $congres->currency }}</strong>
                                            @endif
                                        </label>
                                        <select id="diner_gala"
                                            class="form-control @error('diner_gala') is-invalid @enderror" name="diner_gala"
                                            required>
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
                                            <i class="glyphicon glyphicon-eye-open"></i>
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
                                            <i class="glyphicon glyphicon-envelope"></i>
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
                                            <i class="glyphicon glyphicon-file"></i>
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
                                            <i class="glyphicon glyphicon-picture"></i>
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
                                                    target="_blank" class="btn btn-xs btn-info">
                                                    <i class="glyphicon glyphicon-eye-open"></i>
                                                    {{ app()->getLocale() == 'fr' ? 'Voir' : 'View' }}
                                                </a>
                                            </small>
                                        @endif
                                    </div>

                                    <div class="col-md-8 text-center">
                                        <div class="alert text-center" style="border: 2px solid #0121a0">
                                            <p>
                                                <span
                                                    style="font-size: 1.5em;font-weight: bold;color:black">{{ app()->getLocale() == 'fr' ? 'Montant à payer :' : 'Amount to pay :' }}</span><br>
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
                                    <a href="{{ route('add.accompagning.form') }}" class="btn btn-outline btn-danger">
                                        <i class="glyphicon glyphicon-arrow-left"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Annuler' : 'Cancel' }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="glyphicon glyphicon-check"></i>
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

                // Type de personne accompagnante
                let typeOption = $('#category option:selected');
                let typeAmount = parseFloat(typeOption.data('amount')) || 0;
                total += typeAmount;
                selectedCurrency = '{{ $congres->currency }}' || selectedCurrency;

                // Dîner gala
                let dinerOption = $('#diner_gala option:selected');
                let dinerAmount = parseFloat(dinerOption.data('amount')) || 0;
                total += dinerAmount;

                // Visite touristique
                let visiteOption = $('#visite_touristique option:selected');
                let visiteAmount = parseFloat(visiteOption.data('amount')) || 0;
                total += visiteAmount;

                // Affichage
                if (total > 0) {
                    const symbol = getCurrencySymbol(selectedCurrency);
                    $('#amount2').text(total.toLocaleString('fr-FR') + ' ' + symbol);
                    $('#total_amount').val(total);
                } else {
                    $('#amount2').text('0');
                    $('#total_amount').val(0);
                }
            }

            // Événements pour le calcul automatique
            $('#category, #diner_gala, #visite_touristique').on('change', function() {
                calculateTotal();
            });

            // Initialisation au chargement de la page
            calculateTotal();

            // Gestion du téléphone international
            if (typeof intlTelInput !== 'undefined') {
                const phoneInput = document.querySelector("#telephone-input");
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

                    // Pré-remplir avec la valeur existante
                    if (phoneInput.value) {
                        iti.setNumber(phoneInput.value);
                    }

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
@endsection
