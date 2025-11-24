<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">

<head>
    <title>@yield('page_title', setting('admin.title') . ' - ' . setting('admin.description'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="assets-path" content="{{ route('voyager.voyager_assets') }}" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Favicon -->
    <?php $admin_favicon = Voyager::setting('admin.icon_image', ''); ?>
    @if ($admin_favicon == '')
        <link rel="shortcut icon" href="{{ voyager_asset('images/logo-icon.png') }}" type="image/png">
    @else
        <link rel="shortcut icon" href="{{ Voyager::image($admin_favicon) }}" type="image/png">
    @endif



    <!-- App CSS -->
    <link rel="stylesheet" href="{{ voyager_asset('css/app.css') }}">

    

    @if (__('voyager::generic.is_rtl') == 'true')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.4.0/css/bootstrap-rtl.css">
        <link rel="stylesheet" href="{{ voyager_asset('css/rtl.css') }}">
    @endif

    <!-- Few Dynamic Styles -->
    <style type="text/css">
        .voyager .side-menu .navbar-header {
            background: {{ config('voyager.primary_color', '#22A7F0') }};
            border-color: {{ config('voyager.primary_color', '#22A7F0') }};
        }

        .widget .btn-primary {
            border-color: {{ config('voyager.primary_color', '#22A7F0') }};
        }

        .widget .btn-primary:focus,
        .widget .btn-primary:hover,
        .widget .btn-primary:active,
        .widget .btn-primary.active,
        .widget .btn-primary:active:focus {
            background: {{ config('voyager.primary_color', '#22A7F0') }};
        }

        .voyager .breadcrumb a {
            color: {{ config('voyager.primary_color', '#22A7F0') }};
        }

        /* Load Awesome v1.1.0 (http: //github.danielcardoso.net/load-awesome/)

            * Copyright 2015 Daniel Cardoso <@DanielCardoso> * Licensed under MIT */
        .la-ball-clip-rotate-pulse,
        .la-ball-clip-rotate-pulse>div {
            position: relative;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .la-ball-clip-rotate-pulse {
            display: block;
            font-size: 0;
            color: #fff;
        }

        .la-ball-clip-rotate-pulse.la-dark {
            color: #333;
        }

        .la-ball-clip-rotate-pulse>div {
            display: inline-block;
            float: none;
            background-color: currentColor;
            border: 0 solid currentColor;
        }

        .la-ball-clip-rotate-pulse {
            width: 32px;
            height: 32px;
        }

        .la-ball-clip-rotate-pulse>div {
            position: absolute;
            top: 50%;
            left: 50%;
            border-radius: 100%;
        }

        .la-ball-clip-rotate-pulse>div:first-child {
            position: absolute;
            width: 32px;
            height: 32px;
            background: transparent;
            border-style: solid;
            border-width: 2px;
            border-right-color: transparent;
            border-left-color: transparent;
            -webkit-animation: ball-clip-rotate-pulse-rotate 1s cubic-bezier(.09, .57, .49, .9) infinite;
            -moz-animation: ball-clip-rotate-pulse-rotate 1s cubic-bezier(.09, .57, .49, .9) infinite;
            -o-animation: ball-clip-rotate-pulse-rotate 1s cubic-bezier(.09, .57, .49, .9) infinite;
            animation: ball-clip-rotate-pulse-rotate 1s cubic-bezier(.09, .57, .49, .9) infinite;
        }

        .la-ball-clip-rotate-pulse>div:last-child {
            width: 16px;
            height: 16px;
            -webkit-animation: ball-clip-rotate-pulse-scale 1s cubic-bezier(.09, .57, .49, .9) infinite;
            -moz-animation: ball-clip-rotate-pulse-scale 1s cubic-bezier(.09, .57, .49, .9) infinite;
            -o-animation: ball-clip-rotate-pulse-scale 1s cubic-bezier(.09, .57, .49, .9) infinite;
            animation: ball-clip-rotate-pulse-scale 1s cubic-bezier(.09, .57, .49, .9) infinite;
        }

        .la-ball-clip-rotate-pulse.la-sm {
            width: 16px;
            height: 16px;
        }

        .la-ball-clip-rotate-pulse.la-sm>div:first-child {
            width: 16px;
            height: 16px;
            border-width: 1px;
        }

        .la-ball-clip-rotate-pulse.la-sm>div:last-child {
            width: 8px;
            height: 8px;
        }

        .la-ball-clip-rotate-pulse.la-2x {
            width: 64px;
            height: 64px;
        }

        .la-ball-clip-rotate-pulse.la-2x>div:first-child {
            width: 64px;
            height: 64px;
            border-width: 4px;
        }

        .la-ball-clip-rotate-pulse.la-2x>div:last-child {
            width: 32px;
            height: 32px;
        }

        .la-ball-clip-rotate-pulse.la-3x {
            width: 96px;
            height: 96px;
        }

        .la-ball-clip-rotate-pulse.la-3x>div:first-child {
            width: 96px;
            height: 96px;
            border-width: 6px;
        }

        .la-ball-clip-rotate-pulse.la-3x>div:last-child {
            width: 48px;
            height: 48px;
        }

        /*
 * Animations
 */
        @-webkit-keyframes ball-clip-rotate-pulse-rotate {
            0% {
                -webkit-transform: translate(-50%, -50%) rotate(0deg);
                transform: translate(-50%, -50%) rotate(0deg);
            }

            50% {
                -webkit-transform: translate(-50%, -50%) rotate(180deg);
                transform: translate(-50%, -50%) rotate(180deg);
            }

            100% {
                -webkit-transform: translate(-50%, -50%) rotate(360deg);
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @-moz-keyframes ball-clip-rotate-pulse-rotate {
            0% {
                -moz-transform: translate(-50%, -50%) rotate(0deg);
                transform: translate(-50%, -50%) rotate(0deg);
            }

            50% {
                -moz-transform: translate(-50%, -50%) rotate(180deg);
                transform: translate(-50%, -50%) rotate(180deg);
            }

            100% {
                -moz-transform: translate(-50%, -50%) rotate(360deg);
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @-o-keyframes ball-clip-rotate-pulse-rotate {
            0% {
                -o-transform: translate(-50%, -50%) rotate(0deg);
                transform: translate(-50%, -50%) rotate(0deg);
            }

            50% {
                -o-transform: translate(-50%, -50%) rotate(180deg);
                transform: translate(-50%, -50%) rotate(180deg);
            }

            100% {
                -o-transform: translate(-50%, -50%) rotate(360deg);
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @keyframes ball-clip-rotate-pulse-rotate {
            0% {
                -webkit-transform: translate(-50%, -50%) rotate(0deg);
                -moz-transform: translate(-50%, -50%) rotate(0deg);
                -o-transform: translate(-50%, -50%) rotate(0deg);
                transform: translate(-50%, -50%) rotate(0deg);
            }

            50% {
                -webkit-transform: translate(-50%, -50%) rotate(180deg);
                -moz-transform: translate(-50%, -50%) rotate(180deg);
                -o-transform: translate(-50%, -50%) rotate(180deg);
                transform: translate(-50%, -50%) rotate(180deg);
            }

            100% {
                -webkit-transform: translate(-50%, -50%) rotate(360deg);
                -moz-transform: translate(-50%, -50%) rotate(360deg);
                -o-transform: translate(-50%, -50%) rotate(360deg);
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @-webkit-keyframes ball-clip-rotate-pulse-scale {

            0%,
            100% {
                opacity: 1;
                -webkit-transform: translate(-50%, -50%) scale(1);
                transform: translate(-50%, -50%) scale(1);
            }

            30% {
                opacity: .3;
                -webkit-transform: translate(-50%, -50%) scale(.15);
                transform: translate(-50%, -50%) scale(.15);
            }
        }

        @-moz-keyframes ball-clip-rotate-pulse-scale {

            0%,
            100% {
                opacity: 1;
                -moz-transform: translate(-50%, -50%) scale(1);
                transform: translate(-50%, -50%) scale(1);
            }

            30% {
                opacity: .3;
                -moz-transform: translate(-50%, -50%) scale(.15);
                transform: translate(-50%, -50%) scale(.15);
            }
        }

        @-o-keyframes ball-clip-rotate-pulse-scale {

            0%,
            100% {
                opacity: 1;
                -o-transform: translate(-50%, -50%) scale(1);
                transform: translate(-50%, -50%) scale(1);
            }

            30% {
                opacity: .3;
                -o-transform: translate(-50%, -50%) scale(.15);
                transform: translate(-50%, -50%) scale(.15);
            }
        }

        @keyframes ball-clip-rotate-pulse-scale {

            0%,
            100% {
                opacity: 1;
                -webkit-transform: translate(-50%, -50%) scale(1);
                -moz-transform: translate(-50%, -50%) scale(1);
                -o-transform: translate(-50%, -50%) scale(1);
                transform: translate(-50%, -50%) scale(1);
            }

            30% {
                opacity: .3;
                -webkit-transform: translate(-50%, -50%) scale(.15);
                -moz-transform: translate(-50%, -50%) scale(.15);
                -o-transform: translate(-50%, -50%) scale(.15);
                transform: translate(-50%, -50%) scale(.15);
            }
        }

        .support-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .support-btn {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0015ff 0%, #1f02f8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 47, 255, 0.4);
            cursor: pointer;
            border: none;
            position: relative;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .support-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(2, 2, 253, 0.6);
        }

        .support-btn i {
            color: white !important;
            font-size: 24px;
        }

        /* Effet de pulsation */
        .pulse-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 2px solid #ff0000;
            border-radius: 50%;
            animation: pulse 2s infinite;
            opacity: 0;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.4;
            }

            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        /* Tooltip */
        .support-tooltip {
            position: absolute;
            right: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .support-tooltip::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-left-color: #333;
        }

        .support-btn:hover .support-tooltip {
            opacity: 1;
            visibility: visible;
        }

        .required::after {
            content: ' *';
            color: red;
            font-weight: bold;
        }
    </style>
    
@yield('css')
    @if (!empty(config('voyager.additional_css')))<!-- Additional CSS -->
        @foreach (config('voyager.additional_css') as $css)
            <link rel="stylesheet" type="text/css" href="{{ asset($css) }}">
        @endforeach
    @endif

    @yield('head')
</head>

<body class="voyager @if (isset($dataType) && isset($dataType->slug)) {{ $dataType->slug }} @endif">

    <div id="voyager-loader" class="la-ball-clip-rotate-pulse la-3x"
        style="z-index:9999;display:flex;justify-content:center;align-items:center;position:fixed;top:0px;left:0px;background-color:black;width:100%;height:100%;opacity: 0.75;">
        <div></div>
        <div></div>
    </div>

    <div id="loader" class="hidden la-ball-clip-rotate-pulse la-3x"
        style="z-index:9999;display:flex;justify-content:center;align-items:center;position:fixed;top:0px;left:0px;background-color:black;width:100%;height:100%;opacity: 0.75;">
        <div></div>
        <div></div>
    </div>
    {{-- <div id="voyager-loader"> --}}
    <?php /* $admin_loader_img = Voyager::setting('admin.loader', ''); */ ?>
    {{--  @if ($admin_loader_img == '')
            <img src="{{ voyager_asset('images/logo-icon.png') }}" alt="Voyager Loader">
        @else
            <img src="{{ Voyager::image($admin_loader_img) }}" alt="Voyager Loader">
        @endif
    </div> --}}

    <?php
    if (\Illuminate\Support\Str::startsWith(Auth::user()->avatar, 'http://') || \Illuminate\Support\Str::startsWith(Auth::user()->avatar, 'https://')) {
        $user_avatar = Auth::user()->avatar;
    } else {
        $user_avatar = Voyager::image(Auth::user()->avatar);
    }
    ?>

    <div class="app-container">
        <div class="fadetoblack visible-xs"></div>
        <div class="row content-container">
            @include('voyager::dashboard.navbar')
            @include('voyager::dashboard.sidebar')
            <script>
                (function() {
                    var appContainer = document.querySelector('.app-container'),
                        sidebar = appContainer.querySelector('.side-menu'),
                        navbar = appContainer.querySelector('nav.navbar.navbar-top'),
                        loader = document.getElementById('voyager-loader'),
                        hamburgerMenu = document.querySelector('.hamburger'),
                        sidebarTransition = sidebar.style.transition,
                        navbarTransition = navbar.style.transition,
                        containerTransition = appContainer.style.transition;

                    sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition =
                        appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition =
                        navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = 'none';

                    if (window.innerWidth > 768 && window.localStorage && window.localStorage['voyager.stickySidebar'] ==
                        'true') {
                        appContainer.className += ' expanded no-animation';
                        loader.style.left = (sidebar.clientWidth / 2) + 'px';
                        hamburgerMenu.className += ' is-active no-animation';
                    }

                    navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = navbarTransition;
                    sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition = sidebarTransition;
                    appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition =
                        containerTransition;
                })();
            </script>
            <!-- Main Content -->
            <div class="container-fluid">
                <div class="side-body padding-top">
                    @yield('page_header')
                    <div id="voyager-notifications"></div>
                    @yield('content')



                    <!-- The modal -->
                    <div class="modal fade" id="flipFlop" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="modalLabel">
                                        <i class="bi bi-headset me-2"></i>Technical Support
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <!-- Contact Information Section -->
                                    <div class="contact-section mb-4">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-info-circle"></i> How to Contact Us
                                        </h5>

                                        <div class="contact-item mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-telephone text-success me-3 fs-5"></i>
                                                <div>
                                                    <strong>Phone</strong>
                                                    <div class="text-muted">+1 234 567 890</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="contact-item mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-whatsapp text-success me-3 fs-5"></i>
                                                <div>
                                                    <strong>WhatsApp</strong>
                                                    <div class="text-muted">+1 234 567 891</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="contact-item mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-envelope text-primary me-3 fs-5"></i>
                                                <div>
                                                    <strong>Email</strong>
                                                    <div class="text-muted">support@congress.com</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Schedule Section -->
                                    <div class="schedule-section mb-4">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-clock"></i> Opening Hours
                                        </h5>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Mon - Fri</strong><br>
                                                8:00 AM - 6:00 PM
                                            </div>
                                            <div class="col-6">
                                                <strong>Sat - Sun</strong><br>
                                                9:00 AM - 1:00 PM
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Help Section -->
                                    <div class="quick-help-section">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-lightning"></i> Quick Assistance
                                        </h5>
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="bi bi-lightbulb"></i>
                                                <strong>Tip:</strong> For faster assistance, please have your
                                                participant number and problem description ready.
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Emergency Section -->
                                    <div class="emergency-section mt-4">
                                        <div class="alert alert-warning">
                                            <h6 class="alert-heading">
                                                <i class="bi bi-exclamation-triangle"></i> Technical Emergency
                                            </h6>
                                            <small class="mb-0">
                                                For urgent issues preventing your registration, contact us immediately
                                                by phone.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="bi bi-x-circle"></i> Close
                                    </button>
                                    <a href="tel:+1234567890" class="btn btn-success">
                                        <i class="bi bi-telephone"></i> Call
                                    </a>
                                    <a href="mailto:support@congress.com" class="btn btn-primary">
                                        <i class="bi bi-envelope"></i> Email
                                    </a>
                                    <a href="https://wa.me/1234567891" class="btn btn-success"
                                        style="background-color: #25D366; border-color: #25D366;">
                                        <i class="bi bi-whatsapp"></i> WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    </div>

    @include('voyager::partials.app-footer')

    <!-- Javascript Libs -->


    <script type="text/javascript" src="{{ voyager_asset('js/app.js') }}"></script>
    <script>
        // Initialize tooltip component
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        // Initialize popover component
        $(function() {
            $('[data-toggle="popover"]').popover()
        })
    </script>
    <script>
        $('.ajax-form').on('submit', function() {
            let submitBtn = $(this).find('button[type="submit"]');

            // Désactiver le bouton pour éviter plusieurs clics
            submitBtn.prop('disabled', true);

            // Remplacer le texte par un loader
            submitBtn.html(
                '<i class="bi bi-hourglass-split spinner-border spinner-border-sm me-2"></i> Enregistrement...'
            );

            // Le formulaire continue sa soumission normale
            return true;
        });
        @if (Session::has('alerts'))
            let alerts = {!! json_encode(Session::get('alerts')) !!};
            helpers.displayAlerts(alerts, toastr);
        @endif

        @if (Session::has('message'))

            // TODO: change Controllers to use AlertsMessages trait... then remove this
            var alertType = {!! json_encode(Session::get('alert-type', 'info')) !!};
            var alertMessage = {!! json_encode(Session::get('message')) !!};
            var alerter = toastr[alertType];

            if (alerter) {
                alerter(alertMessage);
            } else {
                toastr.error("toastr alert-type " + alertType + " is unknown");
            }
        @endif
    </script>
    @include('voyager::media.manager')
    @yield('javascript')
    @stack('javascript')
    @if (!empty(config('voyager.additional_js')))<!-- Additional Javascript -->
        @foreach (config('voyager.additional_js') as $js)
            <script type="text/javascript" src="{{ asset($js) }}?v={{ time() }}" defer></script>
        @endforeach
    @endif



</body>

</html>
