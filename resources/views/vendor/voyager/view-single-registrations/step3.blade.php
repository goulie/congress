<form method="POST" action="{{ route('form.step2') }}">
    @csrf

    @php
        $levels = App\Models\TypeOrganisation::get()
            ->translate(app()->getLocale(), 'fallbackLocale')
            ->sortBy(function ($level) {
                return $level->libelle === 'Autre' || $level->libelle === 'Other' ? 'ZZZZZZZZ' : $level->libelle;
            });
    @endphp

    <div class="box-body">
        <input type="hidden" name="uuid" value="{{ $participant->uuid }}">

        {{-- Étape 2 : Coordonnées --}}
        <div class="row">
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-envelope"></i>
                    {{ __('registration.step2.fields.email') }}
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="{{ __('registration.step2.placeholders.email') }}" value="{{ auth()->user()->email }}"
                    required disabled>

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-telephone"></i>
                    {{ __('registration.step2.fields.telephone') }}
                </label>
                <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone-input"
                    name="telephone" placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                    value="{{ old('telephone', $participant->phone ?? '') }}" required>

                @error('telephone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <input type="hidden" id="telephone" name="telephone_complet">
            </div>

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-building"></i>
                    {{ __('registration.step2.fields.organisation') }}
                </label>
                <input type="text" class="form-control @error('organisation') is-invalid @enderror"
                    name="organisation" id="organisation"
                    placeholder="{{ __('registration.step2.placeholders.organisation') }}"
                    value="{{ old('organisation', $participant->organisation ?? '') }}" required>

                @error('organisation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Champ sigle (caché par défaut) -->
        <div class="col-md-4 hidden" id="sigle-container">
            <label class="control-label font-weight-bold text-dark required">
                <i class="bi bi-abbr"></i>
                {{ __('registration.sigle') }}
            </label>
            <input type="text" class="form-control text-uppercase" id="sigle" name="sigle_organisation"
                placeholder="Ex: UNESCO, UNICEF, OMS..." maxlength="10"
                @isset($participant) value="{{ old('sigle_organisation', $participant->sigle_organisation) }}" @else value="{{ old('sigle_organisation') }}" @endisset>

            <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="text-muted">
                    <span id="sigle-counter">0</span>/10 {{ __('registration.caracteres') }}
                </small>
                <small class="text-info">
                    <i class="bi bi-info-circle"></i> {{ __('registration.maj_only') }}
                </small>
            </div>
            <!-- Message d'avertissement (caché par défaut) -->
            {{-- <div id="organisation-warning" class="text-danger mt-2 p-2 hidden">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Caractères > 10, veuillez entrer un sigle
            </div> --}}
        </div>
        @php
            $levels = App\Models\TypeOrganisation::get()
                ->translate(app()->getLocale(), 'fallbackLocale')
                ->sortBy(function ($level) {
                    // Met "Autre" à la fin du tri alphabétique
                    return $level->libelle === 'Autre' || $level->libelle === 'Other' ? 'ZZZZZZZZ' : $level->libelle;
                });
        @endphp
        <div class="row" style="margin-top:15px;">
            <div class="col-md-3">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-card-checklist"></i>
                    {{ __('registration.step2.fields.type_organisation') }}
                </label>
                <select class="form-control @error('type_organisation') is-invalid @enderror" name="type_organisation"
                    id="type_organisation" required>
                    <option selected disabled value="">{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse ($levels as $typeOrganisation)
                        <option value="{{ $typeOrganisation->id }}" {{ old('type_organisation') }}
                            {{ isset($participant) && $participant->organisation_type_id == $typeOrganisation->id ? 'selected' : '' }}>
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

            <div class="col-md-3 {{ isset($participant) && $participant->organisation_type_id == 10 ? '' : 'd-none' }}"
                id="autre_type_org_div">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-pencil-square"></i>
                    {{ __('registration.step2.fields.autre_type_org') }}
                </label>
                <input type="text" class="form-control @error('autre_type_org') is-invalid @enderror"
                    id="autre_type_org" name="autre_type_org"
                    placeholder="{{ __('registration.step2.placeholders.autre_type_org') }}"
                    value="{{ old('autre_type_org', $participant->organisation_type_other ?? '') }}"
                    {{ isset($participant) && $participant->organisation_type_id == 10 ? 'required' : '' }}>
                @error('autre_type_org')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person-badge"></i>
                    {{ __('registration.step2.fields.fonction') }}
                </label>
                <input type="text" class="form-control @error('fonction') is-invalid @enderror" name="fonction"
                    placeholder="{{ __('registration.step2.placeholders.fonction') }}"
                    value="{{ old('fonction', $participant->job ?? '') }}" required>
                @error('fonction')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-globe-americas"></i>
                    {{ __('registration.step2.fields.job_country') }}
                </label>
                <select class="form-control @error('job_country') is-invalid @enderror" name="job_country" required>
                    <option selected disabled value="">{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                        <option value="{{ $country->id }}"
                            {{ old('job_country', $participant->job_country_id ?? '') == $country->id ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                        </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
                @error('job_country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

    </div>

    <div class="box-footer">
        <div class="navigation-buttons">
            <a href="{{ route('form.previous') }}" type="button" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i> {{ __('registration.step2.buttons.previous') }}
            </a>
            <button type="submit" class="btn btn-outline-success">
                {{ __('registration.step3.buttons.save_continue') }} <i class="bi bi-check-circle-fill"></i>
            </button>
        </div>
    </div>
</form>


@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.querySelector("#telephone-input");

            if (phoneInput) {
                const iti = window.intlTelInput(phoneInput, {
                    initialCountry: "auto",
                    separateDialCode: true,
                    nationalMode: false,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    //preferredCountries: ['fr', 'be', 'ch', 'de'] // Pays favoris
                    geoIpLookup: function(callback) {
                        fetch('https://ipapi.co/json/')
                            .then(res => res.json())
                            .then(data => callback(data.country_code)) // exemple : "CI", "FR", "BE"
                            .catch(() => callback('us')); // valeur par défaut si erreur
                    },


                });

                // Validation avant soumission
                const form = phoneInput.closest('form');
                form.addEventListener('submit', function(e) {


                    // Injecter le numéro complet (avec indicatif) dans un champ caché si nécessaire
                    //injecter dans id telephone

                    const fullNumber = iti.getNumber();
                    const hiddenInput = document.getElementById('telephone');
                    hiddenInput.value = fullNumber;

                });
            }
        });

        function toggleTypeOrg() {
            const typeOrg = $('#type_organisation').val();
            const $AutretypeOrg = $('#autre_type_org_div');
            const $AutretypeOrgInput = $('#autre_type_org');

            if (typeOrg == '15') {
                $AutretypeOrg.removeClass('hidden').slideDown(200);
                $AutretypeOrgInput.attr('required', true);
            } else {
                $AutretypeOrg.slideUp(200);
                $AutretypeOrgInput.removeAttr('required').val('');
            }
        }

        $('#type_organisation').on('change', toggleTypeOrg);
        $(document).ready(
            function() {
                toggleTypeOrg();
            }
        );
    </script>

    <script>
        $(document).ready(function() {
            const MAX_LENGTH = 10;
            const $orgInput = $('#organisation');
            const $orgWarning = $('#organisation-warning');
            const $sigleContainer = $('#sigle-container');
            const $sigleInput = $('#sigle');
            const $sigleCounter = $('#sigle-counter');

            // Vérifier la longueur au chargement
            checkOrganisationLength();

            // Vérifier la longueur de l'organisation
            function checkOrganisationLength() {
                const orgValue = $orgInput.val().trim();
                const orgLength = orgValue.length;

                if (orgLength > MAX_LENGTH) {
                    // Afficher l'avertissement
                    $orgWarning.removeClass('hidden');

                    // Afficher le champ sigle
                    $sigleContainer.removeClass('hidden');
                    $sigleInput.prop('required', true);

                    // Générer un sigle suggéré si vide
                    if ($sigleInput.val() === '' && orgLength <= 30) {
                        const suggestedSigle = generateSigle(orgValue);
                        $sigleInput.val(suggestedSigle);
                        updateSigleCounter();
                    }
                } else {
                    // Cacher l'avertissement et le champ sigle
                    $orgWarning.addClass('hidden');
                    $sigleContainer.addClass('hidden');
                    $sigleInput.prop('required', false);
                }
            }

            // Générer un sigle suggéré
            function generateSigle(organisation) {
                // Nettoyer et convertir en majuscules
                const words = organisation.toUpperCase()
                    .replace(/[^A-Z\s]/g, '') // Garder uniquement lettres et espaces
                    .split(/\s+/)
                    .filter(word => word.length > 2); // Ignorer mots courts

                if (words.length === 0) return '';

                let sigle = '';
                for (let word of words) {
                    if (sigle.length >= MAX_LENGTH) break;

                    // Pour les mots courts, prendre tout le mot
                    if (word.length <= 3 && sigle.length + word.length <= MAX_LENGTH) {
                        sigle += word;
                    } else {
                        // Sinon prendre la première lettre
                        sigle += word.charAt(0);
                    }
                }

                return sigle.substring(0, MAX_LENGTH);
            }

            // Forcer les majuscules dans le champ sigle
            function forceUppercase() {
                const cursorPos = this.selectionStart;
                const originalValue = $(this).val();

                // Convertir en majuscules et supprimer caractères non alphabétiques
                const uppercaseValue = originalValue.toUpperCase()
                    .replace(/[^A-Z]/g, '');

                $(this).val(uppercaseValue);

                // Restaurer la position du curseur
                this.setSelectionRange(cursorPos, cursorPos);

                updateSigleCounter();
            }

            // Mettre à jour le compteur de caractères
            function updateSigleCounter() {
                const length = $sigleInput.val().length;
                $sigleCounter.text(length);

                // Changer la couleur du compteur
                $sigleCounter.removeClass('text-success text-warning text-danger');

                if (length === MAX_LENGTH) {
                    $sigleCounter.addClass('text-danger');
                } else if (length >= MAX_LENGTH - 2) {
                    $sigleCounter.addClass('text-warning');
                } else if (length > 0) {
                    $sigleCounter.addClass('text-success');
                } else {
                    $sigleCounter.addClass('text-muted');
                }
            }

            // Événements
            $orgInput.on('input keyup blur', checkOrganisationLength);
            $sigleInput.on('input keyup', forceUppercase);

            // Initialiser le compteur
            updateSigleCounter();

            // Validation du formulaire
            $('form').on('submit', function(e) {
                const orgValue = $orgInput.val().trim();
                const sigleValue = $sigleInput.val().trim();

                // Vérifier si organisation > 10 caractères et sigle vide
                if (orgValue.length > MAX_LENGTH && sigleValue.length === 0) {
                    e.preventDefault();

                    // Afficher une alerte
                    alert(
                        'Veuillez entrer un sigle pour votre organisation (max 10 caractères en majuscules)'
                        );

                    // Focus sur le champ sigle
                    $sigleInput.focus();

                    // Ajouter une classe d'erreur
                    $sigleInput.addClass('is-invalid');

                    return false;
                }

                // Vérifier si le sigle dépasse la limite
                if (sigleValue.length > MAX_LENGTH) {
                    e.preventDefault();
                    alert(`Le sigle ne peut pas dépasser ${MAX_LENGTH} caractères`);
                    $sigleInput.focus();
                    return false;
                }

                return true;
            });

            // Supprimer la classe d'erreur quand l'utilisateur commence à taper
            $sigleInput.on('input', function() {
                $(this).removeClass('is-invalid');
            });
        });
    </script>
@endsection
