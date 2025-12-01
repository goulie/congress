@extends('voyager::master')

@section('css')
    <style>
        /* === GENERAL STYLES === */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.5;
        }

        .dashboard {
            margin-top: 30px;
        }

        /* === PERIOD CARD === */
        .period-card {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #3f51b5;
        }

        .period-card h3 {
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
            font-size: 1.3rem;
        }

        .period-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .period-info-item {
            flex: 1;
            min-width: 150px;
            margin-bottom: 10px;
        }

        .period-info-item span {
            display: block;
            font-size: 0.85em;
            color: #666;
            margin-bottom: 5px;
        }

        .period-info-item strong {
            font-size: 1em;
            font-weight: 600;
            color: #2c3e50;
        }

        .period-prices {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
        }

        .price-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eaeaea;
        }

        .price-item:last-child {
            border-bottom: none;
        }

        /* === DASHBOARD BUTTONS === */
        .dash-btn {
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 25px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dash-btn:hover {
            background: #f8f9fa;
            transform: translateY(-3px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.08);
            border-color: #d0d0d0;
        }

        .dash-btn i {
            font-size: 1.8em;
            margin-bottom: 10px;
            color: #3f51b5;
        }

        .dash-btn h4 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .dash-btn p {
            font-size: 0.85em;
            color: #666;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    @php
        $periode = App\Models\Periode::PeriodeActive(App\Models\Congress::latest()->first()->id);
        $locale = app()->getLocale(); // 'fr' or 'en'
        \Carbon\Carbon::setLocale($locale);
        $start = \Carbon\Carbon::parse($periode->start_date);
        $end = \Carbon\Carbon::parse($periode->end_date);
        $daysRemaining = $periode->joursRestants();

        $dateFormattedStart = $locale === 'fr' ? $start->translatedFormat('d F Y') : $start->translatedFormat('F d, Y');
        $dateFormattedEnd = $locale === 'fr' ? $end->translatedFormat('d F Y') : $end->translatedFormat('F d, Y');
    @endphp

    <div class="page-content">
        <div class="container-fluid">
            <!-- Carte / Period Card -->
            <div class="period-card">
                <h3>
                    @if ($locale === 'fr')
                        Période d'Inscription Actuelle
                    @else
                        Current Registration Period
                    @endif
                </h3>

                <div class="period-info">
                    <div class="period-info-item">
                        <span>
                            @if ($locale === 'fr')
                                PACKAGE APPLIQUE
                            @else
                                APPLIED PACKAGE
                            @endif
                        </span>
                        <strong>{{ $periode->translate($locale, 'fallbackLocale')->libelle }}</strong>
                    </div>
                    <div class="period-info-item">
                        <span>
                            @if ($locale === 'fr')
                                Début
                            @else
                                Start
                            @endif
                        </span>
                        <strong>{{ $dateFormattedStart }}</strong>
                    </div>
                    <div class="period-info-item">
                        <span>
                            @if ($locale === 'fr')
                                Fin
                            @else
                                End
                            @endif
                        </span>
                        <strong>{{ $dateFormattedEnd }}</strong>
                    </div>
                    <div class="period-info-item">
                        <span>
                            @if ($locale === 'fr')
                                Jours restants
                            @else
                                Days remaining
                            @endif
                        </span>
                        <strong>{{ $daysRemaining }} {{ $locale === 'fr' ? 'jours' : 'days' }}</strong>
                    </div>
                </div>

                <div class="period-prices">
                    <h2 class="text-primary">
                        @if ($locale === 'fr')
                            Tarifs actuels appliqués
                        @else
                            Current Applied Rates
                        @endif
                    </h2>
                    @foreach ($periode->tarifs as $tarif)
                        <div class="price-item" style="font-weight: bold;">
                            <span class="price-category">
                                {{ $tarif->categorie_registrant->translate($locale, 'fallbackLocale')->libelle }}
                            </span>
                            <span class="price-amount">
                                {{ $tarif->montant }} {{ $tarif->congres->currency }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mt-4 card" style="font-weight: bold;font-size: 1.5em">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Tarif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John</td>
                                <td>200 Eur</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>


            @if (auth()->user()->isParticipant() || auth()->user()->isAdmin())


                <div class="row dashboard">
                    <div class="col-md-6 col-sm-6 mb-4">
                        <a href="{{ route('voyager.view-single-registrations.index') }}" class="text-decoration-none">
                            <div class="dash-btn">
                                <i class="bi bi-person-plus-fill"></i>
                                <h4>
                                    @if ($locale === 'fr')
                                        Inscription Individuelle
                                    @else
                                        Individual Registration
                                    @endif
                                </h4>
                                <p>
                                    @if ($locale === 'fr')
                                        Inscription unique
                                    @else
                                        Register one participant
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-sm-6 mb-4">
                        <a href="{{ route('voyager.view-group-registrations.index') }}" class="text-decoration-none">
                            <div class="dash-btn">
                                <i class="bi bi-people-fill"></i>
                                <h4>
                                    @if ($locale === 'fr')
                                        Inscription de Groupe
                                    @else
                                        Group Registration
                                    @endif
                                </h4>
                                <p>
                                    @if ($locale === 'fr')
                                        Inscrire plusieurs participants
                                    @else
                                        Register multiple participants
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>


                </div>



                <div class="row dashboard">
                    <div class="col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('voyager.invoices.index') }}" class="text-decoration-none">
                            <div class="dash-btn">
                                <i class="bi bi-file-medical-fill"></i>
                                <h4>
                                    @if ($locale === 'fr')
                                        Facturation
                                    @else
                                        Billing
                                    @endif
                                </h4>
                                <p>
                                    @if ($locale === 'fr')
                                        Effectuer et suivre le paiement
                                    @else
                                        Make and track payments
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('voyager.profile') }}" class="text-decoration-none">
                            <div class="dash-btn">
                                <i class="bi bi-person-badge-fill"></i>
                                <h4>
                                    @if ($locale === 'fr')
                                        Mon Profil
                                    @else
                                        My Profile
                                    @endif
                                </h4>
                                <p>
                                    @if ($locale === 'fr')
                                        Voir et mettre à jour le profil
                                    @else
                                        View and update profile
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('voyager.view-badges.index') }}" class="text-decoration-none">
                            <div class="dash-btn">
                                <i class="bi bi-qr-code"></i>
                                <h4>
                                    @if ($locale === 'fr')
                                        Mon Badge
                                    @else
                                        My Badge
                                    @endif
                                </h4>
                                <p>
                                    @if ($locale === 'fr')
                                        Télécharger le badge QR
                                    @else
                                        Download your QR badge
                                    @endif
                                </p>
                            </div>
                        </a>
                    </div>

                </div>
            @elseif(auth()->user()->isValidator() || auth()->user()->isAdmin())
                <div class="row dashboard">

                    <div class="col-md-12 col-sm-12 mb-4">
                        <a href="{{ route('voyager.view-validation-ywp-students.index') }}" class="btn btn-block">
                            <div class="dash-btn" style="background-color: #9cc9f7">
                                <i class="bi bi-people-fill"></i>
                                <h4>
                                    @if ($locale === 'fr')
                                        Traiter les Jeunes professionnels et Etudiants
                                    @else
                                        Process Young Professionals and Students
                                    @endif
                                </h4>

                            </div>
                        </a>
                    </div>

                </div>
            @elseif(auth()->user()->isFinance() || auth()->user()->isAdmin())
                <div class="row dashboard">

                    <div class="col-md-12 col-sm-12 mb-4">
                        <a href="{{ route('voyager.view-validation-payments.index') }}" class="btn btn-block">
                            <div class="dash-btn" style="background-color: #9cc9f7">
                                <i class="bi bi-file-medical-fill"></i>
                                <h4>
                                    @if ($locale === 'fr')
                                        Traiter les factures
                                    @else
                                        Traiter les factures
                                    @endif
                                </h4>

                            </div>
                        </a>
                    </div>

                </div>
            @else
            @endif
        </div>
    </div>
@stop
