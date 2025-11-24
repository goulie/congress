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
                        <form method="POST" action="{{ route('accompagning.update.participant') }}"
                            enctype="multipart/form-data">
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
                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="glyphicon glyphicon-tag"></i>
                                            {{ app()->getLocale() == 'fr' ? 'Type de personne' : 'Person type' }}
                                        </label>
                                        <select class="form-control @error('type_accompanying') is-invalid @enderror"
                                            name="type_accompanying" id="category" required>
                                            <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                                            @forelse (App\Models\AccompanyingPersonType::get()->translate(app()->getLocale(), 'fallbackLocale') as $category)
                                                <option data-amount="{{ $accompanying->montant }}"
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

                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
                                        <label class="control-label font-weight-bold text-dark required">
                                            <i class="glyphicon glyphicon-eye-open"></i>
                                            {{ __('registration.step3.fields.visite_technical') }}
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

                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
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
                                                <a href="{{ asset(Voyager::image($participant->passeport_pdf)) }}"
                                                    target="_blank" class="btn btn-xs btn-info">
                                                    <i class="glyphicon glyphicon-eye-open"></i>
                                                    {{ app()->getLocale() == 'fr' ? 'Voir' : 'View' }}
                                                </a>
                                            </small>
                                        @endif
                                    </div>

                                    <div class="col-md-6 text-center">
                                        <div class="alert alert-white text-center">
                                            <p>
                                                <span
                                                    style="font-size: 1.5em;font-weight: bold;color:black">{{ app()->getLocale() == 'fr' ? 'Montant à payer :' : 'Amount to pay :' }}</span><br>
                                                <span id="amount2" class="text-success"
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

    <script>
        $(document).ready(function() {
            // Configuration globale de SweetAlert
            const SwalTheme = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-secondary mx-2'
                },
                buttonsStyling: false
            });

            // =============================================
            // CALCUL DU MONTANT TOTAL AVEC LA BONNE DEVISE
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

            // Fonction pour formater le montant selon la devise
            function formatAmount(amount, currency) {
                amount = parseFloat(amount) || 0;
                const symbol = getCurrencySymbol(currency);

                if (currency === 'EUR') {
                    return amount.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' ' + symbol;
                } else if (currency === 'USD' || currency === 'US') {
                    return symbol + amount.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else {
                    return amount.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' ' + currency;
                }
            }

            // Fonction de calcul du total
            function calculateTotal() {
                let total = 0;
                const congresCurrency = '{{ $congres->currency }}'; // Devise du congrès

                // Type de personne accompagnante
                const categoryOption = $('#category option:selected');
                const categoryAmount = parseFloat(categoryOption.data('amount')) || 0;
                total += categoryAmount;

                // Dîner gala
                const dinerOption = $('#diner_gala option:selected');
                if (dinerOption.val() === 'oui') {
                    const dinerAmount = parseFloat(dinerOption.data('amount')) || 0;
                    total += dinerAmount;
                }

                // Visite touristique
                const visiteOption = $('#visite_touristique option:selected');
                if (visiteOption.val() === 'oui') {
                    const visiteAmount = parseFloat(visiteOption.data('amount')) || 0;
                    total += visiteAmount;
                }

                // Affichage dans le span et champ hidden
                const formattedAmount = formatAmount(total, congresCurrency);
                $('#amount2').text(formattedAmount);
                $('#total_amount').val(total);
            }

            // Événements pour le calcul automatique
            $('#category, #diner_gala, #visite_touristique').on('change', function() {
                calculateTotal();
            });

            // Initialisation au chargement de la page
            calculateTotal();

            // =============================================
            // GESTION DU TÉLÉPHONE INTERNATIONAL
            // =============================================

            // Initialisation du input téléphone
            const phoneInput = document.querySelector("#telephone-input");
            if (phoneInput) {
                // Récupérer le numéro existant
                const existingPhone = '{{ old('telephone', $participant->phone) }}';

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

                // Si un numéro existe déjà, le définir
                if (existingPhone) {
                    iti.setNumber(existingPhone);
                }

                phoneInput.addEventListener("countrychange", function() {
                    $('#telephone').val(iti.getNumber());
                });

                phoneInput.addEventListener("input", function() {
                    $('#telephone').val(iti.getNumber());
                });

                // Initialiser la valeur cachée
                $('#telephone').val(iti.getNumber());
            }

            // =============================================
            // VALIDATION DU FORMULAIRE
            // =============================================

            $('form').on('submit', function(e) {
                const totalAmount = parseFloat($('#total_amount').val()) || 0;
                const isFrench = '{{ app()->getLocale() }}' === 'fr';

                // Vérification des champs requis
                const requiredFields = $(this).find('[required]');
                let missingFields = [];

                requiredFields.each(function() {
                    if (!$(this).val().trim()) {
                        const fieldName = $(this).attr('name') || $(this).attr('id');
                        missingFields.push(fieldName);
                    }
                });

                if (missingFields.length > 0) {
                    e.preventDefault();
                    SwalTheme.fire({
                        icon: 'warning',
                        title: isFrench ? 'Champs manquants' : 'Missing fields',
                        html: isFrench ?
                            'Veuillez remplir tous les champs obligatoires.' :
                            'Please fill in all required fields.',
                        confirmButtonText: isFrench ? 'Compris' : 'Understood'
                    });
                    return false;
                }

                if (totalAmount <= 0) {
                    e.preventDefault();
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

                // Validation du téléphone
                if (typeof iti !== 'undefined') {
                    if (!iti.isValidNumber()) {
                        e.preventDefault();
                        SwalTheme.fire({
                            icon: 'warning',
                            title: isFrench ? 'Numéro invalide' : 'Invalid number',
                            text: isFrench ?
                                'Veuillez saisir un numéro de téléphone valide.' :
                                'Please enter a valid phone number.',
                            confirmButtonText: isFrench ? 'Compris' : 'Understood'
                        });
                        return false;
                    }
                }

                // Confirmation de mise à jour
                e.preventDefault();
                SwalTheme.fire({
                    title: isFrench ? 'Confirmer la modification' : 'Confirm update',
                    html: isFrench ?
                        'Êtes-vous sûr de vouloir modifier cette personne accompagnante ?' :
                        'Are you sure you want to update this accompanying person?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: isFrench ? 'Oui, modifier' : 'Yes, update',
                    cancelButtonText: isFrench ? 'Annuler' : 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Soumettre le formulaire
                        $(this).off('submit').submit();
                    }
                });

                return false;
            });

            // =============================================
            // GESTION DES MESSAGES DE SESSION
            // =============================================

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

            // =============================================
            // GESTION DE LA PHOTO DE PASSEPORT
            // =============================================

            // Aperçu de la nouvelle photo
            $('input[name="photo_passeport"]').on('change', function(e) {
                const file = e.target.files[0];
                const isFrench = '{{ app()->getLocale() }}' === 'fr';

                if (file) {
                    // Vérification de la taille (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        SwalTheme.fire({
                            icon: 'error',
                            title: isFrench ? 'Fichier trop volumineux' : 'File too large',
                            text: isFrench ?
                                'La photo ne doit pas dépasser 5MB.' :
                                'The photo must not exceed 5MB.',
                            confirmButtonText: isFrench ? 'Compris' : 'Understood'
                        });
                        $(this).val('');
                        return;
                    }

                    // Vérification du type
                    if (!file.type.match('image.*')) {
                        SwalTheme.fire({
                            icon: 'error',
                            title: isFrench ? 'Format invalide' : 'Invalid format',
                            text: isFrench ?
                                'Veuillez sélectionner une image.' : 'Please select an image.',
                            confirmButtonText: isFrench ? 'Compris' : 'Understood'
                        });
                        $(this).val('');
                        return;
                    }

                    // Afficher un message de confirmation
                    SwalTheme.fire({
                        icon: 'success',
                        title: isFrench ? 'Photo sélectionnée' : 'Photo selected',
                        text: isFrench ?
                            'La nouvelle photo sera mise à jour après validation.' :
                            'The new photo will be updated after validation.',
                        confirmButtonText: isFrench ? 'OK' : 'OK',
                        timer: 2000,
                        timerProgressBar: true,
                    });
                }
            });

            // =============================================
            // GESTION DU CHANGEMENT DE DEVISE D'AFFICHAGE
            // =============================================

            // Mettre à jour l'affichage des prix quand la devise change
            function updateCurrencyDisplay() {
                const congresCurrency = '{{ $congres->currency }}';
                const symbol = getCurrencySymbol(congresCurrency);

                // Mettre à jour l'affichage des labels si nécessaire
                $('.currency-display').each(function() {
                    const amount = $(this).data('amount');
                    if (amount) {
                        $(this).text(formatAmount(amount, congresCurrency));
                    }
                });
            }

            // Initialiser l'affichage des devises
            updateCurrencyDisplay();
        });
    </script>
@endsection
