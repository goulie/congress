@php
    $categories = App\Models\CategorieRegistrant::forCongress($congres->id);
    $dinner = App\Models\CategorieRegistrant::DinnerforCongress($congres->id);
    $accompanying = App\Models\CategorieRegistrant::accompanyingPersonForCongress($congres->id);
    $tours = App\Models\CategorieRegistrant::ToursforCongress($congres->id);
    $passDeleguate = App\Models\CategorieRegistrant::PassDeleguateforCongress($congres->id);
    $non_member = App\Models\CategorieRegistrant::NonMemberPriceforCongress($congres->id);
    $student_ywp = App\Models\CategorieRegistrant::studentForCongress($congres->id);
    $student_ywp_member = App\Models\CategorieRegistrant::student_ywp_memberForCongress($congres->id);
    $deleguate = App\Models\CategorieRegistrant::deleguateForCongress($congres->id);
@endphp

<form class="ajax-form" method="POST" action="{{ route('form.step') }}" enctype="multipart/form-data">
    @csrf
    <div class="box-body">
        <input type="hidden" name="uuid" value="{{ $participant->uuid ?? '' }}">

        <div class="row align-items-end">
            <!-- Catégorie -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
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

            <!-- Êtes-vous ywp or student   -->
            <div class="col-md-4 hidden" id="ywp_student-box">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-user"></i> {{ __('registration.step3.fields.type_ywp_student') }}

                </label>
                <select class="form-control" name="ywp_or_student" id="ywp_student">
                    <option value="" selected disabled>{{ __('registration.choose') }}</option>
                    <option value="ywp"
                        {{ isset($participant) && $participant->ywp_or_student == 'ywp' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.ywp') }}
                    </option>
                    <option value="student"
                        {{ isset($participant) && $participant->ywp_or_student == 'student' ? 'selected' : '' }}>
                        {{ __('registration.step3.fields.student') }}
                    </option>
                </select>
            </div>

            <!-- Passe 1 Jour -->
            <div class="col-md-4 hidden" id="pass-box">
                <label class="control-label font-weight-bold text-dark required">
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
                <label class="control-label font-weight-bold text-dark required">
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
            <div class="col-md-4 hidden" id="membership-code-box">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-person-check"></i> {{ __('registration.step3.fields.membership') }}
                </label>
                <select class="form-control" name="membership" id="membership">
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
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-upc"></i> {{ __('registration.step3.fields.membershipcode') }}
                </label>
                <input type="text" class="form-control" name="member_code" id="member_code"
                    placeholder="{{ __('registration.step3.placeholders.membershipcode') }}"
                    value="{{ $participant->membership_code ?? '' }}">
            </div>

            <!-- Dîner -->
            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-cup-straw"></i> {{ __('registration.step3.fields.diner_gala') }} <span
                        class="text-danger">({{ $dinner->montant . ' ' . $congres->currency . ' - ' . $congres->nbre_place_dinner }}
                        {{ app()->getLocale() == 'fr' ? 'Places restantes' : 'Remaining seats' }} ) </span>
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
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-geo-alt"></i> {{ __('registration.step3.fields.visite_technical') }} <span
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
                <label class="control-label font-weight-bold text-dark required">
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
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-upc"></i> {{ __('registration.step3.fields.num_passeport') }}
                </label>
                <input type="text" class="form-control" name="passport_number" id="passport_number"
                    placeholder="{{ __('registration.step3.placeholders.num_passeport') }}"
                    value="{{ $participant->passeport_number ?? '' }}">
            </div>

            <div class="col-md-4 hidden" id="passport-date-box">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-calendar"></i> {{ __('registration.step3.fields.date_passeport') }}
                </label>

                <div class="input-group date" id="passport_date_group">
                    <input type="text" class="form-control" name="passport_date" id="passport_date"
                        placeholder="{{ __('registration.step3.placeholders.date_passeport') }}"
                        value="{{ $participant->expiration_passeport_date ?? '' }}" readonly>
                    <span class="input-group-addon">
                        <i class="bi bi-calendar"></i>
                    </span>
                </div>

                <span class="text-danger" id="passport_date_error" style="display:none;"></span>
            </div>


            <div class="col-md-4">
                <label class="control-label font-weight-bold text-dark required">
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

            <!-- Upload Carte étudiant -->
            <div class="col-md-4 hidden" id="student-card-box">
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-card-image"></i> {{ __('registration.step3.fields.student_card') }}
                </label>
                <input type="file" class="form-control" name="student_card" id="student_card" accept="image/*"
                    {{ !isset($participant) || !$participant->student_card ? 'required' : '' }}>
                @if (isset($participant) && $participant->student_card)
                    <a class="btn btn-primary btn-sm" href="{{ Voyager::image($participant->student_card) }}"
                        target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> {{ __('registration.step3.buttons.open') }}
                        <span class="text-danger">Max: 2Mo</span>
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
                <label class="control-label font-weight-bold text-dark required">
                    <i class="bi bi-file-earmark-text"></i> {{ __('registration.step3.fields.attestation_letter') }}
                    <span class="text-danger">Max: 2Mo</span>
                </label>
                <input type="file" class="form-control" name="student_letter" id="student_letter"
                    accept="image/*,application/pdf"
                    {{ !isset($participant) || !$participant->student_letter ? 'required' : '' }}>
                @if (isset($participant) && $participant->student_letter)
                    <a class="btn btn-primary btn-sm" href="{{ Voyager::image($participant->student_letter) }}"
                        target="_blank">
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
        <div class="col-md-12 mt-4" style="font-weight: bold;font-size: 1.5em">
            <div class="alert alert-info text-center fs-5 fw-bold" id="total-box">
                {{ __('registration.step3.fields.total_to_pay') }}: <span id="total-amount">0</span>
                {{ $congres->currency }}
            </div>
        </div>
    </div>

    <div class="box-footer">
        <div class="navigation-buttons mt-3">
            @if (isset($step) && $step > 1)
                <a href="{{ route('form.previous') }}" type="button" class="btn btn-outline">
                    <i class="bi bi-arrow-left"></i> {{ __('registration.step1.buttons.previous') }}
                </a>
            @endif
            <button type="button" id="submit" class="btn btn-primary">
                {{ __('registration.step1.buttons.save_continue') }} <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>

</form>

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- BOOTSTRAP 3 DATEPICKER -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js">
    </script>

    <script>
        $(document).ready(function() {

            /* ============================================================
             *  INITIALISATION
             * ============================================================ */
            $('#submit').on('click', () => $('form.ajax-form').submit());

            const congresEndDate = "{{ $congres->end_date }}";

            $('#passport_date_group').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
                orientation: 'bottom',
                language: "{{ app()->getLocale() }}",
                startDate: congresEndDate
            });

            const montant = {
                passDelegue: parseFloat("{{ $passDeleguate->montant ?? 0 }}"),
                nonMembre: parseFloat("{{ $non_member->montant ?? 0 }}"),
                dinner: parseFloat("{{ $dinner->montant ?? 0 }}"),
                visite: parseFloat("{{ $tours->montant ?? 0 }}"),
                delegue: parseFloat("{{ $deleguate->montant ?? 0 }}"),
                student: parseFloat("{{ $student_ywp->montant ?? 0 }}"),
                student_member: parseFloat("{{ $student_ywp_member->montant ?? 0 }}")
            };

            /* ============================================================
             * HELPERS SHOW / HIDE
             * ============================================================ */
            function show(el) {
                $(el).removeClass('hidden').slideDown()
                    .find('input,select').prop('disabled', false);
            }

            function hide(el) {
                $(el).slideUp().addClass('hidden')
                    .find('input,select').prop('disabled', true).prop('required', false);
            }

            function resetAll() {
                hide('#pass-box');
                hide('#pass-date-box');
                hide('#membership-code-box');
                hide('#member-code-box');
                hide('#ywp_student-box');
                hide('#student-card-box');
                hide('#student-letter-box');
                hide('#site-visit-box');
                hide('#passport-number-box');
                hide('#passport-date-box');
            }

            /* ============================================================
             * LOGIQUE FORMULAIRE
             * ============================================================ */

            // ---- LOGIQUE DÉLÉGUÉ ----
            function logiqueDelegue() {
                // Passeport toujours affiché + required
                show('#passport-number-box');
                $('#passport_number').prop('required', true);

                show('#passport-date-box');
                $('#passport_date').prop('required', true);

                // Pass toujours affiché + required
                show('#pass-box');
                $('#pass').prop('required', true);

                const pass = $('#pass').val();

                if (pass === 'oui') {

                    // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                    // PASS = OUI
                    // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬

                    show('#pass-date-box');
                    $('.pass-date-checkbox').prop('required', true);

                    // Aucun membership demandé
                    hide('#membership-code-box');
                    hide('#member-code-box');

                } else {

                    // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                    // PASS = NON → membership obligatoire
                    // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬

                    hide('#pass-date-box');
                    $('.pass-date-checkbox').prop('required', false);

                    // Afficher membership
                    show('#membership-code-box');
                    $('#membership').prop('required', true);

                    const membership = $('#membership').val();

                    // Si membre = oui → code obligatoire
                    if (membership === 'oui') {
                        show('#member-code-box');
                        $('#member_code').prop('required', true);
                    } else {
                        hide('#member-code-box');
                        $('#member_code').prop('required', false);
                    }
                }
            }


            // ---- LOGIQUE STUDENT ----
            function logiqueStudent() {

                show('#passport-number-box');
                $('#passport_number').prop('required', true);

                show('#passport-date-box');
                $('#passport_date').prop('required', true);

                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                // Sélection YWP / STUDENT → required
                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                show('#ywp_student-box');
                $('#ywp_student').prop('required', true);

                const type = $('#ywp_student').val(); // "ywp" ou "student"

                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                // Membership toujours demandé pour un Student
                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                show('#membership-code-box');
                $('#membership').prop('required', true);

                if ($('#membership').val() === 'oui') {
                    show('#member-code-box');
                    $('#member_code').prop('required', true);
                } else {
                    hide('#member-code-box');
                    $('#member_code').prop('required', false);
                }

                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                // Gestion Jeune Professionnel (YWP)
                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                if (type === 'ywp') {
                    show('#student-letter-box');
                    $('#student_letter').prop('required', true);

                    hide('#student-card-box');
                    $('#student_card').prop('required', false);
                }

                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                // Gestion Étudiant (Student simple)
                // ▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬
                if (type === 'student') {
                    show('#student-card-box');
                    $('#student_card').prop('required', true);

                    hide('#student-letter-box');
                    $('#student_letter').prop('required', false);
                }

                // ▬▬▬ Si aucun choix encore fait (page reload)
                if (!type) {
                    hide('#student-letter-box');
                    hide('#student-card-box');
                }
            }


            // ---- VISITE ----
            function logiqueVisite() {
                if ($('#visit').val() === 'oui') {
                    show('#site-visit-box');
                    $('#site_visit').prop('required', true);
                } else {
                    hide('#site-visit-box');
                    $('#site_visit').prop('required', false);
                }
            }


            // ---- RENDER GLOBAL ----
            function renderForm() {
                resetAll();

                const cat = $('#categorie').val();

                if (cat === '1') logiqueDelegue();
                if (cat === '4') logiqueStudent();

                logiqueVisite(); // commun
                calculerTotal();
            }

            /* ============================================================
             * CALCUL TOTAL
             * ============================================================ */
            function calculerTotal() {
                let total = 0;

                const cat = $('#categorie').val();
                const membership = $('#membership').val();
                const dinner = $('#dinner').val();
                const visit = $('#visit').val();
                const pass = $('#pass').val();
                const nbPass = $('.pass-date-checkbox:checked').length;

                if (!cat) {
                    $('#total-amount').text('0.00');
                    return;
                }

                // ---- DÉLÉGUÉ ----
                if (cat === '1') {
                    if (pass === 'oui' && nbPass > 0) {
                        total += nbPass * montant.passDelegue;
                    } else {
                        total += (membership === 'oui') ? montant.delegue : montant.nonMembre;
                    }
                }

                // ---- STUDENT ----
                else if (cat === '4') {
                    total += (membership === 'oui') ? montant.student_member : montant.student;
                }

                // ---- AUTRES CATÉGORIES (si existait) ----
                else {
                    const montantCategorie = parseFloat($('#categorie option:selected').data('amount')) || 0;
                    total += (membership === 'oui') ? montantCategorie : montant.nonMembre;
                }

                if (dinner === 'oui') total += montant.dinner;
                if (visit === 'oui') total += montant.visite;

                $('#total-amount').text(total.toFixed(2));
            }

            /* ============================================================
             * VALIDATION DATE PASSEPORT
             * ============================================================ */
            const locale = "{{ app()->getLocale() }}";

            const messages = {
                fr: {
                    required: "La date est obligatoire.",
                    invalid: "La date doit être supérieure à la date du congrès."
                },
                en: {
                    required: "The date is required.",
                    invalid: "The date must be later than the congress date."
                }
            };

            function validerDate() {
                if (!$('#passport_date').is(':visible')) return true;

                const date = $('#passport_date').val();
                const msg = messages[locale] || messages['fr']; // fallback FR

                if (!date) {
                    $('#passport_date_error').text(msg.required).show();
                    return false;
                }

                if (date <= congresEndDate) {
                    $('#passport_date_error').text(msg.invalid).show();
                    return false;
                }

                $('#passport_date_error').hide();
                return true;
            }

            /* ============================================================
             * AJAX SUBMIT
             * ============================================================ */
            $('form.ajax-form').on('submit', function(e) {
                e.preventDefault();

                if (!validerDate()) return;

                $('#loader').removeClass('hidden').show();
                const form = $(this);
                const formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(response) {
                        $('#loader').hide();

                        Swal.fire({
                            icon: 'success',
                            title: "{{ __('registration.success') }}",
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            window.location.href = response.redirect ?? window.location
                                .href;
                        });
                    },

                    error: function(xhr) {
                        $('#loader').hide();

                        // Message d’erreur par défaut
                        let errorMessage = "{{ __('registration.error_occurred') }}";
                        let status = xhr.responseJSON?.status ?? null;

                        // Erreurs de validation
                        if (xhr.responseJSON?.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                '<br>');
                        }
                        // Message personnalisé
                        else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Traitement selon statut membership
                        if (status === 'inexistant') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('registration.error') }}',
                                html: errorMessage,
                                showDenyButton: true,
                                confirmButtonText: 'Ok',
                                denyButtonText: '{{ __('registration.contactby_email') }}',
                            }).then((result) => {
                                if (result.isDenied) {
                                    window.location.href =
                                        "mailto:mlawson@afwasa.org?cc=mseck@afwasa.org";
                                }
                            });
                        } else if (status === 'inactif') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('registration.error') }}',
                                html: errorMessage,
                                showDenyButton: true,
                                confirmButtonText: 'Ok',
                                denyButtonText: '{{ __('registration.contactby_email') }}',
                            }).then((result) => {
                                if (result.isDenied) {
                                    window.location.href =
                                        "mailto:snguessan@afwasa.org?cc=vtihi@afwasa.org;mlawson@afwasa.org;mseck@afwasa.org";
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('registration.error') }}',
                                html: errorMessage,
                                confirmButtonText: 'Ok',
                            });
                        }
                    }
                });

            });

            /* ============================================================
             * EVENTS
             * ============================================================ */
            $('#categorie, #membership, #dinner, #visit, #pass, #ywp_student')
                .on('change', renderForm);

            $(document).on('change', '.pass-date-checkbox', calculerTotal);

            /* ============================================================
             * INITIAL LOAD
             * ============================================================ */
            renderForm();
        });
    </script>
@endsection
