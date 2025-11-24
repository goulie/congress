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
                    name="organisation" placeholder="{{ __('registration.step2.placeholders.organisation') }}"
                    value="{{ old('organisation', $participant->organisation ?? '') }}" required>

                @error('organisation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
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
                        <option value="{{ $typeOrganisation->id }}"
                            {{ old('type_organisation')}} {{ isset($participant) && $participant->organisation_type_id == $typeOrganisation->id ? 'selected' : '' }}>
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
            <button type="submit" class="btn btn-outline">
                {{ __('registration.step2.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
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
@endsection
