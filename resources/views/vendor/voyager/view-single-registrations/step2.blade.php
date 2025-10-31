<form method="POST" action="{{ route('form.step2') }}">
    @csrf
    <div class="box-body">
        <input type="hidden" name="participant_id" value="{{ $participant->id ?? '' }}">
        {{-- Étape 2 : Coordonnées --}}
        <div class="row">
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-envelope"></i>
                    {{ __('registration.step2.fields.email') }}
                </label>
                <input type="email" class="form-control"
                    placeholder="{{ __('registration.step2.placeholders.email') }}" value="{{ auth()->user()->email }}"
                    required disabled>
            </div>
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-telephone"></i>
                    {{ __('registration.step2.fields.telephone') }}
                </label>

                <input type="tel" class="form-control" id="telephone-input" name="telephone"
                    placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                    @isset($participant) value="{{ $participant->phone }}" @endisset required>

                <input type="hidden" id="telephone" name="telephone_complet">
            </div>
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-building"></i>
                    {{ __('registration.step2.fields.organisation') }}
                </label>
                <input type="text" class="form-control" name="organisation"
                    placeholder="{{ __('registration.step2.placeholders.organisation') }}"
                    @isset($participant) value="{{ $participant->organisation }}" @endisset required>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-card-checklist"></i>
                    {{ __('registration.step2.fields.type_organisation') }}
                </label>
                <select class="form-control" name="type_organisation" id="type_organisation" required>
                    <option disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\TypeOrganisation::get()->translate(app()->getLocale(), 'fallbackLocale') as $typeOrganisation)
                        <option value="{{ $typeOrganisation->id }}"
                            {{ isset($participant) && $participant->organisation_type_id == $typeOrganisation->id ? 'selected' : '' }}>
                            {{ $typeOrganisation->libelle }}</option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
            </div>
            <div class="col-md-4 hidden {{ isset($participant) && $participant->organisation_type_id == 10 ? '' : 'd-none' }}"
                id="autre_type_org_div">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-pencil-square"></i>
                    {{ __('registration.step2.fields.autre_type_org') }}
                </label>
                <input type="text" class="form-control" id="autre_type_org" name="autre_type_org"
                    placeholder="{{ __('registration.step2.placeholders.autre_type_org') }}"
                    @isset($participant) value="{{ $participant->organisation_type_other }}" @endisset
                    {{ isset($participant) && $participant->organisation_type_id == 10 ? 'required' : '' }}>
            </div>
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person-badge"></i>
                    {{ __('registration.step2.fields.fonction') }}
                </label>
                <input type="text" class="form-control" name="fonction"
                    placeholder="{{ __('registration.step2.placeholders.fonction') }}"
                    @isset($participant) value="{{ $participant->job }}" @endisset required required>
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
                    console.log('Numéro complet:', fullNumber);
                });
            }
        });

        function toggleTypeOrg() {
            const typeOrg = $('#type_organisation').val();
            const $AutretypeOrg = $('#autre_type_org_div');
            const $AutretypeOrgInput = $('#autre_type_org');

            if (typeOrg == 10) {
                $AutretypeOrg.removeClass('hidden').slideDown(200);
                $AutretypeOrgInput.attr('required', true);
            } else {
                $AutretypeOrg.slideUp(200);
                $AutretypeOrgInput.removeAttr('required').val('');
            }
        }

        $('#type_organisation').on('change', toggleTypeOrg);
        $(document).ready(toggleMembershipCode);
    </script>
@endsection
