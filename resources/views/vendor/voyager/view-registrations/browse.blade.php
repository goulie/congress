@extends('voyager::master')

@section('page_title', 'Gestion des Participants')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i> Gestion des Participants
        </h1>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css"/>
<style>
    .info-box {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        background: #fff;
        margin-bottom: 20px;
        display: flex;
        min-height: 90px;
    }

    .info-box-icon {
        border-radius: 5px 0 0 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        font-size: 30px;
        color: white;
    }

    .bg-blue { background-color: #007bff; }
    .bg-green { background-color: #28a745; }
    .bg-yellow { background-color: #ffc107; }
    .bg-red { background-color: #dc3545; }
    .bg-primary { background-color: #6f42c1; }

    .info-box-content {
        padding: 15px;
        flex: 1;
    }

    .info-box-text {
        font-size: 14px;
        text-transform: uppercase;
        font-weight: bold;
        display: block;
        line-height: 1.2;
    }

    .info-box-number {
        font-size: 24px;
        font-weight: bold;
        display: block;
        margin-top: 5px;
    }

    .participant-info {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .label {
        font-size: 85%;
        padding: 4px 8px;
    }

    .flag-icon {
        margin-right: 5px;
        border-radius: 2px;
    }

    .panel-bordered {
        border: 1px solid #e1e4e8;
        border-radius: 5px;
    }

    .btn-sm {
        padding: 4px 8px;
        margin: 0 1px;
    }

    /* DataTable customization */
    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 15px;
    }

    /* Modal improvements */
    .modal-xl {
        max-width: 1200px;
    }

    .detail-section {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #e1e4e8;
        border-radius: 5px;
        background: #f8f9fa;
    }

    .detail-section h5 {
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
        margin-bottom: 15px;
        color: #007bff;
    }

    .detail-row {
        display: flex;
        margin-bottom: 8px;
        padding: 5px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-label {
        font-weight: bold;
        min-width: 200px;
        color: #495057;
    }

    .detail-value {
        flex: 1;
        color: #6c757d;
    }

    .voyager-refresh-animate {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    /*Detail card styles*/ 
        .detail-card {
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .detail-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .detail-card-header {
            background: #025ab9;
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .detail-card-header i {
            font-size: 18px;
        }

        .detail-card-body {
            padding: 20px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .detail-row:hover {
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .detail-label {
            font-weight: 600;
            min-width: 200px;
            color: #495057;
            font-size: 14px;
        }

        .detail-value {
            flex: 1;
            color: #6c757d;
            font-size: 14px;
            word-break: break-word;
        }

        /* Style pour les labels de statut */
        .label {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
        }

        .label-success {
            background-color: #28a745;
        }

        .label-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .label-danger {
            background-color: #dc3545;
        }

        /* Style pour les boutons de téléchargement */
        .btn-xs {
            padding: 2px 8px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }

        /* Style pour les tables dans les cartes */
        .detail-card .table {
            margin-bottom: 0;
            font-size: 13px;
        }

        .detail-card .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }

            .detail-label {
                min-width: auto;
                font-weight: 700;
            }

            .detail-card-body {
                padding: 15px;
            }

            .detail-card-header {
                padding: 12px 15px;
            }
        }

        /* Animation de chargement */
        .voyager-refresh-animate {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Style pour les drapeaux */
        .flag-icon {
            margin-right: 5px;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
    </style>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <!-- Filtres et recherche -->
        <div class="panel panel-bordered">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">{{ app()->getLocale() == 'fr' ? 'Recherche' : 'Search' }}</label>
                            <input type="text" class="form-control" id="search" placeholder="Nom, prénom, email..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="country">{{ app()->getLocale() == 'fr' ? 'Nationalité' : 'Nationality' }}</label>
                            <select class="form-control" id="country">
                                <option value="">Tous</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ request('country') == $country->id ? 'selected' : '' }}>
                                        {{ app()->getLocale() == 'fr' ? $country->libelle_fr : $country->libelle_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label
                                for="status">{{ app()->getLocale() == 'fr' ? 'Statut de paiement' : 'Payment Status' }}</label>
                            <select class="form-control" id="status">
                                <option value="">Tous</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'fr' ? 'Payé' : 'Paid' }}
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'fr' ? 'En attente' : 'Pending' }}
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    {{ app()->getLocale() == 'fr' ? 'Annulé' : 'Cancelled' }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-primary" id="apply-filters">
                                    <i class="voyager-search"></i> {{ app()->getLocale() == 'fr' ? 'Appliquer' : 'Apply' }}
                                </button>
                                <button type="button" class="btn btn-default" id="reset-filters">
                                    <i class="voyager-refresh"></i>
                                    {{ app()->getLocale() == 'fr' ? 'Réinitialiser' : 'Reset' }}
                                </button>
                                <a href="#" class="btn btn-success" id="export-participants">
                                    <i class="voyager-download"></i>
                                    {{ app()->getLocale() == 'fr' ? 'Exporter' : 'Export' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-blue">
                        <i class="voyager-people"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total <br />Participants</span>
                        <span class="info-box-number">{{ $stats['totalParticipants'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green">
                        <i class="voyager-check"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Factures <br /> payées</span>
                        <span class="info-box-number">{{ $invoices->where('status', 'paid')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-red">
                        <i class="voyager-x"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Factures <br /> impayées</span>
                        <span class="info-box-number">{{ $invoices->where('status', '!=', 'paid')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary">
                        <i class="voyager-world"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pays <br />représentés</span>
                        <span class="info-box-number">{{ $stats['countriesCount'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions groupées -->
        <div class="panel panel-bordered">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        
                    </div>
                    <div class="col-md-6 text-right">
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des participants -->
        <div class="panel panel-bordered">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="participants-table" class="table table-hover">
                        <thead>
                            <tr>
                               
                                <th width="5%">Genre</th>
                                <th width="12%">Nom & Prénoms</th>
                                <th width="12%">Email</th>
                                <th width="12%">Organisation</th>
                                <th width="8%">Catégorie</th>
                                <th width="6%">Diner</th>
                                <th width="6%">Visite</th>
                                <th width="8%">Nationnalité</th>
                                <th width="8%">Montant</th>
                                <th width="6%">Statut</th>
                                
                                <th width="8%" class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $invoice)
                                <tr>
                                    
                                    <td>
                                        {{ $invoice->participant->gender->libelle ?? '-' }}
                                    </td>
                                    <td>
                                        <div class="participant-info">
                                            @if ($invoice->participant->civility)
                                                <small class="text-muted">
                                                    ({{ $invoice->participant->civility->libelle ?? '' }})
                                                    &nbsp;
                                                </small>
                                            @endif
                                            <strong>{{ $invoice->participant->lname }}
                                                {{ $invoice->participant->fname }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <a
                                            href="mailto:{{ $invoice->participant->email }}">{{ $invoice->participant->email }}</a>
                                        @if ($invoice->participant->phone)
                                            <br><small class="text-muted">{{ $invoice->participant->phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->participant->organisation)
                                            {{ $invoice->participant->organisation }}
                                            @if ($invoice->participant->organisation_type)
                                                <br><small
                                                    class="text-muted">{{ $invoice->participant->organisation_type->libelle }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $invoice->participant->participantCategory->libelle ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $invoice->participant->diner ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $invoice->participant->visite ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($invoice->participant->country)
                                            <span
                                                class="flag-icon flag-icon-{{ substr(strtolower($invoice->participant->country->abreviation), 0, -1) }}"></span>
                                            {{ app()->getLocale() == 'fr' ? $invoice->participant->country->libelle_fr : $invoice->participant->country->libelle_en }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="label label-info">
                                            <strong>
                                                {{ $invoice->total_amount > 0 ? number_format($invoice->total_amount, 0, ',', ' ') : '-' }}
                                            </strong>
                                            {{ $invoice->congres->currency ?? 'XAF' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($invoice->status == 'paid')
                                            <span class="label label-success">Payé</span>
                                        @elseif($invoice->status == 'pending')
                                            <span class="label label-warning">En attente</span>
                                        @else
                                            <span class="label label-danger">Annulé</span>
                                        @endif
                                    </td>
                                    
                                    <td class="no-sort no-click bread-actions">
                                        <div class="btn-group btn-group-sm">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-info btn-details"
                                                data-id="{{ $invoice->participant->id }}"
                                                title="Voir les détails du participant">
                                                <i class="voyager-eye"></i>
                                            </a>
                                            @can('edit', $invoice->participant)
                                                <a href="{{ route('voyager.participants.edit', $invoice->participant->id) }}"
                                                    title="Edit" class="btn btn-sm btn-primary edit">
                                                    <i class="voyager-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $invoice->participant)
                                                <a href="javascript:;" title="Delete" class="btn btn-sm btn-danger delete"
                                                    data-id="{{ $invoice->participant->getKey() }}"
                                                    id="delete-{{ $invoice->participant->getKey() }}">
                                                    <i class="voyager-trash"></i>
                                                </a>
                                            @endcan
                                            <a href="#" title="Badge" class="btn btn-sm btn-success"
                                                target="_blank">
                                                <i class="voyager-ticket"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">
                                        <div style="padding: 50px;">
                                            <i class="voyager-people" style="font-size: 48px; color: #ccc;"></i>
                                            <h4 style="color: #999;">Aucun participant trouvé</h4>
                                            <p>Commencez par ajouter des participants à votre congrès.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>

    <!-- Modal pour les détails du participant -->
    <div class="modal fade" id="participantModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Détails du participant</h4>
                </div>
                <div class="modal-body" id="participantModalBody">
                    <!-- Le contenu sera chargé via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js">
    </script>
    <script>
        $(document).ready(function() {
            // Configuration
            const config = {
                routes: {
                    details: "{{ url('/admin/participants/details') }}/"
                },
                messages: {
                    loading: `<div class="text-center">
                            <i class="voyager-refresh voyager-refresh-animate" style="font-size: 48px;"></i>
                            <p class="text-muted mt-3">{{ __('Loading participant details...') }}</p>
                         </div>`,
                    error: `<div class="text-center text-danger">
                            <i class="voyager-warning" style="font-size: 48px;"></i>
                            <p class="mt-3">{{ __('Error loading participant details.') }}</p>
                         </div>`
                }
            };

            // Initialize DataTable
            const dataTable = $('#dataTable').DataTable({
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    @if (app()->getLocale() == 'fr')
                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
                    @else
                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json"
                    @endif
                },
                responsive: true,
                pageLength: 25,
                order: [
                    [11, 'desc']
                ],
                columnDefs: [{
                        orderable: false,
                        targets: [0, 13]
                    },
                    {
                        searchable: false,
                        targets: [0, 13]
                    }
                ],
                buttons: [{
                        extend: 'excel',
                        text: '<i class="voyager-download"></i> {{ __('Export Excel') }}',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="voyager-download"></i> {{ __('Export PDF') }}',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'print',
                        text: '<i class="voyager-print"></i> {{ __('Print') }}',
                        className: 'btn btn-info'
                    }
                ]
            });

            // Add DataTable buttons to the page
            $('.dataTables_length').before(
                '<div class="col-sm-12 col-md-6"><div class="btn-group mb-3" id="datatable-buttons"></div></div>'
            );
            dataTable.buttons().container().appendTo('#datatable-buttons');

            // Function to escape HTML (XSS security)
            function escapeHtml(unsafe) {
                if (unsafe === null || unsafe === undefined) return '-';
                return unsafe.toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Function to format date
            function formatDate(dateString) {
                if (!dateString) return '-';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('{{ app()->getLocale() }}', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    return dateString;
                }
            }

            function renderParticipantDetails(participant, invoice, items) {
                const isFr = '{{ app()->getLocale() }}' === 'fr';

                // Fonction utilitaire pour afficher un champ seulement s'il a une valeur
                function renderField(labelFr, labelEn, value, isDate = false) {
                    if (!value || value === '-' || value === '') return '';

                    const label = isFr ? labelFr : labelEn;
                    const displayValue = isDate ? formatDate(value) : escapeHtml(value);

                    return `
            <div class="detail-row">
                <span class="detail-label">${label}:</span>
                <span class="detail-value">${displayValue}</span>
            </div>
        `;
                }

                // Fonction pour afficher les relations avec libellés
                function renderRelationField(labelFr, labelEn, relation, fallback = '-') {
                    if (!relation || !relation.libelle) return '';

                    const label = isFr ? labelFr : labelEn;
                    const value = escapeHtml(relation.libelle);

                    return `
            <div class="detail-row">
                <span class="detail-label">${label}:</span>
                <span class="detail-value">${value}</span>
            </div>
        `;
                }

                // Fonction pour afficher les pays
                function renderCountryField(labelFr, labelEn, country) {
                    if (!country) return '';

                    const label = isFr ? labelFr : labelEn;
                    const countryName = isFr ? country.libelle_fr : country.libelle_en;
                    const flag = country.code ?
                        `<span class="flag-icon flag-icon-${country.code.toLowerCase()}"></span>` : '';

                    return `
            <div class="detail-row">
                <span class="detail-label">${label}:</span>
                <span class="detail-value">${flag} ${escapeHtml(countryName)}</span>
            </div>
        `;
                }

                return `
        <div class="row">
            <!-- Personal Information Card -->
            <div class="col-md-6">
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="voyager-person"></i>
                        <h5>${isFr ? 'Informations Personnelles' : 'Personal Information'}</h5>
                    </div>
                    <div class="detail-card-body">
                        ${renderRelationField('Civilité', 'Civility', participant.civility_id)}
                        ${renderField('Nom', 'Last Name', participant.lname)}
                        ${renderField('Prénom', 'First Name', participant.fname)}
                        ${renderRelationField('Genre', 'Gender', participant.gender)}
                        ${renderField('Email', 'Email', participant.email)}
                        ${renderField('Téléphone', 'Phone', participant.phone)}
                        ${renderCountryField('Pays de nationalité', 'Nationality Country', participant.country)}
                        ${renderRelationField('Niveau étudiant', 'Student Level', participant.student_level)}
                        ${renderField('Autre niveau étudiant', 'Other Student Level', participant.student_level_other)}
                    </div>
                </div>

                <!-- Professional Information Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="voyager-briefcase"></i>
                        <h5>${isFr ? 'Informations Professionnelles' : 'Professional Information'}</h5>
                    </div>
                    <div class="detail-card-body">
                        ${renderField('Organisation', 'Organization', participant.organisation)}
                        ${renderRelationField('Type d\'organisation', 'Organization Type', participant.organisation_type)}
                        ${renderField('Autre type d\'organisation', 'Other Organization Type', participant.organisation_type_other)}
                        ${renderField('Poste', 'Job', participant.job)}
                        ${renderCountryField('Pays de travail', 'Job Country', participant.job_country)}
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Registration Information Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="voyager-ticket"></i>
                        <h5>${isFr ? 'Informations d\'Inscription' : 'Registration Information'}</h5>
                    </div>
                    <div class="detail-card-body">
                        ${renderRelationField('Catégorie de participant', 'Participant Category', participant.participant_category)}
                        ${renderRelationField('Type de membre', 'Member Type', participant.type_member)}
                        ${renderField('Code de membre', 'Membership Code', participant.membership_code)}
                        ${renderField('Dîner de gala', 'Gala Dinner', participant.diner)}
                        ${renderField('Visite', 'Visit', participant.visite)}
                        ${renderRelationField('Site de visite', 'Visit Site', participant.site_visite)}
                    </div>
                </div>

                <!-- Documents and Additional Info Card -->
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="voyager-paperclip"></i>
                        <h5>${isFr ? 'Documents et Informations Supplémentaires' : 'Documents and Additional Information'}</h5>
                    </div>
                    <div class="detail-card-body">
                        ${renderField('Numéro de passeport', 'Passport Number', participant.passeport_number)}
                        ${renderField('Date d\'expiration du passeport', 'Passport Expiration Date', participant.expiration_passeport_date, true)}
                        ${renderField('Membre AAE', 'AAE Member', participant.membre_aae)}
                        ${renderField('YWP ou Étudiant', 'YWP or Student', participant.ywp_or_student)}
                        ${renderField('Passe de délégué', 'Delegate Pass', participant.pass_deleguate)}
                        ${renderRelationField(`Tranche d'âge`, `Age Range`, participant.age_range?.libelle)}

                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Information Card -->
        <div class="row">
            <div class="col-12">
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="voyager-credit-card"></i>
                        <h5>${isFr ? 'Informations de Facturation' : 'Billing Information'}</h5>
                    </div>
                    <div class="detail-card-body">
                        ${invoice ? `
                                <div class="row">
                                    <div class="col-md-12">
                                        ${items && items.length > 0 ? `
                                        <h6>${isFr ? 'Articles de facture' : 'Invoice Items'}:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>${isFr ? 'Description' : 'Description'}</th>
                                                        <th class="text-right">${isFr ? 'Prix' : 'Price'}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${items.map(item => `
                                                            <tr>
                                                                <td>${escapeHtml(item.description_fr)}</td>
                                                                <td class="text-right">${escapeHtml(item.price)} ${escapeHtml(item.currency)}</td>
                                                            </tr>
                                                        `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    ` : `<p class="text-muted">${isFr ? 'Aucun article de facture' : 'No invoice items'}</p>`}
                                    
                                    </div>
                                    <div class="row">
                                    <div class="col-md-12">
                                        ${renderField(isFr ? 'Numéro de facture' : 'Invoice Number', 'Invoice Number', invoice.invoice_number)}
                                        ${renderField(isFr ? 'Montant total' : 'Total Amount', 'Total Amount', invoice.total_amount ? `${invoice.total_amount} ${invoice.currency}` : null)}
                                        ${invoice.status ? `
                                        <div class="detail-row">
                                            <span class="detail-label">${isFr ? 'Statut' : 'Status'}:</span>
                                            <span class="detail-value">
                                                ${invoice.status === 'paid' ? 
                                                    '<span class="label label-success">' + (isFr ? 'Payé' : 'Paid') + '</span>' : 
                                                 invoice.status === 'pending' ? 
                                                    '<span class="label label-warning">' + (isFr ? 'En attente' : 'Pending') + '</span>' : 
                                                    '<span class="label label-danger">' + (isFr ? 'Annulé' : 'Cancelled') + '</span>'}
                                            </span>
                                        </div>
                                    ` : ''}
                                    
                                    
                                </div>
                                

                                </div>
                            ` : `<p class="text-muted">${isFr ? 'Aucune facture disponible' : 'No invoice available'}</p>`}
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="detail-card">
                    <div class="detail-card-header">
                        <i class="voyager-info-circled"></i>
                        <h5>${isFr ? 'Informations Système' : 'System Information'}</h5>
                    </div>
                    <div class="detail-card-body">
                        
                        ${renderField('Congrès', 'Congress', participant.congres?.title)}
                        ${renderField('Date de création', 'Created At', participant.created_at, true)}
                        ${renderField('Date de modification', 'Updated At', participant.updated_at, true)}
                        ${renderField('Langue', 'Language', participant.langue)}
                        ${renderField('Congrès', 'Congress', participant.congres?.title)}
                        ${renderField('Inscrit par', 'Registered By', participant.user?.name ?? 'N/A' )}
                    </div>
                </div>
            </div>
        </div>
    `;
            }

            // Event handler for participant details
            $(document).on('click', '.btn-details', function() {
                const id = $(this).data('id');
                const modal = $('#participantModal');

                if (!id) {
                    console.error('Participant ID not found');
                    return;
                }

                // Reset and show modal
                $('#participantModalBody').html(config.messages.loading);
                modal.modal('show');

                // AJAX request
                $.ajax({
                    url: config.routes.details + id,
                    method: "GET",
                    timeout: 15000,
                    success: function(response) {
                        if (!response || !response.participant) {
                            $('#participantModalBody').html(config.messages.error);
                            return;
                        }

                        const html = renderParticipantDetails(
                            response.participant,
                            response.invoice,
                            response.items
                        );
                        $('#participantModalBody').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        let errorMessage = config.messages.error;

                        if (status === 'timeout') {
                            errorMessage = `<div class="text-center text-danger">
                                            <i class="voyager-warning" style="font-size: 48px;"></i>
                                            <p class="mt-3">{{ __('Timeout loading participant details.') }}</p>
                                        </div>`;
                        } else if (xhr.status === 404) {
                            errorMessage = `<div class="text-center text-danger">
                                            <i class="voyager-warning" style="font-size: 48px;"></i>
                                            <p class="mt-3">{{ __('Participant not found.') }}</p>
                                        </div>`;
                        }

                        $('#participantModalBody').html(errorMessage);
                    }
                });
            });

            // Cleanup when modal is closed
            $('#participantModal').on('hidden.bs.modal', function() {
                $('#participantModalBody').html('');
            });

            // Bulk actions handling
            $('.bulk-action').on('click', function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                const selected = $('input[name="row_id"]:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selected.length === 0) {
                    alert('{{ __('Please select at least one participant.') }}');
                    return;
                }

                handleBulkAction(action, selected);
            });

            function handleBulkAction(action, selectedIds) {
                switch (action) {
                    case 'export-selected':
                        exportParticipants(selectedIds);
                        break;
                    case 'generate-badges':
                        generateBadges(selectedIds);
                        break;
                    case 'delete-selected':
                        deleteParticipants(selectedIds);
                        break;
                }
            }

            function generateBadges(ids) {
                $('#badgeModal').modal('show');
                $('#confirmGenerateBadges').off('click').on('click', function() {
                    const count = $('#badgeCount').val();
                    // Implement badge generation here
                    console.log('Generating badges:', ids, 'Count:', count);
                    $('#badgeModal').modal('hide');
                });
            }

            // Checkbox handling
            $('.select_all').on('click', function() {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
            });
        });
    </script>
@stop
