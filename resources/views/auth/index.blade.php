@extends('auth.layout')

@section('title', 'Evenement AAEA')

@section('content')

    @php
        $nationalites = App\Models\Nationality::all();
        $genres = App\Models\Genre::all()->translate(app()->getLocale(), 'fallbackLocale');
        $ages = App\Models\Age::all()->translate(app()->getLocale(), 'fallbackLocale');
        $civilites = App\Models\Civility::all()->translate(app()->getLocale(), 'fallbackLocale');
    @endphp
    <iframe width="100%" height="880px"
        src="https://forms.office.com/Pages/ResponsePage.aspx?id=PSy4tH8JaU2CCvHFpOjoBMNydGdxG61Hne67sf8NDsRUOE9BUjlYWFVEU1dXTERGNjlWQk9MMkxITC4u&embed=true"
        frameborder="0" marginwidth="0" marginheight="0" style="border: none; max-width:100%; max-height:100vh" allowfullscreen
        webkitallowfullscreen mozallowfullscreen msallowfullscreen> </iframe>
   {{--  <form id="form1">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="code_membre" class="form-label">{{ __('eventform.code_membre') }}</label>
            <input type="text" id="code_membre" class="form-control" name="code_membre" placeholder=" "
                oninput="this.value = this.value.toUpperCase()" autocomplete="off">
            <input type="hidden" name="event_token" value="{{ $event->token }}" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="adresse_email" class="form-label">{{ __('eventform.adresse_email') }}</label>
            <input type="email" id="adresse_email" class="form-control" name="adresse_email" autocomplete="off">
        </div>

        <div id="resultat"></div>
        <div id="loader" class="text-center mt-2" style="display:none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('eventform.chargement') }}</span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-lg mt-3">{{ __('eventform.suivant') }}</button>
    </form>


    <br>

    <form id="form2" style="display:none;">
        {{ csrf_field() }}
        <input type="hidden" name="event_token" value="{{ $event->token }}">
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        <input type="hidden" name="membership_code" id="membership_code">

        <div class="row mb-5">
            <div class="col-md-4">
                <label for="civ" class="form-label">{{ __('eventform.civility') }}</label>
                <select class="form-select" id="civ" name="civ" required>
                    <option selected disabled>--{{ __('eventform.choose') }}--</option>
                    @foreach ($civilites as $civility)
                        <option value="{{ $civility->id }}">{{ $civility->libelle }}</option>
                    @endforeach
                </select>
                <p id="civ_error" class="text-danger"></p>
            </div>
            <div class="col-md-4">
                <label for="genre" class="form-label">{{ __('eventform.gender') }}</label>
                <select class="form-select" id="genre" name="genre" required>
                    <option selected disabled>--{{ __('eventform.choose') }}--</option>
                    @foreach ($genres as $genre)
                        <option value="{{ $genre->id }}">{{ $genre->libelle }}</option>
                    @endforeach
                </select>
                <p id="genre_error" class="text-danger"></p>
            </div>
            <div class="col-md-4">
                <label for="age" class="form-label">{{ __('eventform.age_range') }}</label>
                <select class="form-select" name="age" id="age" required>
                    <option selected disabled>--{{ __('eventform.choose') }}--</option>
                    @foreach ($ages as $age)
                        <option value="{{ $age->id }}">{{ $age->libelle }}</option>
                    @endforeach
                </select>
                <p id="age_error" class="text-danger"></p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <label for="fname" class="form-label">{{ __('eventform.fullname') }}</label>
                <input type="text" class="form-control" id="fname" name="fname"
                    placeholder="{{ __('eventform.placeholder_name') }}" oninput="this.value = this.value.toUpperCase()"
                    required autocomplete="off">
                <p id="fname_error" class="text-danger"></p>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">{{ __('eventform.email') }}</label>
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="{{ __('eventform.placeholder_email') }}" required autocomplete="off">
                <p id="email_error" class="text-danger"></p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <label for="organisation" class="form-label">{{ __('eventform.organisation') }}</label>
                <input type="text" class="form-control" id="organisation" name="organisation"
                    placeholder="{{ __('eventform.placeholder_org') }}" oninput="this.value = this.value.toUpperCase()"
                    required autocomplete="off">
                <p id="organisation_error" class="text-danger"></p>
            </div>
            <div class="col-md-6">
                <label for="fonctions" class="form-label">{{ __('eventform.position') }}</label>
                <input type="text" class="form-control" id="fonctions" name="fonctions"
                    placeholder="{{ __('eventform.placeholder_position') }}" required autocomplete="off">
                <p id="fonctions_error" class="text-danger"></p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-4">
                <label for="phone" class="label" style="padding-bottom:9px">{{ __('eventform.phone') }}</label>
                <input type="tel" id="phone" name="phone" class="form-control" required autocomplete="off">
                <p id="phone_error" class="text-danger"></p>
            </div>
            <div class="col-md-8">
                <label for="pays_nationalite" class="form-label">{{ __('eventform.nationality') }}</label>
                <select class="form-select" id="pays_nationalite" name="nationality" required>
                    <option selected disabled>--{{ __('eventform.choose') }}--</option>
                    @foreach ($nationalites as $nationalite)
                        <option value="{{ $nationalite->id }}">{{ $nationalite->libelle_fr }}</option>
                    @endforeach
                </select>
                <p id="pays_nationalite_error" class="text-danger"></p>
            </div>
        </div>

        <!-- Résumé -->
        <div class="cost-summary-card">
            <div class="cost-summary-header">
                <i class="bi bi-tag-fill"></i>
                {{ __('eventform.summary_title') }}
            </div>
            <p class="alert alert-info text-center text-uppercase">
                <strong class="text-shadow">{{ $event->theme }}</strong>
            </p>
            <div class="summary-display">
                {{ __('eventform.total_cost') }} : <span id="amount"></span>
            </div>
            <p>{{ __('eventform.applied_code') }}: <span class="membership_code text-black fw-bold"></span></p>
        </div>

        <!-- Boutons -->
        <div class="payment-options mt-4">
            <div class="row g-2">
                <div class="col-sm-6">
                    <button type="submit" id="btnPayLater" class="btn btn-outline-secondary w-100 btn-lg">
                        {{ __('eventform.pay_later') }}
                    </button>
                </div>
                <div class="col-sm-6">
                    <button type="submit" id="btnPayNow" class="btn btn-primary w-100 btn-lg">
                        {{ __('eventform.pay_now') }}
                    </button>
                </div>
                <div class="col-sm-12">
                    <button type="submit" id="btnRegister" class="btn btn-primary w-100 btn-lg">
                        {{ __('eventform.register_now') }}
                    </button>
                </div>
            </div>
        </div>
    </form> --}}


