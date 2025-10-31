@extends('voyager::master')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/AdminLTE.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />

    <style>
        body {
            background: linear-gradient(135deg, #eaf3ff, #ffffff);
            font-family: 'Segoe UI', sans-serif;
            padding: 40px 0;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
        }

        .box-header {
            background: #2c80ff;
            color: #fff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .box-title {
            font-size: 22px;
            font-weight: 600;
        }

        .help-block {
            color: #eef;
            font-style: italic;
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .step-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 3px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #777;
            transition: all 0.3s;
            background: #fff;
        }

        .step.active .step-circle {
            border-color: #2c80ff;
            background: #2c80ff;
            color: white;
            transform: scale(1.1);
        }

        .step.completed .step-circle {
            background: #00a65a;
            border-color: #00a65a;
            color: white;
        }

        .step-label {
            margin-top: 8px;
            font-size: 13px;
            text-align: center;
            color: #555;
        }

        .step-divider {
            width: 70px;
            height: 3px;
            background: #ccc;
            margin: 0 15px;
            border-radius: 3px;
        }

        .form-control {
            border-radius: 6px;
            height: 40px;
            font-size: 14px;
        }

        .control-label i {
            color: #2c80ff;
            margin-right: 8px;
        }

        .btn-primary {
            background: #2c80ff;
            border: none;
            border-radius: 5px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #1a5fd0;
        }

        .btn-outline {
            border: 2px solid #2c80ff;
            background: none;
            color: #2c80ff;
            font-weight: 600;
            border-radius: 5px;
        }

        .btn-outline:hover {
            background: #2c80ff;
            color: #fff;
        }

        .box-footer {
            background: #f7f9fc;
            border-top: 1px solid #ddd;
            padding: 15px 25px;
            border-radius: 0 0 10px 10px;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Telephone */
        /* Correction pour l'intégration Bootstrap */
        /* Style minimal pour intl-tel-input */
        .iti {
            width: 100%;
        }

        .iti__flag {
            background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/img/flags.png");
        }

        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/img/flags@2x.png");
            }
        }

        .iti__selected-flag {
            padding: 0 10px;
            border-radius: 3px 0 0 3px;
        }

        .iti__country-list {
            z-index: 1000;
        }

        .intl-tel-input {
            width: 100%;
            display: block;
        }

        .form-control.iti-input {
            padding-left: 52px !important;
        }
    </style>
@endsection

@section('page_title', __('Registration Form'))

@section('content')
    @php
        $step = Session::get('step') ?? 1;
        $congres = App\Models\Congress::latest('id')->first();
        $participant = App\Models\Participant::where([
            'registration_id' => auth()->user()->user_id,
            'congres_id' => $congres->id,
            'type_participant' => 'individual',
        ])
            ->latest()
            ->first();
        if (!$participant) {
            $participant = App\Models\Participant::create([
                'user_id' => auth()->user()->id,
                'registration_id' => auth()->user()->user_id,
                'congres_id' => $congres->id,
                'type_participant' => 'individual',
            ]);
        }
    @endphp
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="card">
                    <div class="box-header">
                        <h3 class="box-title">@lang('Register in 3 Easy Steps')</h3>
                        <p class="help-block">@lang("S'inscrire en 3 étapes simples")</p>
                    </div>

                    <!-- Étapes -->
                    <div class="progress-steps">
                        <div class="step {{ $step == 1 ? 'active' : '' }}">
                            <div class="step-circle">1</div>
                            <div class="step-label">
                                {{ __('registration.step1.label') }}<br>
                                {{-- {{ __('registration.step1.subtitle') }} --}}
                            </div>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step {{ $step == 2 ? 'active' : '' }}">
                            <div class="step-circle">2</div>
                            <div class="step-label">
                                {{ __('registration.step2.label') }}<br>
                                {{-- {{ __('registration.step2.subtitle') }} --}}
                            </div>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step {{ $step == 3 ? 'active' : '' }}">
                            <div class="step-circle">3</div>
                            <div class="step-label">
                                {{ __('registration.step3.label') }}<br>
                                {{-- {{ __('registration.step3.subtitle') }} --}}
                            </div>
                        </div>
                    </div>

                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    
                    @if ($step == 1)
                        @include('vendor.voyager.view-single-registrations.step1')
                    @endif
                    @if ($step == 2)
                        @include('vendor.voyager.view-single-registrations.step2')
                    @endif
                    @if ($step == 3)
                        @include('vendor.voyager.view-single-registrations.step3')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        // Trigger file input when button is clicked
        document.getElementById('photo_passeport_btn').addEventListener('click', function() {
            document.getElementById('photo_passeport_input').click();
        });

        // Display selected file name in text input
        document.getElementById('photo_passeport_input').addEventListener('change', function() {
            const fileName = this.files[0]?.name || '';
            document.getElementById('photo_passeport_text').value = fileName;
        });

        // Affiche le champ 'Autre type organisation' si sélectionnée
        document.getElementById('type_organisation').addEventListener('change', function() {
            const autreDiv = document.getElementById('autre_type_org_div');
            if (this.value === 'autre') {
                autreDiv.classList.remove('d-none');
            } else {
                autreDiv.classList.add('d-none');
            }
        });
    </script>
@endsection
