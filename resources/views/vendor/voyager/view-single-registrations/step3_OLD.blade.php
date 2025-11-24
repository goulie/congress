@php
    $categories = App\Models\CategorieRegistrant::forCongress($congres->id);
    $dinner = App\Models\CategorieRegistrant::DinnerforCongress($congres->id);
    $accompanying = App\Models\CategorieRegistrant::accompanyingPersonForCongress($congres->id);
    $tours = App\Models\CategorieRegistrant::ToursforCongress($congres->id);
@endphp

<form class="ajax-form" method="POST" action="{{ route('form.step3') }}" enctype="multipart/form-data">
    @csrf
    <div class="box-body">
        <input type="hidden" name="participant_id" value="{{ $participant->id ?? '' }}">
        {{-- Étape 3 : Détails du congrès --}}
        <div class="row">
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person-badge"></i>
                    {{ __('registration.step3.fields.category') }}
                </label>
                <select class="form-control" name="category" id="category" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
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

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person"></i>
                    {{ __('registration.step3.fields.membership') }}
                </label>

                <select class="form-control" name="membership" id="membership" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>

                    @forelse ($categories as $typeMember)
                        <option data-amount="{{ $typeMember->montant }}" data-currency="{{ $congres->currency }}"
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
            </div>




            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
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
                <select id="diner_gala" class="form-control" name="diner_gala" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option data-amount ="{{ $dinner->montant ?? 0 }}" value="oui"
                        {{ isset($participant) && $participant->diner == 'oui' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                    <option value="non" {{ isset($participant) && $participant->diner == 'non' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                </select>
            </div>
        </div>
    
    <div class="row hidden" id="membershipcode_div">
        <div class="col-md-12">
            <label class="control-label font-weight-bold text-dark">
                <i class="bi bi-key"></i>
                {{ __('registration.step3.fields.membershipcode') }}
            </label>
            <input type="text" class="form-control" name="membershipcode"
                placeholder="{{ __('registration.step3.placeholders.membershipcode') }}"
                @isset($participant) value="{{ $participant->membership_code }}" @endisset>
        </div>
    </div>
        <div class="row" style="margin-top:15px;">
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-binoculars"></i>
                    {{ __('registration.step3.fields.visite_touristique') }}
                </label>
                @if ($congres->currency == 'EUR')
                    <strong style="font-weight: bold"> {{ $tours->montant ?? 0 }} € </strong>
                @elseif ($congres->currency == 'US' || $congres->currency == 'USD')
                    <strong style="font-weight: bold"> ${{ $tours->montant ?? 0 }}</strong>
                @else
                    <strong style="font-weight: bold"> {{ $tours->montant ?? 0 }} {{ $congres->currency }}</strong>
                @endif

                <select class="form-control" id="visite_touristique" name="visite_touristique" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option value="oui" data-amount="{{ $tours->montant ?? 0 }}"
                        {{ isset($participant) && $participant->visite == 'oui' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                    <option value="non"
                        {{ isset($participant) && $participant->visite == 'non' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-envelope-check"></i>
                    {{ __('registration.step3.fields.lettre_invitation') }}
                </label>
                <select class="form-control" name="lettre_invitation" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option value="oui"
                        {{ isset($participant) && $participant->invitation_letter == 'oui' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                    <option value="non"
                        {{ isset($participant) && $participant->invitation_letter == 'non' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-pencil-square"></i>
                    {{ __('registration.step3.fields.auteur') }}
                </label>
                <select class="form-control" name="auteur" required>
                    <option value="" selected disabled>{{ __('registration.choose') ?? 'Select' }}</option>
                    <option value="oui"
                        {{ isset($participant) && $participant->author == 'oui' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.oui') ?? 'Oui' }}</option>
                    <option value="non"
                        {{ isset($participant) && $participant->author == 'non' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.non') ?? 'Non' }}</option>
                </select>
            </div>
        </div>

        <div class="row" style="margin-top:15px;">
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-passport"></i>
                    {{ __('registration.step3.fields.num_passeport') }}
                </label>
                <input type="text" class="form-control" name="num_passeport"
                    placeholder="{{ __('registration.step3.placeholders.num_passeport') }}"
                    @isset($participant) value="{{ $participant->passeport_number }}" @endisset
                    required>
            </div>
            <div class="col-md-4 text-center">
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
                let selectedCurrency = '{{ $congres->currency }}'; // Devise par défaut

                // Membership - CORRECTION : Récupérer le montant correctement
                let membershipOption = $('#membership option:selected');
                if (membershipOption.length > 0 && membershipOption.val() !== '') {
                    let membershipAmount = parseFloat(membershipOption.data('amount')) || 0;
                    total += membershipAmount;
                }

                // Dîner gala - CORRECTION : Vérifier si "oui" est sélectionné
                let dinerOption = $('#diner_gala option:selected');
                if (dinerOption.length > 0 && dinerOption.val() === 'oui') {
                    let dinerAmount = parseFloat(dinerOption.data('amount')) || 0;
                    total += dinerAmount;
                }

                // Visite touristique - CORRECTION : Vérifier si "oui" est sélectionné
                let visiteOption = $('#visite_touristique option:selected');
                if (visiteOption.length > 0 && visiteOption.val() === 'oui') {
                    let visiteAmount = parseFloat(visiteOption.data('amount')) || 0;
                    total += visiteAmount;
                }

                // Affichage du total
                const symbol = getCurrencySymbol(selectedCurrency);
                $('#amount2').text(total.toLocaleString('fr-FR') + ' ' + symbol);
            }

            // Fonction pour gérer l'affichage du code de membership - CORRECTION : Logique améliorée
            function toggleMembershipCode() {
                let selectedMembershipId = $('#membership').val();

                // Afficher le champ code de membership uniquement si l'ID du membership est "1"
                if (selectedMembershipId == "1") {
                    $('#membershipcode_div').show();
                    $('#membershipcode_div').removeClass('hidden');
                    $('input[name="membershipcode"]').prop('required', true);
                } else {
                    $('#membershipcode_div').hide();
                    $('input[name="membershipcode"]').prop('required', false).val('');
                }
            }

            // Événements pour le recalcul du total
            $('#membership, #diner_gala, #visite_touristique').on('change', function() {
                calculateTotal();
                toggleMembershipCode(); // Mettre à jour l'affichage du code membership
            });

            // Initialisation au chargement de la page
            calculateTotal();
            toggleMembershipCode();
        });
    </script>
@endsection
