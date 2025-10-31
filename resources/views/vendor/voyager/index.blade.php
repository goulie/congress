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

        .period-prices h4 {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #2c3e50;
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

        .price-category {
            color: #555;
        }

        .price-amount {
            font-weight: 500;
            color: #2c3e50;
        }

        /* === DASHBOARD CARDS === */
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

        /* === RESPONSIVE === */
        @media (max-width: 767px) {
            .dash-btn {
                margin-bottom: 15px;
            }
            
            .period-info {
                flex-direction: column;
            }
            
            .period-info-item {
                margin-bottom: 15px;
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

        $daysRemaining = $periode->joursRestants();

        // Format date selon locale
        $dateFormattedStart = $locale === 'fr' ? $start->translatedFormat('d F Y') : $start->translatedFormat('F d, Y');
        $dateFormattedEnd = $locale === 'fr' ? $end->translatedFormat('d F Y') : $end->translatedFormat('F d, Y');
    @endphp
    
    <div class="page-content">
        <div class="container">
            <!-- Carte de période d'inscription -->
            <div class="period-card">
                <h3>Période d'Inscription Actuelle</h3>
                
                <div class="period-info">
                    <div class="period-info-item">
                        <span>Nom de la période</span>
                        <strong>{{ $periode->translate(app()->getLocale(), 'fallbackLocale')->libelle }}</strong>
                    </div>
                    <div class="period-info-item">
                        <span>Début</span>
                        <strong>{{ $dateFormattedStart }}</strong>
                    </div>
                    <div class="period-info-item">
                        <span>Fin</span>
                        <strong>{{ $dateFormattedEnd }}</strong>
                    </div>
                    <div class="period-info-item">
                        <span>Jours restants</span>
                        <strong>{{ $daysRemaining }} jours</strong>
                    </div>
                </div>

                <div class="period-prices">
                    <h2 class="text-primary">Tarifs actuels appliqués</h2>
                    @foreach ($periode->tarifs as $tarif)
                        <div class="price-item" style="font-weight: bold;">
                            <span class="price-category">{{ $tarif->categorie_registrant->libelle }}</span>
                            <span class="price-amount">{{ $tarif->montant }} {{ $tarif->congres->currency }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions principales -->
            <div class="row dashboard">
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/view-single-registrations" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-person-plus-fill"></i>
                            <h4>Inscription Individuelle</h4>
                            <p>Inscription unique</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/view-group-registrations" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-people-fill"></i>
                            <h4>Inscription de Groupe</h4>
                            <p>Inscrire plusieurs participants</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/view-accompagning-registration" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-person-workspace"></i>
                            <h4>Personne accompagnante</h4>
                            <p>Ajouter des personnes tierces</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row dashboard">
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/invoices" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-file-medical-fill"></i>
                            <h4>Facturation</h4>
                            <p>Effectuer et suivre le paiement</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="/admin/profile" class="dash-btn-link text-decoration-none">
                        <div class="dash-btn">
                            <i class="bi bi-person-badge-fill"></i>
                            <h4>Mon Profil</h4>
                            <p>Voir et mettre à jour le profil</p>
                        </div>
                    </a>
                </div>

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