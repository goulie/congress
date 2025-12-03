@extends('voyager::master')

@section('page_title', 'Tableau de bord - Inscrits')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-group"></i> Tableau de bord - Inscrits
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <div class="row">
            <!-- Cartes de statistiques -->
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">

                        <h3 class="panel-title paneltitle"><i class="bi bi-bar-chart-fill"></i> Statistiques générales</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <!-- Carte 1: Total participants -->
                            <div class="col-md-2 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary">
                                        <i class="voyager-people"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Inscrits</span>
                                        <span class="info-box-number">{{ $statGeneral['TotalInscrits'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte 2: Étudiants -->
                            <div class="col-md-2 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="bi bi-person-video"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Délégués</span>
                                        <span class="info-box-number">{{ $statGeneral['TotalDelegues'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-purple">
                                        <i class="voyager-study"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Etudiants</span>
                                        <span class="info-box-number">{{ $statGeneral['TotalEtudiant'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte 3: YWP -->
                            <div class="col-md-2 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="bi bi-person-heart"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total YWP</span>
                                        <span class="info-box-number">{{ $statGeneral['TotalYwp'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger">
                                        <i class="bi bi-globe-americas"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Nationalités</span>
                                        <span class="info-box-number">{{ $statGeneral['TotalNationalites'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary">
                                        <i class="bi bi-building"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Organisations</span>
                                        <span class="info-box-number">{{ $statGeneral['TotalOrganisations'] }}</span>
                                        {{-- <small id="ywp-percentage" class="text-success">0%</small> --}}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Cartes de statistiques etudiants -->
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title paneltitle">
                            <i class="voyager-study"></i>
                            Statistiques Etudiants
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <!-- Carte 1: Total participants -->
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Etudiants Acceptés</span>
                                        <span class="info-box-number">{{ $statEtudiants['AcceptedStudent'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte 2: refusés -->
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Étudiants Refusés</span>
                                        <span class="info-box-number">{{ $statEtudiants['RejectedStudent'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte 3: Pending -->
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="bi bi-clock-fill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Etudiants en attente</span>
                                        <span class="info-box-number">{{ $statEtudiants['PendingStudent'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cartes de statistiques YWP -->
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title paneltitle">
                            <i class="bi bi-person-heart"></i>
                            Statistiques YWP (Young Water Professionals)
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <!-- Carte 1: Total participants -->
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">YWP Acceptés</span>
                                        <span class="info-box-number">{{ $statYwp['AcceptedYwp'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte 2: refusés -->
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">YWP Refusés</span>
                                        <span class="info-box-number">{{ $statYwp['RejectedYwp'] }}</span>

                                    </div>
                                </div>
                            </div>

                            <!-- Carte 3: Pending -->
                            <div class="col-md-4 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="bi bi-clock-fill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">YWP en attente</span>
                                        <span class="info-box-number">{{ $statYwp['PendingYwp'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title paneltitle">
                            <i class="bi bi-person-heart"></i>
                            Repartions par Genre
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">


                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title paneltitle">
                            <i class="bi bi-person-heart"></i>
                            Repartions par type Membre
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">


                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title paneltitle">
                            <i class="bi bi-person"></i>
                            Liste complète des participants
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead class="table-light">
                                    <tr>



                                        <!-- Civilité -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-person-badge me-1"></i>Civilité
                                        </th>

                                        <!-- Nom & Prénom -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-person me-1"></i>Nom & Prénom
                                        </th>

                                        <!-- Genre -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-gender-ambiguous me-1"></i>Genre
                                        </th>

                                        <!-- Nationalité -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-flag me-1"></i>Nationalité
                                        </th>

                                        <!-- Email -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-envelope me-1"></i>Email
                                        </th>

                                        <!-- Organisation -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-building me-1"></i>Organisation
                                        </th>

                                        <!-- Type d'organisation -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-diagram-2 me-1"></i>Type Org.
                                        </th>

                                        <!-- Catégorie -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-tags me-1"></i>Catégorie
                                        </th>

                                        <!-- Dîner -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-egg-fried me-1"></i>Dîner
                                        </th>

                                        <!-- Pass délégué -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-ticket-perforated me-1"></i>Pass Dél.
                                        </th>

                                        <!-- Pays Travail -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-geo-alt me-1"></i>Pays Travail
                                        </th>

                                        <!-- YWP / Étudiant -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-mortarboard me-1"></i>YWP/Étud.
                                        </th>

                                        <!-- Actions -->
                                        <th class="text-start text-muted text-uppercase small fw-bold">
                                            <i class="bi bi-gear me-1"></i>Actions
                                        </th>

                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($query as $p)
                                        <tr>

                                            <!-- Civilité -->
                                            <td>{{ $p->civility?->translate(app()->getLocale())->libelle ?? '-' }}</td>

                                            <!-- Nom & Prénom -->
                                            <td class="fw-semibold">{{ $p->fname }} {{ $p->lname }}</td>

                                            <!-- Genre -->
                                            <td>{{ $p->gender?->translate(app()->getLocale())->libelle ?? '-' }}</td>

                                            <!-- Nationalité -->
                                            <td>
                                                {{ app()->getLocale() === 'fr' ? $p->country->libelle_fr ?? '-' : $p->country->libelle_en ?? '-' }}
                                            </td>

                                            <!-- Email -->
                                            <td>{{ $p->email }}</td>

                                            <!-- Organisation -->
                                            <td>{{ $p->organisation ?? '-' }}</td>

                                            <!-- Type Organisation -->
                                            <td>
                                                {{ $p->organisationType?->translate(app()->getLocale())->libelle ?? '-' }}
                                            </td>

                                            <!-- Catégorie -->
                                            <td>
                                                {{ $p->participantCategory?->translate(app()->getLocale())->libelle ?? '-' }}
                                            </td>

                                            <!-- Dîner -->
                                            <td>
                                                @if ($p->diner)
                                                    <span class="badge bg-success">Oui</span>
                                                @else
                                                    <span class="badge bg-danger">Non</span>
                                                @endif
                                            </td>

                                            <!-- Pass Délégué -->
                                            <td>
                                                @if ($p->pass_deleguate)
                                                    <span class="badge bg-success">Oui</span>
                                                @else
                                                    <span class="badge bg-secondary">Non</span>
                                                @endif
                                            </td>

                                            <!-- Pays Travail -->
                                            <td>
                                                {{ app()->getLocale() === 'fr' ? $p->jobCountry->libelle_fr ?? '-' : $p->jobCountry->libelle_en ?? '-' }}
                                            </td>

                                            <!-- YWP / Étudiant -->
                                            <td>
                                                @if ($p->ywp_or_student === 'ywp')
                                                    <span class="badge bg-primary">YWP</span>
                                                @elseif($p->ywp_or_student === 'student')
                                                    <span class="badge bg-purple">Étudiant</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td>
                                                <a class="btn btn-sm btn-info"
                                                    href="{{ route('voyager.participants.show', $p->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a class="btn btn-sm btn-info"
                                                    href="{{ route('voyager.participants.show', $p->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="20" class="text-center py-4 text-muted">Aucun participant</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>



    @stop

    @section('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.5/css/buttons.dataTables.css">
        <style>
            .info-box:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .info-box {
                box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
                border-radius: .25rem;
                background-color: #fff;
                display: flex;
                margin-bottom: 1rem;
                min-height: 80px;
                padding: .5rem;
                position: relative;
                transition: transform 0.2s;
            }

            .info-box-icon {
                border-radius: .25rem;
                align-items: center;
                display: flex;
                font-size: 1.875rem;
                justify-content: center;
                text-align: center;
                width: 70px;
                color: #fff;
            }

            .info-box-content {
                flex: 1;
                padding: 5px 10px;
            }

            .info-box-text {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                text-transform: uppercase;
                font-weight: bold;
                font-size: 14px;
            }

            .info-box-number {
                display: block;
                font-weight: bold;
                font-size: 18px;
            }

            .bg-purple {
                background-color: #6f42c1 !important;
            }

            .panel-actions {
                position: absolute;
                right: 15px;
                top: 15px;
            }

            .chart-container {
                position: relative;
                height: 300px;
            }

            .bg-warning {
                background-color: #ffc107 !important;
                color: white;
            }

            .bg-info {
                background-color: #17a2b8 !important;
                color: white;
            }

            .bg-danger {
                background-color: #dc3545 !important;
                color: white;
            }

            .bg-success {
                background-color: #28a745 !important;
                color: white;
            }

            .bg-primary {
                background-color: #5707eb !important;
                color: white;
            }

            .bg-secondary {
                background-color: #6c757d !important;
                color: white;
            }

            .paneltitle {
                font-weight: bold;
                font-size: 20px;
                color: #080ebd;
            }
        </style>
    @stop

    @section('javascript')
        <!-- Chart.js -->
        <script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
        {{-- add scripts --}}
<script>
            $(document).ready(function() {
                let table = new DataTable('#dataTable' {
                    layout: {
                        topStart: {
                            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                        }
                    }
                });
            });
        </script>
        <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.dataTables.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.print.min.js"></script>

        
    @stop
