<form method="POST" action="{{ route('form.step1') }}">
    @csrf

    <div class="box-body">

        {{-- Étape 1 : Renseignements personnels --}}
        <div class="row">
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark font-weight-bold text-dark"><i
                        class="bi bi-person-vcard"></i>
                    {{ __('registration.step1.fields.title') }}</label>
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
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark font-weight-bold text-dark"><i
                        class="bi bi-person"></i>
                    {{ __('registration.step1.fields.first_name') }}</label>
                <input type="text" class="form-control" name="first_name"
                    placeholder="{{ __('registration.step1.placeholders.first_name') }}"
                    @isset($participant) value="{{ $participant->fname }}" @endisset required>
            </div>
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark font-weight-bold text-dark"><i
                        class="bi bi-person"></i>
                    {{ __('registration.step1.fields.last_name') }}</label>
                <input type="text" class="form-control" name="last_name"
                    placeholder="{{ __('registration.step1.placeholders.last_name') }}"
                    @isset($participant) value="{{ $participant->lname }}" @endisset required>
            </div>


            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-mortarboard"></i>
                    {{ __('registration.step1.fields.education') }}
                </label>
                <select class="form-control" name="education" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>

                    @forelse (App\Models\StudentLevel::all()->translate(app()->getLocale(), 'fallbackLocale') as $studentLevel)
                        <option value="{{ $studentLevel->id }}"
                            {{ isset($participant) && $participant->student_level_id == $studentLevel->id ? 'selected' : '' }}>
                            {{ $studentLevel->libelle }}
                        </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
            </div>


            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark font-weight-bold text-dark"><i
                        class="bi bi-gender-ambiguous"></i>
                    {{ __('registration.step1.fields.gender') }}</label>

                <select class="form-control" name="gender" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (App\Models\Gender::get()->translate(app()->getLocale(), 'fallbackLocale') as $gender)
                        <option value="{{ $gender->id }}"
                            {{ isset($participant) && $participant->gender_id == $gender->id ? 'selected' : '' }}>
                            {{ $gender->libelle }}</option>
                    @empty
                        <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                    @endforelse
                </select>
            </div>
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark font-weight-bold text-dark"><i
                        class="bi bi-globe"></i>
                    {{ __('registration.step1.fields.country') }}</label>
                <select class="form-control" name="country" required>
                    <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    @forelse (app()->getLocale() == 'fr' ? App\Models\Country::orderBy('libelle_fr', 'asc')->get() : App\Models\Country::orderBy('libelle_en', 'asc')->get() as $country)
                        <option value="{{ $country->id }} "
                            {{ isset($participant) && $participant->nationality_id == $country->id ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }} </option>
                    @empty
                        <option disabled>No data</option>
                    @endforelse
                </select>
            </div>

            <input type="hidden" name="participant_id" value="{{ $participant->id ?? '' }}">
            {{-- Étape 2 : Coordonnées --}}
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-envelope"></i>
                    {{ __('registration.step2.fields.email') }}
                </label>
                <input type="email" class="form-control"
                    placeholder="{{ __('registration.step2.placeholders.email') }}"
                    value="{{ auth()->user()->email }}" required disabled>
            </div>
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-telephone"></i>
                    {{ __('registration.step2.fields.telephone') }}
                </label>

                <input type="tel" class="form-control" id="telephone-input" name="telephone"
                    placeholder="{{ __('registration.step2.placeholders.telephone') }}"
                    @isset($participant) value="{{ $participant->phone }}" @endisset required>

                <input type="hidden" id="telephone" name="telephone_complet">
            </div>
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-building"></i>
                    {{ __('registration.step2.fields.organisation') }}
                </label>
                <input type="text" class="form-control" name="organisation"
                    placeholder="{{ __('registration.step2.placeholders.organisation') }}"
                    @isset($participant) value="{{ $participant->organisation }}" @endisset required>
            </div>

            <div class="col-md-2">
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
            <div class="col-md-2 d-none" id="autre_type_org_div">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-pencil-square"></i>
                    {{ __('registration.step2.fields.autre_type_org') }}
                </label>
                <input type="text" class="form-control" name="autre_type_org"
                    placeholder="{{ __('registration.step2.placeholders.autre_type_org') }}"
                    @isset($participant) value="{{ $participant->organisation_type_other }}" @endisset
                    required>
            </div>
            <div class="col-md-2">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person-badge"></i>
                    {{ __('registration.step2.fields.fonction') }}
                </label>
                <input type="text" class="form-control" name="fonction"
                    placeholder="{{ __('registration.step2.placeholders.fonction') }}"
                    @isset($participant) value="{{ $participant->job }}" @endisset required required>
            </div>


            {{-- Étape 3 : Détails du congrès --}}
                <div class="col-md-2">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-person-badge"></i>
                        {{ __('registration.step3.fields.category') }}
                    </label>
                    <select class="form-control" name="category" id="category" required>
                        <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                        @forelse (App\Models\CategoryParticipant::get()->translate(app()->getLocale(), 'fallbackLocale') as $category)
                            <option value="{{ $category->id }}"
                                {{ isset($participant) && $participant->participant_category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->libelle }}
                            </option>
                        @empty
                            <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                        @endforelse
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-person"></i>
                        {{ __('registration.step3.fields.membership') }}
                    </label>
                    <select class="form-control" name="membership" id="membership" required>
                        <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>

                        @forelse (App\Models\TypeMember::get()->translate(app()->getLocale(), 'fallbackLocale') as $typeMember)
                            <option data-amount="{{ $typeMember->amount }}"
                                data-currency="{{ $typeMember->currency }}" value="{{ $typeMember->id }}"
                                {{ isset($participant) && $participant->type_member_id == $typeMember->id ? 'selected' : '' }}>

                                {{ $typeMember->libelle . ' - ' }}

                                @if ($typeMember->currency == 'EUR')
                                    <strong>{{ $typeMember->amount }} €</strong>
                                @elseif (in_array($typeMember->currency, ['US', 'USD']))
                                    <strong>${{ $typeMember->amount }}</strong>
                                @else
                                    <strong>{{ $typeMember->amount }} {{ $typeMember->currency }}</strong>
                                @endif
                            </option>
                        @empty
                            <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                        @endforelse
                    </select>
                </div>




                <div class="col-md-2">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-egg-fried"></i>
                        {{ __('registration.step3.fields.diner_gala') }}

                        @if ($congres->currency == 'EUR')
                            <strong style="font-weight: bold"> {{ $congres->amount_diner }} € </strong>
                        @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                            <strong style="font-weight: bold"> ${{ $congres->amount_diner }}</strong>
                        @else
                            <strong style="font-weight: bold"> {{ $congres->amount_diner }}
                                {{ $congres->currency }}</strong>
                        @endif

                    </label>
                    <select id="diner_gala" class="form-control" name="diner_gala" required>
                        <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                        <option data-amount ="{{ $congres->amount_diner }}" value="oui"
                            {{ isset($participant) && $participant->diner == 'oui' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                        <option value="non"
                            {{ isset($participant) && $participant->diner == 'non' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                    </select>
                </div>
            

                <div class="col-md-2">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-binoculars"></i>
                        {{ __('registration.step3.fields.visite_touristique') }}
                    </label>
                    @if ($congres->currency == 'EUR')
                        <strong style="font-weight: bold"> {{ $congres->amount_visit }} € </strong>
                    @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                        <strong style="font-weight: bold"> ${{ $congres->amount_visit }}</strong>
                    @else
                        <strong style="font-weight: bold"> {{ $congres->amount_visit }}
                            {{ $congres->currency }}</strong>
                    @endif

                    <select class="form-control" id="visite_touristique" name="visite_touristique" required>
                        <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                        <option value="oui" data-amount="{{ $congres->amount_visit }}"
                            {{ isset($participant) && $participant->visite == 'oui' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                        <option value="non"
                            {{ isset($participant) && $participant->visite == 'non' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-envelope-check"></i>
                        {{ __('registration.step3.fields.lettre_invitation') }}
                    </label>
                    <select class="form-control" name="lettre_invitation" required>
                        <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                        <option value="oui"
                            {{ isset($participant) && $participant->invitation_letter == 'oui' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                        <option value="non"
                            {{ isset($participant) && $participant->invitation_letter == 'non' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-pencil-square"></i>
                        {{ __('registration.step3.fields.auteur') }}
                    </label>
                    <select class="form-control" name="auteur" required>
                        <option selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                        <option value="oui"
                            {{ isset($participant) && $participant->author == 'oui' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                        <option value="non"
                            {{ isset($participant) && $participant->author == 'non' ? 'selected' : '' }}>
                            {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                    </select>
                </div>
            
                <div class="col-md-2">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-passport"></i>
                        {{ __('registration.step3.fields.num_passeport') }}
                    </label>
                    <input type="text" class="form-control" name="num_passeport"
                        placeholder="{{ __('registration.step3.placeholders.num_passeport') }}"
                        @isset($participant) value="{{ $participant->passeport_number }}" @endisset
                        required>
                </div>
                <div class="col-md-2 text-center">
                    <label class="control-label font-weight-bold text-dark">
                        <i class="bi bi-image"></i>
                        {{ __('registration.step3.fields.photo_passeport') }}
                    </label>
                    <input type="file" class="form-control" name="photo_passeport" accept="image/*"
                        @if (!$participant->passeport_pdf) required @endif>

                    @if ($participant->passeport_pdf)
                        <a class="btn btn-primary" href="{{ Voyager::image($participant->passeport_pdf) }}"
                            target="_blank"> <i class="bi bi-eye"></i> view doc </a>
                    @endif
                </div>
            </div>

        </div>
        <!-- Montant total -->
        <div class="alert text-center" style="border: 2px solid #0121a0">
            <p> <span style="font-size: 1.5em;font-weight: bold;color:black"> Amount to pay :</span><br>
                <span id="amount2" class="text-danger" style="font-size: 1.7em;font-weight: bold">0</span>
            </p>
        </div>
        <div class="box-footer">
            <div class="navigation-buttons">
                <a href="{{ route('form.previous') }}" type="button" class="btn btn-outline">
                    <i class="bi bi-arrow-left"></i> {{ __('registration.step3.buttons.previous') }}
                </a>
                <button type="submit" class="btn btn-outline">
                    {{ __('registration.step3.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
</form>
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

                // --- Affichage dans le span ---
                if (total > 0) {
                    const symbol = getCurrencySymbol(selectedCurrency);
                    $('#amount2').text(total.toLocaleString('fr-FR') + ' ' + symbol);
                } else {
                    $('#amount2').text('0');
                }
            }


            // Afficher ou masquer le code d'adhésion
            $('#membership').on('change', function() {
                const selectedVal = $(this).val();
                if (selectedVal == '1') {
                    $('#membershipcode_row').slideDown(200);
                    $('#membershipcode').attr('required', true);
                } else {
                    $('#membershipcode_row').slideUp(200);
                    $('#membershipcode').removeAttr('required').val('');
                }
                calculateTotal();
            });

            // Recalcul du total sur tout changement
            $('#diner_gala, #visite_touristique').on('change', calculateTotal);

            // Calcul initial si valeurs préremplies
            calculateTotal();
        });
    </script>
@endsection
