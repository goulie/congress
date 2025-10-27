<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>AfWASA - @yield('title') </title>

    <link rel="shortcut icon" href="{{ TCG\Voyager\Facades\Voyager::image(setting('site.logo')) }}" type="image/x-icon">
    <link rel="icon" href="{{ TCG\Voyager\Facades\Voyager::image(setting('site.logo')) }}" type="image/x-icon">

    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css"
        integrity="sha512-t7Few9xlddEmgd3oKZQahkNI4dS6l80+eGEzFQiqtyVYdvcSG2D3Iub77R20BdotfRPA9caaRkg1tyaJiPmO0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }

        .banner {
            background-size: cover;
            background-position: center;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }

        .event-details {
            text-align: center;
            margin-bottom: 30px;
        }

        .donation-form {
            max-width: 800px;
            margin: -80px auto 30px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .event-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-title {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #333;
        }

        .amount-selection .btn {
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .amount-selection .btn.active {
            background-color: #0d6efd;
            color: white;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        /* .form-control {
            border: none;
            border-bottom: 2px solid #ccc;
            border-radius: 0;
            padding: 10px 0;
            background-color: transparent;
            transition: border-color 0.3s ease;
        } */

        .form-control:focus {
            box-shadow: none;
            border-bottom-color: #0d6efd;
        }

        /*  .form-label {
            position: absolute;
            top: 10px;
            left: 0;
            color: #999;
            pointer-events: none;
            transition: all 0.3s ease;
        } */

        /* .form-control:focus~.form-label,
        .form-control:not(:placeholder-shown)~.form-label {
            top: -20px;
            font-size: 0.85em;
            color: #0d6efd;
        } */

        .donation-summary {
            text-align: center;
            margin: 30px 0;
            font-size: 1.5rem;
            font-weight: 500;
        }

        .donation-summary span {
            font-weight: 700;
            color: #0d6efd;
        }

        /* Style pour le résumé du don/coût */
        .summary-display {
            text-align: center;
            margin: 15px 0;
            font-size: 1.5rem;
            font-weight: 500;
        }

        .summary-display span {
            font-weight: 700;
            color: #0d6efd;
        }

        /* Titres de section dans le formulaire */
        .form-section-title {
            text-align: center;
            color: #6c757d;
            font-weight: 500;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
        }

        /* NOUVEAU : Styles pour la carte de résumé du coût */
        .cost-summary-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
            background-color: #fafafa;
        }

        .cost-summary-header {
            display: flex;
            align-items: center;
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 15px;
            color: #333;
        }

        .cost-summary-header i {
            color: #0d6efd;
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .is-invalid {
            border: 1px solid red;
        }

        .error {
            font-size: 0.9em;
            color: red;
        }

        /*!
 * Load Awesome v1.1.0 (http://github.danielcardoso.net/load-awesome/)
 * Copyright 2015 Daniel Cardoso <@DanielCardoso>
 * Licensed under MIT
 */
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
    </style>

</head>

<body>
    <div id="loader" class="la-ball-clip-rotate-pulse la-3x"
        style="z-index:9999;display:flex;justify-content:center;align-items:center;position:fixed;top:0px;left:0px;background-color:black;width:100%;height:100%;opacity: 0.75;">
        <div></div>
        <div></div>
    </div>

    <div class="banner text-center"
        style="background-image: url('https://iso.500px.com/wp-content/uploads/2016/03/stock-photo-144748015-1.jpg');">

        <h1>@yield('header')</h1>
    </div>

    <div class="container">


        <div class="donation-form">
            <div class="row">
                <div class="col">
                    <img src="{{ TCG\Voyager\Facades\Voyager::image(setting('site.logo')) }}" alt=""
                        width="auto" height="60">
                </div>
                <div class="col">
                    {{-- <img src="{{ TCG\Voyager\Facades\Voyager::image($event->logo_partner) }}" alt=""
                        style="float: right" width="auto" height="60"> --}}
                </div>
            </div>
            <form method="GET" action="{{ url()->current() }}">
                <i class="bi bi-globe"></i>
                <select name="lang" onchange="this.form.submit()">
                    <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
                </select>
            </form>
            

                <div class="container">
                    @yield('content')
                </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            document.getElementById("loader").style.display = "none";
        }

        $(document).ready(function() {
            // Display none de #branding-footer
            $('#branding-footer').css('display', 'none !important');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{--     <script src="{{ asset('/public/assets_event/main.js') }}"></script>
    <script src="{{ asset('/public/assets_event/script.js') }}"></script> --}}
    @yield('script')
    {{-- <script>
        const amountButtons = document.querySelectorAll('.amount-selection .btn');
        const customAmountInput = document.getElementById('custom-amount');
        const donationAmountSpan = document.getElementById('donation-amount');

        function updateDonationAmount(amount) {
            donationAmountSpan.textContent = `${amount} €`;
        }

        amountButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Mettre à jour le style des boutons
                amountButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                // Mettre à jour le montant
                const amount = button.dataset.amount;
                customAmountInput.value = '';
                updateDonationAmount(amount);
            });
        });

        customAmountInput.addEventListener('input', () => {
            amountButtons.forEach(btn => btn.classList.remove('active'));
            const amount = customAmountInput.value || 0;
            updateDonationAmount(amount);
        });
    </script> --}}
</body>

</html>
