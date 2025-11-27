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

<form class="ajax-form" method="POST" action="{{ route('form.step') }}" enctype="multipart/form-data">
    @csrf
    <div class="box-body">
        <input type="hidden" name="participant_id" value="{{ $participant->id ?? '' }}">

        <div class="row align-items-end">
            <!-- Catégorie -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person-badge"></i> {{ __('registration.step3.fields.category') }}
                </label>
                <select class="form-control" name="categorie" id="categorie" required>
                    <option value="" selected disabled>{{ __('registration.choose') }}</option>
                    @foreach (App\Models\CategoryParticipant::where(['status' => 'isActive'])->get() as $categorie)
                        <option value="{{ $categorie->id }}"
                            data-amount="{{ $categorie->libelle == 'Student' ? $student_ywp->montant : $deleguate->montant }}"
                            {{ isset($participant) && $participant->participant_category_id == $categorie->id ? 'selected' : '' }}>
                            {{ $categorie->translate(app()->getLocale(), 'fallbackLocale')->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Passe 1 Jour -->
            <div class="col-md-4 hidden" id="pass-box">
                <label class="control-label font-weight-bold text-dark">
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
                <label class="control-label font-weight-bold text-dark">
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
                                <span
                                    class="text-danger">({{ $passDeleguate->montant . ' ' . $congres->currency }})</span>
                            </label>
                        </div>
                    @empty
                        <p class="text-muted">{{ __('registration.step3.fields.no_pass_dates') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Êtes-vous membre -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-person-check"></i> {{ __('registration.step3.fields.membership') }}
                </label>
                <select class="form-control" name="membership" id="membership" required>
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
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-upc"></i> {{ __('registration.step3.fields.membershipcode') }}
                </label>
                <input type="text" class="form-control" name="member_code" id="member_code"
                    placeholder="{{ __('registration.step3.placeholders.membershipcode') }}" 
                    value="{{ $participant->membership_code ?? '' }}">
            </div>

            <!-- Dîner -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-cup-straw"></i> {{ __('registration.step3.fields.diner_gala') }} <span
                        class="text-danger">({{ $dinner->montant . ' ' . $congres->currency }}) </span>
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
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-geo-alt"></i> {{ __('registration.step3.fields.visite_touristique') }} <span
                        class="text-danger">({{ $tours->montant . ' ' . $congres->currency }})</span></span>
                </label>
                <select class="form-control" name="visit" id="visit" required>
                    <option value="" selected disabled>{{ __('registration.choose') }}</option>
                    <option value="oui" data-amount="{{ $tours->montant }}"
                        {{ isset($participant) && $participant->visite == 'oui' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.oui') }}
                    </option>
                    <option value="non"
                        {{ isset($participant) && $participant->visite == 'non' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.non') }}
                    </option>
                </select>
            </div>

            <!-- Choix du site de visite -->
            <div class="col-md-4 hidden" id="site-visit-box">
                <label class="control-label font-weight-bold text-dark">
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

            <div class="col-md-4 hidden" id="passport-number-box">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-upc"></i> {{ __('registration.step3.fields.num_passeport') }}
                </label>
                <input type="text" class="form-control" name="passport_number" id="passport_number"
                    placeholder="{{ __('registration.step3.placeholders.num_passeport') }}" 
                    value="{{ $participant->passeport_number ?? '' }}">
            </div>

            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark">
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

            <!-- Upload Passeport (pour délégué) -->
            <div class="col-md-4 hidden" id="passport-box">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-passport"></i> {{ __('registration.step3.fields.photo_passeport') }}
                </label>
                <input type="file" class="form-control" name="passport" id="passport" accept="image/*"
                    {{ !isset($participant) || !$participant->passeport_pdf ? 'required' : '' }}>
                @if (isset($participant) && $participant->passeport_pdf)
                    <a class="btn btn-primary" href="{{ Voyager::image($participant->passeport_pdf) }}" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> {{ __('registration.step3.buttons.open') }}
                    </a>
                    <div class="form-check mt-2">
                        <label class="form-check-label text-danger" for="remove_passport">
                            {{ __('registration.step3.fields.upload_to_replace') }}
                        </label>
                    </div>
                @endif
            </div>

            <!-- Upload Carte étudiant -->
            <div class="col-md-4 hidden" id="student-card-box">
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-card-image"></i> {{ __('registration.step3.fields.student_card') }}
                </label>
                <input type="file" class="form-control" name="student_card" id="student_card" accept="image/*"
                    {{ !isset($participant) || !$participant->student_card ? 'required' : '' }}>
                @if (isset($participant) && $participant->student_card)
                    <a class="btn btn-primary btn-sm" href="{{ Voyager::image($participant->student_card) }}" target="_blank">
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
                <label class="control-label font-weight-bold text-dark">
                    <i class="bi bi-file-earmark-text"></i> {{ __('registration.step3.fields.attestation_letter') }}
                </label>
                <input type="file" class="form-control" name="student_letter" id="student_letter"
                    accept="image/*,application/pdf"
                    {{ !isset($participant) || !$participant->student_letter ? 'required' : '' }}>
                @if (isset($participant) && $participant->student_letter)
                    <a class="btn btn-primary btn-sm" href="{{ Voyager::image($participant->student_letter) }}" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> {{ __('registration.step3.buttons.open') }}
                    </a>                   
                    <div class="form-check mt-2">
                        <label class="form-check-label text-danger" for="remove_student_letter">
                            {{ __('registration.step3.fields.upload_to_replace') }}
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="alert alert-info text-center fs-5 fw-bold" id="total-box">
                {{ __('registration.step3.fields.total_to_pay') }}: <span id="total-amount" style="font-weight: bold;font-size: 1.5em">0</span>
                {{ $congres->currency }}
            </div>
        </div>
    </div>
    <div class="box-footer">
        <div class="navigation-buttons mt-3">
            <a href="{{ route('form.previous') }}" type="button" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i> {{ __('registration.step1.buttons.previous') }}
            </a>
            <button type="submit" class="btn btn-outline">
                {{ __('registration.step1.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</form>

@section('javascript')
    <script>
        $(document).ready(function() {
            // --- VARIABLES MONTANTS ---
            const montantPassDelegue = parseFloat("{{ $passDeleguate->montant ?? 0 }}");
            const montantNonMembre = parseFloat("{{ $non_member->montant ?? 0 }}");
            const montantDinner = parseFloat("{{ $dinner->montant ?? 0 }}");
            const montantVisite = parseFloat("{{ $tours->montant ?? 0 }}");
            const montantDeleguate = parseFloat("{{ $deleguate->montant ?? 0 }}");

            // --- INIT --- 
            $('#member-code-box, #passport-box, #passport-number-box, #student-card-box, #student-letter-box, #pass-box, #pass-date-box, #site-visit-box')
                .addClass('hidden')
                .find('input, select')
                .prop('disabled', true)
                .prop('required', false);

            // --- FONCTION CALCUL TOTAL ---
            function calculerTotal() {
                let total = 0;
                const categorie = $('#categorie').val();
                const membership = $('#membership').val();
                const dinner = $('#dinner').val();
                const visit = $('#visit').val();
                const passSelection = $('#pass').val();
                const nbPass = $('.pass-date-checkbox:checked').length;

                // --- Catégorie Délégué ---
                if (categorie === '1') {
                    if (passSelection === 'oui' && nbPass > 0) {
                        // Si des passes journées sont cochées, on applique seulement les passes
                        total += nbPass * montantPassDelegue;
                        // Membership n'est pas pris en compte
                    } else {
                        // Pas de pass journée sélectionné
                        total += montantDeleguate;
                        if (membership === 'non') total += montantNonMembre;

                        // Cas rare : pass="oui" mais aucune date cochée → ajouter montantPassDelegue
                        if (passSelection === 'oui' && nbPass === 0) {
                            total += montantPassDelegue;
                        }
                    }
                }

                // --- Catégorie Étudiant/YWP ---
                if (categorie === '4') {
                    total += parseFloat($('#categorie option:selected').data('amount') || 0);
                }

                // --- Dîner ---
                if (dinner === 'oui') total += montantDinner;

                // --- Visite technique ---
                if (visit === 'oui') total += montantVisite;

                $('#total-amount').text(total.toFixed(2));
            }

            // --- AFFICHAGE CODE MEMBRE ---
            $('#membership').on('change', function() {
                if ($(this).val() === 'oui') {
                    $('#member-code-box').removeClass('hidden').slideDown();
                    $('#member_code').prop('required', true).prop('disabled', false);
                } else {
                    $('#member-code-box').slideUp(function() {
                        $(this).addClass('hidden');
                        $('#member_code').val('').prop('required', false).prop('disabled', true);
                    });
                }
                calculerTotal();
            });

            // --- AFFICHAGE CHAMPS SELON CATEGORIE ---
            $('#categorie').on('change', function() {
                const value = $(this).val();

                // Réinitialiser seulement les champs qui doivent être cachés
                if (value !== '1') {
                    $('#passport-box, #passport-number-box, #pass-box, #pass-date-box')
                        .slideUp().addClass('hidden')
                        .find('input, select').prop('required', false).prop('disabled', true);
                    // Ne pas vider les valeurs ici pour conserver les données existantes
                    $('#pass').val('');
                }

                if (value !== '4') {
                    $('#student-card-box, #student-letter-box')
                        .slideUp().addClass('hidden')
                        .find('input, select').prop('required', false).prop('disabled', true);
                }

                // Afficher les champs selon la catégorie
                if (value === '1') {
                    $('#passport-box, #passport-number-box, #pass-box').removeClass('hidden').slideDown();
                    $('#passport, #passport_number, #pass').prop('required', true).prop('disabled', false);
                }

                if (value === '4') {
                    $('#student-card-box, #student-letter-box').removeClass('hidden').slideDown();
                    $('#student_card, #student_letter').prop('required', true).prop('disabled', false);
                }

                calculerTotal();
            });

            // --- AFFICHAGE SITE VISITE ---
            $('#visit').on('change', function() {
                if ($(this).val() === 'oui') {
                    $('#site-visit-box').removeClass('hidden').slideDown();
                    $('#site_visit').prop('required', true).prop('disabled', false);
                } else {
                    $('#site-visit-box').slideUp(function() {
                        $(this).addClass('hidden');
                        $('#site_visit').prop('required', false).prop('disabled', true);
                    });
                }
                calculerTotal();
            });

            // --- AFFICHAGE DATES PASS 1 JOUR ---
            $('#pass').on('change', function() {
                if ($(this).val() === 'oui') {
                    $('#pass-date-box').removeClass('hidden').slideDown();
                    $('.pass-date-checkbox').prop('disabled', false);
                } else {
                    $('#pass-date-box').slideUp(function() {
                        $(this).addClass('hidden');
                        $('.pass-date-checkbox').prop('required', false).prop('disabled', true);
                    });
                }
                calculerTotal();
            });

            // --- INITIALISATION DES CHAMPS EXISTANTS ---
            function initialiserChampsExistants() {
                @if (isset($participant))
                    // Afficher les champs conditionnels selon les données existantes
                    if ($('#membership').val() === 'oui') {
                        $('#member-code-box').removeClass('hidden').show();
                        $('#member_code').prop('required', true).prop('disabled', false);
                    }

                    if ($('#visit').val() === 'oui') {
                        $('#site-visit-box').removeClass('hidden').show();
                        $('#site_visit').prop('required', true).prop('disabled', false);
                    }

                    if ($('#pass').val() === 'oui') {
                        $('#pass-date-box').removeClass('hidden').show();
                        $('.pass-date-checkbox').prop('disabled', false);
                    }

                    // Forcer l'affichage des champs selon la catégorie
                    const categorie = $('#categorie').val();
                    if (categorie === '1') {
                        $('#passport-box, #passport-number-box, #pass-box').removeClass('hidden').show();
                        $('#passport_number, #pass').prop('required', true).prop('disabled', false);

                        // Le passeport n'est required que si pas de fichier existant
                        const hasPassport = @json(isset($participant) && $participant->passeport_pdf);
                        $('#passport').prop('required', !hasPassport).prop('disabled', false);
                    }

                    if (categorie === '4') {
                        $('#student-card-box, #student-letter-box').removeClass('hidden').show();

                        // Les fichiers ne sont required que si pas de fichiers existants
                        const hasStudentCard = @json(isset($participant) && $participant->student_card);
                        const hasStudentLetter = @json(isset($participant) && $participant->student_letter);

                        $('#student_card').prop('required', !hasStudentCard).prop('disabled', false);
                        $('#student_letter').prop('required', !hasStudentLetter).prop('disabled', false);
                    }

                    // Recalculer le total
                    calculerTotal();
                @endif
            }

            // --- CALCUL TOTAL EN TEMPS REEL ---
            $('#categorie, #membership, #dinner, #visit, #pass').on('change', calculerTotal);
            $(document).on('change', '.pass-date-checkbox', calculerTotal);

            // --- INIT TOTAL ET CHAMPS EXISTANTS ---
            initialiserChampsExistants();
            calculerTotal();

        });
    </script>
@endsection
