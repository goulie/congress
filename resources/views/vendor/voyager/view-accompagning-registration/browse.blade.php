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

        .required::after {
            content: ' *';
            color: red;
            font-weight: bold;
        }

        /* DataTable Small visual polish */
        .panel-custom {
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(1, 33, 160, 0.08);
            border: 1px solid rgba(1, 33, 160, 0.08);
        }

        .table-toolbar {
            padding: 12px 16px;
            border-bottom: 1px solid #eef3ff;
            background: #ffffff;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .table-toolbar .btn+.btn {
            margin-left: 8px;
        }

        .table-responsive {
            padding: 12px 16px;
        }

        .table>tbody>tr>td.actions {
            white-space: nowrap;
            width: 210px;
        }

        .action-btn {
            margin-right: 6px;
        }

        .table>tbody>tr:hover {
            background: #fbfdff;
        }

        .small-muted {
            font-size: 12px;
            color: #6c757d;
        }

        .pagination {
            margin: 0;
        }

        .search-input {
            max-width: 320px;
            display: inline-block;
            margin-left: 12px;
        }

        @media (max-width: 768px) {
            .table-toolbar {
                text-align: center;
            }

            .search-input {
                display: block;
                margin: 8px auto 0;
            }
        }

        /* View details */
        .view-details {
            transition: all 0.3s ease;
        }

        .view-details:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
        }

        .participant-photo-container img {
            object-fit: cover;
            border-radius: 4px;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .table-sm td {
            padding: 0.3rem 0.5rem;
        }

        .badge {
            font-size: 0.75em;
        }
    </style>
@endsection

@section('page_title', __('Registration Form'))

@section('content')
    @php
        $congres = App\Models\Congress::latest('id')->first();
        $participants = App\Models\Participant::where([
            'user_id' => auth()->user()->id,
            'congres_id' => $congres->id,
            'type_participant' => 'accompagning',
        ])->get();

        $categories = App\Models\CategorieRegistrant::forCongress($congres->id);
        $dinner = App\Models\CategorieRegistrant::DinnerforCongress($congres->id);
        $accompanying = App\Models\CategorieRegistrant::accompanyingPersonForCongress($congres->id);
        $tours = App\Models\CategorieRegistrant::ToursforCongress($congres->id);

    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="box-header">
                        <h1>{{ app()->getLocale() == 'fr' ? 'Enregistrement de personne accompagnante' : 'Accompanying person registration' }}
                        </h1>
                    </div>

                    @php
                        $edit = $edit ?? false;

                    @endphp

                    @if ($edit)
                        @include('vendor.voyager.view-accompagning-registration.edit')
                    @else
                        @include('vendor.voyager.view-accompagning-registration.form')
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

    <!-- Inclure SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script pour gérer les alertes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gérer les alertes SweetAlert
            @if (session('swal'))
                Swal.fire({
                    icon: '{{ session('swal.icon') }}',
                    title: '{{ session('swal.title') }}',
                    text: '{{ session('swal.text') }}',
                    confirmButtonText: '{{ session('swal.confirmButtonText') }}',
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            // Gérer les alertes classiques (fallback)
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '{{ app()->getLocale() == 'fr' ? 'Succès !' : 'Success!' }}',
                    text: '{{ session('success') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'OK' : 'OK' }}',
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ app()->getLocale() == 'fr' ? 'Erreur !' : 'Error!' }}',
                    text: '{{ session('error') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'OK' : 'OK' }}',
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: '{{ app()->getLocale() == 'fr' ? 'Attention !' : 'Warning!' }}',
                    text: '{{ session('warning') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'OK' : 'OK' }}',
                });
            @endif

            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: '{{ app()->getLocale() == 'fr' ? 'Information' : 'Information' }}',
                    text: '{{ session('info') }}',
                    confirmButtonText: '{{ app()->getLocale() == 'fr' ? 'OK' : 'OK' }}',
                });
            @endif
        });
    </script>
@endsection