@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const phoneInputField = document.querySelector("#phone");
            // Initialize the plugin
            const phoneInput = window.intlTelInput(phoneInputField, {
                initialCountry: "auto",
                geoIpLookup: function(success, failure) {
                    fetch("https://ipapi.co/json/")
                        .then(res => res.json())
                        .then(data => success(data.country_code.toLowerCase()))
                        .catch(() => success("ci")); // fallback par défaut si erreur
                },
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });
            // Soumission du form1
            $('#form1').on('submit', function(e) {
                e.preventDefault();
                $('#loader').show();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('check.matricule') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#loader').hide();

                        if (response.exists) {
                            $('#fname').val(response.data.name + ' ' + response.data
                                .first_name);
                            $('#genre').val(response.data.gender_id);
                            $('#age').val(response.data.age);
                            $('#email').val(response.data.email);
                            $('#organisation').val(response.data.organisation);
                            $('#fonctions').val(response.data.job);
                            $('#pays_nationalite').val(response.data.nationality_id);
                            $("#amount").html(response.amount.price + ' ' + (response.amount
                                .currency === 'EUR' ? '€' : '$'));
                            $("#membership_code").val(response.code_membre);
                            $(".membership_code").html(response.code_membre);

                        } else {
                            $('#fname').val('');
                            $('#genre').val('');
                            $('#age').val('');
                            $('#email').val('');
                            $('#organisation').val('');
                            $('#fonctions').val('');
                            $('#pays_nationalite').val('');
                            $("#amount").html(response.amount.price + ' ' + (response.amount
                                .currency === 'EUR' ? '€' : '$'));
                            $("#membership_code").val(response.code_membre);
                            $(".membership_code").html(response.code_membre);

                        }

                        if (response.amount.price == 0) {
                            $('#btnPayNow').hide();
                            $('#btnPayLater').hide();
                            $('#btnRegister').show();
                        } else {
                            $('#btnPayNow').show();
                            $('#btnPayLater').show();
                            $('#btnRegister').hide();
                        }

                        $('#form2').fadeIn();
                        $('#form1').fadeOut();
                    },
                    error: function() {
                        $('#loader').hide();
                        alert("Erreur lors de la requête.");
                    }
                });
            });

            // Validation du form2
            function submitForm(actionUrl) {
                // Récupérer le numéro complet
                const fullNumber = phoneInput.getNumber();

                // Créer ou mettre à jour le champ hidden
                if ($("#full_phone").length === 0) {
                    $("#form2").append('<input type="hidden" name="full_phone" id="full_phone">');
                }
                $("#full_phone").val(fullNumber);

                // Vérifications
                /*   let fname = $("#fname").val().trim();
                                let email = $("#a_email").val().trim();
                 */
                /* if (fname === "") {
                            $("#fname").addClass("is-invalid")
                                .next(".error").text("Le nom complet est obligatoire.");
                            isValid = false;
                        }

                        if (email === "") {
                            $("#a_email").addClass("is-invalid")
                                .next(".error").text("L'email est obligatoire.");
                            isValid = false;
                        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                            $("#a_email").addClass("is-invalid")
                                .next(".error").text("Veuillez entrer un email valide.");
                            isValid = false;
                        }

                   

                        // Si erreurs → stoppe l’envoi
                        if (!isValid) {
                            return;
                        } */

                const data = $("#form2").serialize();

                $.ajax({
                    url: actionUrl,
                    method: "POST",
                    data: data,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    beforeSend: function() {
                        $("#loader").show();
                    },

                    success: function(response) {

                        $("#loader").hide();
                        $('#form2').fadeIn();
                        $('#form1').fadeOut();
                        if (response.success && response.url) {
                            window.location.href = response.url;
                        } else if (response.success && !response.url) {
                            Swal.fire({
                                icon: "success",
                                title: "Succès",
                                text: response.message,
                            });
                            window.location.reload();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Erreur",
                                text: response.message || "Une erreur est survenue.",
                            });
                        }
                    },
                    error: function(xhr) {


                        $("#loader").hide();
                        // Récupérer toutes les erreurs
                        let errors = xhr.responseJSON.message;
                        let errorMessages = "";

                        // Concaténer chaque erreur dans une chaîne
                        $.each(errors, function(key, value) {
                            errorMessages += value[0] + '\n';
                        });

                        // Afficher dans SweetAlert
                        Swal.fire({
                            icon: "error",
                            title: "Erreur de validation",
                            text: errorMessages,
                        });
                    },
                });
            }


            // Bouton Pay Now
            $("#btnPayNow").on("click", function(e) {
                e.preventDefault();
                submitForm("{{ route('event.register.paynow') }}");
            });

            // Bouton Pay Later
            $("#btnPayLater").on("click", function(e) {
                e.preventDefault();
                submitForm("{{ route('event.register.paylater') }}");
            });
        });
    </script>
@endsection
