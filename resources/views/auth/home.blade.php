<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">

    <title>AWASA Event plateform</title>


    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="shortcut icon"
        href="https://meetings.afwasa.org/public/storage/settings/June2024/sEFw7DUnhTUfOg8alB4u.jpg"
        type="image/x-icon">
    <link rel="icon" href="https://meetings.afwasa.org/public/storage/settings/June2024/sEFw7DUnhTUfOg8alB4u.jpg"
        type="image/x-icon">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>


</head>

<body>

    <main>
        <div class="container-fluid py-4">
            <header class="pb-3 mb-4 ">
                <div class="row">
                    <div class="logo-container d-flex justify-content-between align-items-center">
                        <!-- Logo à gauche -->
                        <img src="{{ TCG\Voyager\Facades\Voyager::image(setting('site.logo')) }}" alt=""
                            width="auto" height="80">

                        <img src="{{ TCG\Voyager\Facades\Voyager::image($event->logo_partner) }}" alt=""
                            style="float: right" width="auto" height="80">
                        {{-- <img src="{{ TCG\Voyager\Facades\Voyager::image(setting('site.logo')) }}" alt="AfWASA Logo" class="logo">

                        <!-- Logo à droite -->
                        <img src="{{ __('eventhome.logo_hwp') }}" alt="HWP Logo" class="logo"> --}}
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h1 class="bg-primary text-white p-5 text-center fw-bold ">
                            {{ __('eventhome.title') }}
                        </h1>
                    </div>
                </div>
            </header>

        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center">

                    <div class="language-switcher mb-3">
                        <label
                            for="language-select">{{ app()->getLocale() == 'fr' ? 'Traduction' : 'Translation' }}</label>
                        <select name="lang" id="language-select" onchange="window.location.href = this.value;">

                            <option value="{{ route('events.home', ['lang' => 'fr']) }}"
                                {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="{{ route('events.home', ['lang' => 'en']) }}"
                                {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
                        </select>
                        {{-- <a href="{{ route('events.home', ['lang' => 'fr']) }}"
                            class="btn btn-sm btn-outline-primary {{ app()->getLocale() == 'fr' ? 'active' : '' }}">FR</a>
                        <a href="{{ route('events.home', ['lang' => 'en']) }}"
                            class="btn btn-sm btn-outline-primary {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a> --}}
                    </div>

                </div>
            </div>
            <div class="row">
                <!-- Main Content -->
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm p-4">
                        <div class="card-body">

                            <p class="text-justify">{!! __('eventhome.intro_html') !!}
                            </p>
                            <div class="card shadow-sm my-4 p-4 bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary fw-bold">{{ __('eventhome.overview.title') }}
                                    </h5>
                                    <p class="card-text mb-2">
                                        <strong>{{ __('eventhome.overview.location_label') }}:</strong>
                                        {{ __('eventhome.overview.location') }}
                                    </p>
                                    <p class="card-text mb-0">
                                        <strong>{{ __('eventhome.overview.dates_label') }}:</strong>
                                        {{ __('eventhome.overview.dates') }}
                                    </p>
                                </div>
                            </div>

                            <p>{!! __('eventhome.more_than_visit') !!}</p>

                            <div class="text-center my-4">
                                {{-- lazy --}}
                                <img loading="lazy" src="{{ asset('/public/ghana.jpg') }}" alt="Event"
                                    class="img-fluid rounded shadow">
                            </div>

                            <p>{{-- {!! __('eventhome.dates_range') !!} <br> --}}
                                {!! __('eventhome.conference_paragraph') !!}</p>

                            <h5 class="mt-4 text-primary">
                                <strong><em>{{ __('eventhome.profile.title') }}</em></strong>
                            </h5>
                            <p>{!! __('eventhome.profile.description') !!}</p>


                            <h5 class="mt-4 text-primary">
                                <strong><em>{{ __('eventhome.key_focus_title') }}</em></strong>
                            </h5>
                            <ul class="list-styled ps-3">
                                <li class="pb-2">{{ __('eventhome.focus.drinking') }}</li>
                                <li class="pb-2">{{ __('eventhome.focus.wastewater') }}</li>
                                <li class="pb-2">{{ __('eventhome.focus.nrw') }}</li>
                                <li class="pb-2">{{ __('eventhome.focus.smart') }}</li>
                                <li class="pb-2">{{ __('eventhome.focus.rainwater') }}</li>
                                <li class="pb-2">{{ __('eventhome.focus.case_studies') }}</li>
                            </ul>

                            <p class="mt-3">{!! __('eventhome.opportunity_paragraph') !!}</p>

                            <h5 class="mt-4 text-primary">
                                <strong><em>{{ __('eventhome.programme.title') }}</em></strong>
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <td colspan="2"><strong>{{ __('eventhome.programme.day0') }}</strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2"><strong>{{ __('eventhome.programme.day1') }}</strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2"><strong>{{ __('eventhome.programme.day2') }}</strong>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2"><strong>{{ __('eventhome.programme.day3') }}</strong>
                                            </td>
                                        </tr>
                                        @if (__('eventhome.programme.day3_desc') != '')
                                            <tr>
                                                <td colspan="2">{{ __('eventhome.programme.day3_desc') }}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td colspan="2"><strong>{{ __('eventhome.programme.day4') }}</strong>
                                            </td>
                                        </tr>
                                        @if (__('eventhome.programme.day4_desc') != '')
                                            <tr>
                                                <td colspan="2">{{ __('eventhome.programme.day4_desc') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>


                            <h5 class="mt-4 text-primary">
                                <strong><em>{{ __('eventhome.logistics.title') }}</em></strong>
                            </h5>

                            <p class="mt-3">{!! nl2br(__('eventhome.logistics.accommodation')) !!}</p>
                            <p class="mt-3">{!! nl2br(__('eventhome.logistics.transport')) !!}</p>
                            <p class="mt-3">{!! nl2br(__('eventhome.logistics.visa')) !!}</p>
                            <p class="mt-3">{!! nl2br(__('eventhome.logistics.health')) !!}</p>
                            <p class="mt-3">{!! nl2br(__('eventhome.logistics.b2b')) !!}</p>

                            <h5 class="mt-4 text-primary">
                                <strong><em>{!! __('eventhome.logistics.contact_title') !!}</em></strong>
                            </h5>
                            <p class="mt-3">{!! nl2br(__('eventhome.logistics.contact_info')) !!}</p>
                            <p class="mt-3">{!! nl2br(__('eventhome.logistics.contact_footer')) !!}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body">

                            <div class="mb-3 pb-2 border-bottom">
                                <small class="text-muted">{{ __('eventhome.sidebar.location_label') }}</small><br>
                                <span class="fw-bold text-primary">{{ __('eventhome.sidebar.location') }}</span>
                            </div>

                            <div class="mb-3 pb-2 border-bottom">
                                <small class="text-muted">{{ __('eventhome.sidebar.language_label') }}</small><br>
                                <span class="fw-bold text-primary">{{ __('eventhome.sidebar.language') }}</span>
                            </div>

                            <div class="mb-3 pb-2 border-bottom">
                                <small class="text-muted">{{ __('eventhome.sidebar.date_label') }}</small><br>
                                <span class="fw-bold text-primary">{{ __('eventhome.sidebar.date') }}</span>
                            </div>

                            <div class="mb-3 pb-2 border-bottom">
                                <small class="text-muted">{{ __('eventhome.sidebar.contact_label') }}</small><br>
                                <span class="fw-bold text-primary">
                                    <a href="mailto:{{ __('eventhome.sidebar.contact_email') }}"
                                        class="text-decoration-none text-primary">
                                        {{ __('eventhome.sidebar.contact_email') }}
                                    </a><br>
                                    <a href="tel:{{ __('eventhome.sidebar.contact_phone') }}"
                                        class="text-decoration-none text-primary">
                                        {{ __('eventhome.sidebar.contact_phone') }}
                                    </a>
                                </span>
                            </div>

                            <div class="mt-4">
                                <div class="p-3 text-center border border-3 border-primary rounded bg-white">
                                    <h5 class="fw-bold text-dark mb-2">
                                        {{ __('eventhome.sidebar.participation_title') }}
                                    </h5>
                                    <p class="fs-5 mb-3">
                                        {!! __('eventhome.sidebar.participation_info') !!}</p>
                                    
                                </div>

                            </div>
                            <div class="mt-4">
                                <div class="p-3 text-center border border-3 border-secondary rounded bg-white">
                                    <h5 class="fw-bold text-dark mb-2">
                                        {{ __('eventhome.sidebar.visite_event_title') }}
                                    </h5>
                                    <p class="fs-5 mb-3">
                                        {{ __('eventhome.sidebar.visite_event_content') }}</p>
                                    <a target="_blank"
                                        href="https://www.hungarianwaterpartnership.hu/events/afwasa-hwp-global-water-and-sanitation-connect-2026"
                                        class="btn btn-secondary fw-bold px-4">
                                        {{ __('eventhome.sidebar.visite_event_button') }}
                                    </a>
                                </div>

                                <div class="mt-4">
                                    <div class="p-3 text-center border border-3 border-primary rounded bg-white">
                                        <h5 class="fw-bold text-dark mb-2">
                                            {{ __('eventhome.sidebar.registration_title') }}
                                        </h5>
                                        <p class="text-danger fw-bold fs-5 mb-3">
                                            {{ __('eventhome.sidebar.registration_deadline') }}</p>
                                        <a href="https://meetings.afwasa.org/events/reg/3614aa35-6013-42b8-a0d1-cc00c13510a3"
                                            class="btn btn-primary fw-bold px-4">
                                            {{ __('eventhome.sidebar.registration_button') }}
                                        </a>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <footer class="pt-3 mt-4 text-muted border-top">
                    &copy; {{ date('Y') }}
                </footer>
            </div>
    </main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
