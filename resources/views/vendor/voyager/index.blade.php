@extends('voyager::master')

@section('css')
    <style>
        /* === GENERAL STYLES === */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard {
            margin-top: 40px;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #3f51b5, #2196f3);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .welcome-banner h3 {
            margin: 0;
            font-weight: 600;
        }

        .welcome-banner p {
            margin-top: 10px;
            opacity: 0.9;
        }

        /* === DASHBOARD CARDS === */
        .dash-btn {
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 35px 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.35s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dash-btn:hover {
            background: #f3f7ff;
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        .dash-btn i {
            font-size: 2.5em;
            margin-bottom: 12px;
            color: #3f51b5;
            transition: transform 0.3s ease;
        }

        .dash-btn:hover i {
            transform: scale(1.2);
        }

        .dash-btn h4 {
            font-weight: 600;
            color: #222;
            margin-bottom: 10px;
        }

        .dash-btn p {
            font-size: 0.9em;
            color: #666;
            margin: 0;
        }

        /* === STATUS BADGES === */
        .status-badge {
            display: inline-block;
            font-size: 0.75em;
            padding: 5px 10px;
            border-radius: 20px;
            margin-top: 8px;
        }

        .status-paid {
            background: #c8f7c5;
            color: #2e7d32;
        }

        .status-unpaid {
            background: #ffd6d6;
            color: #b71c1c;
        }

        /* === PERIOD CARD === */
        .period-card {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .period-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .period-card h3 {
            font-weight: 600;
            margin-bottom: 15px;
            position: relative;
        }

        .period-card .current-period {
            font-size: 1.1em;
            margin-bottom: 20px;
            position: relative;
        }

        .period-card .period-dates {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            position: relative;
        }

        .period-card .period-date {
            text-align: center;
            flex: 1;
        }

        .period-card .period-date span {
            display: block;
            font-size: 0.9em;
            opacity: 0.8;
        }

        .period-card .period-date strong {
            font-size: 1.2em;
            font-weight: 600;
        }

        .period-card .package-info {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            position: relative;
        }

        .period-card .package-name {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .period-card .package-price {
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .period-card .package-features {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .period-card .after-deadline {
            background: rgba(216, 160, 7, 0.938);
            border-radius: 10px;
            padding: 12px 15px;
            position: relative;
        }

        .period-card .after-deadline h5 {
            font-size: 1em;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .period-card .after-deadline p {
            margin: 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        /* === MEMBER STATUS === */
        .member-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            animation: pulse 1.5s infinite;
        }

        .member-active {
            background-color: #4caf50;
        }

        /* vert */
        .member-inactive {
            background-color: #f44336;
        }

        /* rouge */

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.3);
                opacity: 1;
            }

            100% {
                transform: scale(1);
                opacity: 0.7;
            }
        }

        /* === RESPONSIVE === */
        @media (max-width: 767px) {
            .dash-btn {
                margin-bottom: 20px;
            }

            .period-card .period-dates {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $periode = App\Models\Periode::PeriodeActive(App\Models\Congress::latest()->first()->id);

        $locale = app()->getLocale(); // fr ou en
        \Carbon\Carbon::setLocale($locale);

        $start = \Carbon\Carbon::parse($periode->start_date);
        $end = \Carbon\Carbon::parse($periode->end_date);

        $daysRemaining = now()->diffInDays($end, false); // false -> return negative si fini

        // Format date selon locale
        $dateFormattedStart = $locale === 'fr' ? $start->translatedFormat('d F Y') : $start->translatedFormat('F d, Y');

        $dateFormattedEnd = $locale === 'fr' ? $end->translatedFormat('d F Y') : $end->translatedFormat('F d, Y');
    @endphp
    <div class="page-content">
        <div class="container">
            <!-- Bandeau de bienvenue -->
            <div class="welcome-banner">
                <h3>👋 Bienvenue, Participant !</h3>
                <p>Gérez votre inscription et votre participation à l'événement</p>
            </div>

            <!-- Carte de période et package -->
            <div class="period-card">
                <h3>Période d'Inscription Actuelle</h3>
                <div class="current-period">
                    <i class="bi bi-calendar-event me-2"></i>
                    Vous êtes dans la période: <strong style="font-weight: bold">
                        {{ $periode->translate(app()->getLocale(), 'fallbackLocale')->libelle }}
                    </strong>
                </div>

                <div class="period-dates">
                    <div class="period-date">
                        <span>Début</span>
                        <strong>{{ $dateFormattedStart }}</strong>
                    </div>
                    <div class="period-date">
                        <span>Fin</span>
                        <strong>{{ $dateFormattedEnd }}</strong>
                    </div>
                    <div class="period-date">
                        <span>Jours restants</span>
                        <strong>{{ $daysRemaining }} jours</strong>
                    </div>
                </div>

                <div class="package-info">
                    <div class="package-name">{{ $periode->translate(app()->getLocale(), 'fallbackLocale')->libelle }}</div>
                    <div class="package-price">Prix actuel</div>


                    <div class="package-features">
                        @foreach ($periode->tarifs as $tarif)
                            <i class="bi bi-check-circle me-1"></i>
                            {{ $tarif->categorie_registrant->libelle }} | {{ $tarif->montant }}
                            {{ $tarif->congres->currency }} <br>
                        @endforeach
                    </div>



                </div>

                {{-- <div class="after-deadline bg-warning">
                    <h5><i class="bi bi-exclamation-triangle me-2"></i>Après la date limite</h5>
                    <p>Le prix passera à <strong>350 €</strong> (Package Standard) ou vous pourrez opter pour le
                        <strong>Package Basique à 200 €</strong> (accès limité aux conférences)
                    </p>
                </div> --}}
            </div>

            <div class="row dashboard">
                <!-- Inscriptions -->
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/view-single-registrations" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-person-plus-fill"></i>
                            <h4>Inscription Individuelle</h4>
                            <p>Inscription unique</p>
                        </div>
                    </a>
                </div>

                <!-- Groupe -->
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/view-group-registrations" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-people-fill"></i>
                            <h4>Inscription de Groupe</h4>
                            <p>Inscrire plusieurs participants</p>
                        </div>
                    </a>
                </div>

                <!-- Profil -->
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/view-accompagning-registration" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-person-workspace"></i>
                            <h4>Inscription de personne tierce</h4>
                            <p>Ajouter une/des personnes accompagnantes</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row dashboard">
                <!-- Paiement -->
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/invoices" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-file-medical-fill"></i>
                            <h4>Facturation</h4>
                            <p>Effectuer et suivre le paiement</p>
                        </div>
                    </a>
                </div>

                <!-- Sponsoring -->
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/profile" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-person-badge-fill"></i>
                            <h4>Mon Profil</h4>
                            <p>Voir et mettre à jour le profil</p>
                        </div>
                    </a>
                </div>

                <!-- Badge / QR -->
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="#" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-qr-code"></i>
                            <h4>Mon Badge</h4>
                            <p>Télécharger le badge QR</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')

@stop
