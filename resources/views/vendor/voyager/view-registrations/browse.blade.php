@extends('voyager::master')

@section('page_title', 'Gestion des Participants')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i>
            {{ app()->getLocale() == 'fr' ? 'Gestion des Participants' : 'Participants Management' }}
        </h1>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css" />
    <style>
        /* Statistiques */
        .info-box {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background: #fff;
            margin-bottom: 20px;
            min-height: 90px;
            display: flex;
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

        .bg-blue {
            background-color: #007bff;
        }

        .bg-green {
            background-color: #28a745;
        }

        .bg-red {
            background-color: #dc3545;
        }

        .bg-primary {
            background-color: #6f42c1;
        }

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

        /* Table et éléments */
        .panel-bordered {
            border: 1px solid #e1e4e8;
            border-radius: 5px;
        }

        .participant-info {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .flag-icon {
            margin-right: 5px;
            border-radius: 2px;
        }

        /* Boutons */
        .btn-group-sm>.btn {
            padding: 4px 8px;
            margin: 0 1px;
        }

        /* Responsive DataTable */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 15px;
        }

        /* Modal */
        .modal-xl {
            max-width: 1200px;
        }

        /* Cartes de détails */
        .detail-card {
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .detail-card-body {
            padding: 20px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
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

        /* Labels */
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

        .label-info {
            background-color: #17a2b8;
        }

        /* Animation */
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

        /* Responsive */
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
        }
    </style>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <!-- Filtres -->
        <div class="panel panel-bordered">
            <div class="panel-body">
                <form id="filter-form" method="GET" action="{{ url()->current() }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">{{ app()->getLocale() == 'fr' ? 'Recherche' : 'Search' }}</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="{{ app()->getLocale() == 'fr' ? 'Nom, prénom, email...' : 'Name, first name, email...' }}"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label
                                    for="country">{{ app()->getLocale() == 'fr' ? 'Nationalité' : 'Nationality' }}</label>
                                <select class="form-control" id="country" name="country">
                                    <option value="">{{ app()->getLocale() == 'fr' ? 'Tous' : 'All' }}</option>
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
                                <select class="form-control" id="status" name="status">
                                    <option value="">Toutes</option>
                                    @foreach (\App\Models\Invoice::getPaymentStatuses() as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ request('method') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary" id="apply-filters">
                                        <i class="voyager-search"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Appliquer' : 'Apply' }}
                                    </button>
                                    <a href="{{ url()->current() }}" class="btn btn-default" id="reset-filters">
                                        <i class="voyager-refresh"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Réinitialiser' : 'Reset' }}
                                    </a>
                                    {{-- <button type="button" class="btn btn-success" id="export-excel">
                                        <i class="voyager-download"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Exporter Excel' : 'Export Excel' }}
                                    </button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                        <span
                            class="info-box-text">{{ app()->getLocale() == 'fr' ? 'Total Participants' : 'Total Participants' }}</span>
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
                        <span
                            class="info-box-text">{{ app()->getLocale() == 'fr' ? 'Factures payées' : 'Paid Invoices' }}</span>
                        <span class="info-box-number">{{ $stats['paidParticipants'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-red">
                        <i class="voyager-x"></i>
                    </span>
                    <div class="info-box-content">
                        <span
                            class="info-box-text">{{ app()->getLocale() == 'fr' ? 'Factures impayées' : 'Unpaid Invoices' }}</span>
                        <span class="info-box-number">{{ $stats['TotalUnpaid'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary">
                        <i class="voyager-world"></i>
                    </span>
                    <div class="info-box-content">
                        <span
                            class="info-box-text">{{ app()->getLocale() == 'fr' ? 'Pays représentés' : 'Countries Represented' }}</span>
                        <span class="info-box-number">{{ $stats['TotalNationalites'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des participants -->
        <div class="panel panel-bordered">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="participants-table" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">{{ app()->getLocale() == 'fr' ? 'Genre' : 'Gender' }}</th>
                                <th width="12%">{{ app()->getLocale() == 'fr' ? 'Nom & Prénoms' : 'Name' }}</th>
                                <th width="12%">Email</th>
                                <th width="12%">{{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}</th>
                                <th width="8%">{{ app()->getLocale() == 'fr' ? 'Catégorie' : 'Category' }}</th>
                                <th width="6%">{{ app()->getLocale() == 'fr' ? 'Diner' : 'Dinner' }}</th>
                                <th width="6%">{{ app()->getLocale() == 'fr' ? 'Visite' : 'Visit' }}</th>
                                <th width="8%">{{ app()->getLocale() == 'fr' ? 'Nationalité' : 'Nationality' }}</th>
                                <th width="8%">{{ app()->getLocale() == 'fr' ? 'Montant' : 'Amount' }}</th>
                                <th width="6%">{{ app()->getLocale() == 'fr' ? 'Statut' : 'Status' }}</th>
                                <th width="8%" class="text-right">{{ __('voyager::generic.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $invoice)
                                @php
                                    $participant = $invoice->participant;
                                @endphp
                                <tr>
                                    <td>{{ $participant?->gender?->libelle ?? '-' }}</td>
                                    <td>
                                        <div class="participant-info">
                                            @if ($participant?->civility)
                                                <small class="text-muted">
                                                    ({{ $participant->civility->libelle ?? '' }})
                                                    &nbsp;
                                                </small>
                                            @endif
                                            <strong>{{ $participant->lname ?? '' }}
                                                {{ $participant->fname ?? '' }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($participant?->email)
                                            <a href="mailto:{{ $participant->email }}">{{ $participant->email }}</a>
                                            @if ($participant->phone)
                                                <br><small class="text-muted">{{ $participant->phone }}</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($participant?->organisation)
                                            {{ $participant->organisation }}
                                            @if ($participant->organisation_type)
                                                <br><small
                                                    class="text-muted">{{ $participant->organisation_type->libelle }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $participant?->participantCategory?->libelle ?? '-' }}</td>
                                    <td>{{ $participant?->diner ?? '-' }}</td>
                                    <td>{{ $participant?->visite ?? '-' }}</td>
                                    <td>
                                        @if ($participant?->country)
                                            <span
                                                class="flag-icon flag-icon-{{ strtolower(substr($participant->country->abreviation, 0, 2)) }}"></span>
                                            {{ app()->getLocale() == 'fr' ? $participant->country->libelle_fr : $participant->country->libelle_en }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->total_amount > 0)
                                            <span class="label label-info">
                                                {{ number_format($invoice->total_amount, 0, ',', ' ') }}
                                                {{ $invoice->congres->currency ?? 'XAF' }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->status == App\Models\Invoice::PAYMENT_STATUS_PAID)
                                            <span
                                                class="label label-success">{{ app()->getLocale() == 'fr' ? 'Payé' : 'Paid' }}</span>
                                        @elseif($invoice->status == App\Models\Invoice::PAYMENT_STATUS_UNPAID)
                                            <span
                                                class="label label-warning">{{ app()->getLocale() == 'fr' ? 'Impayé' : 'Unpaid' }}</span>
                                        @else
                                            <span
                                                class="label label-danger">{{ app()->getLocale() == 'fr' ? 'Annulé' : 'Cancelled' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-sm btn-info btn-details" style="margin: 5px"
                                                data-id="{{ $participant->id ?? '' }}"
                                                title="{{ app()->getLocale() == 'fr' ? 'Voir les détails' : 'View details' }}">
                                                <i class="voyager-eye"></i> Détails
                                            </button>
                                            <a href="{{ route('participants.resend.invoice', $participant->id) }}"
                                                class="btn btn-sm btn-warning"
                                                title="{{ app()->getLocale() == 'fr' ? 'Renvoyer la facture' : 'Resend invoice' }}">
                                                <i class="bi bi-envelope-paper-fill"></i>
                                                {{ app()->getLocale() == 'fr' ? 'Renvoi de la facture' : 'Return invoice' }}
                                            </a>

                                            <a href="{{ route('participants.resend.invitation', $participant->id) }}"
                                                class="btn btn-sm btn-success"
                                                title="{{ app()->getLocale() == 'fr' ? 'Renvoyer la lettre d\'invitation' : 'Resend invitation letter' }}">
                                                <i class="voyager-eye"></i>
                                                {{ app()->getLocale() == 'fr' ? 'Renvoi de la lettre d\'invitation' : 'Return invitation letter' }}
                                            </a>

                                            <a href="{{ route('participants.resend.confirmation', $participant->id) }}"
                                                class="btn btn-dark btn-sm"
                                                title="{{ app()->getLocale() == 'fr' ? 'Renvoyer le mail de confirmation' : 'Resend confirmation email' }}">
                                                <i class="bi bi-envelope-check"></i>
                                                {{ app()->getLocale() == 'fr' ? 'Renvoi du mail de confirmation' : 'Return confirmation email' }}
                                            </a>

                                            @can('edit', $participant)
                                                <a href="{{ route('voyager.participants.edit', $participant->id) }}"
                                                    title="{{ __('voyager::generic.edit') }}" class="btn btn-sm btn-primary">
                                                    <i class="voyager-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $participant)
                                                <button type="button" class="btn btn-sm btn-danger delete"
                                                    data-id="{{ $participant->id ?? '' }}"
                                                    title="{{ __('voyager::generic.delete') }}">
                                                    <i class="voyager-trash"></i>
                                                </button>
                                            @endcan
                                            {{-- @if ($participant)
                                                <a href="{{ route('badge.view', $participant->id) }}"
                                                    class="btn btn-sm btn-success"
                                                    title="{{ app()->getLocale() == 'fr' ? 'Voir le badge' : 'View badge' }}"
                                                    target="_blank">
                                                    <i class="voyager-ticket"></i>
                                                </a>
                                            @endif --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <div style="padding: 50px;">
                                            <i class="voyager-people" style="font-size: 48px; color: #ccc;"></i>
                                            <h4 style="color: #999;">
                                                {{ app()->getLocale() == 'fr' ? 'Aucun participant trouvé' : 'No participants found' }}
                                            </h4>
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
    <div class="modal fade" id="participantModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ app()->getLocale() == 'fr' ? 'Détails du participant' : 'Participant Details' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="participantModalBody">
                    <!-- Contenu chargé via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ app()->getLocale() == 'fr' ? 'Fermer' : 'Close' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js">
    </script>

    <script>
        $(document).ready(function() {
            // Configuration
            const config = {
                locale: "{{ app()->getLocale() }}",
                routes: {
                    details: "{{ url('/admin/participants/details') }}/",
                    export: "#"
                }
            };

            // Initialiser DataTable
            const table = $('#participants-table').DataTable({
                language: config.locale === 'fr' ? {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
                } : {
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json"
                },
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "{{ app()->getLocale() === 'fr' ? 'Tous' : 'All' }}"]
                ],
                order: [
                    [1, 'asc']
                ],
                responsive: true,
                columnDefs: [{
                        orderable: false,
                        targets: [10]
                    },
                    {
                        searchable: false,
                        targets: [10]
                    }
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Filtres
            $('#search, #country, #status').on('keyup change', function() {
                table.draw();
            });

            // Gestionnaire de recherche personnalisé
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    const search = $('#search').val().toLowerCase();
                    const country = $('#country').val();
                    const status = $('#status').val();

                    // Filtre de recherche
                    if (search) {
                        const rowText = data.slice(1, 4).join(' ').toLowerCase();
                        if (rowText.indexOf(search) === -1) return false;
                    }

                    // Filtre par pays
                    if (country) {
                        const countryCell = data[7] || '';
                        if (countryCell.indexOf('value="' + country + '"') === -1) return false;
                    }

                    // Filtre par statut
                    if (status) {
                        const statusCell = data[9] || '';
                        const statusMap = {
                            'Paid': 'Payé',
                            'Unpaid': 'En attente',
                            'cancelled': 'Annulé'
                        };
                        const statusText = config.locale === 'fr' ? statusMap[status] : status;
                        if (statusCell.indexOf(statusText) === -1) return false;
                    }

                    return true;
                }
            );

            // Export Excel
            $('#export-excel').on('click', function() {
                const params = new URLSearchParams({
                    search: $('#search').val(),
                    country: $('#country').val(),
                    status: $('#status').val()
                });

                window.location.href = config.routes.export+'?' + params.toString();
            });

            // Gestion des détails du participant
            $(document).on('click', '.btn-details', function() {
                const id = $(this).data('id');
                if (!id) return;

                const modal = $('#participantModal');
                const modalBody = $('#participantModalBody');

                // Afficher le loader
                modalBody.html(`
                    <div class="text-center py-5">
                        <i class="voyager-refresh voyager-refresh-animate" style="font-size: 48px;"></i>
                        <p class="text-muted mt-3">${config.locale === 'fr' ? 'Chargement...' : 'Loading...'}</p>
                    </div>
                `);

                modal.modal('show');

                // Requête AJAX
                $.ajax({
                    url: config.routes.details + id,
                    method: "GET",
                    timeout: 15000,
                    success: function(response) {
                        if (!response || !response.participant) {
                            modalBody.html(`
                                <div class="text-center text-danger py-5">
                                    <i class="voyager-warning" style="font-size: 48px;"></i>
                                    <p class="mt-3">${config.locale === 'fr' ? 'Données non disponibles' : 'Data not available'}</p>
                                </div>
                            `);
                            return;
                        }

                        // Rendre les détails
                        modalBody.html(renderParticipantDetails(response));
                    },
                    error: function() {
                        modalBody.html(`
                            <div class="text-center text-danger py-5">
                                <i class="voyager-warning" style="font-size: 48px;"></i>
                                <p class="mt-3">${config.locale === 'fr' ? 'Erreur lors du chargement' : 'Error loading data'}</p>
                            </div>
                        `);
                    }
                });
            });

            // Fonction utilitaire pour échapper le HTML
            function escapeHtml(text) {
                if (!text) return '-';
                return String(text)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Fonction pour formater une date
            function formatDate(dateString) {
                if (!dateString) return '-';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString(config.locale, {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                } catch (e) {
                    return dateString;
                }
            }

            // Fonction pour rendre les détails du participant
            function renderParticipantDetails(data) {
                const {
                    participant,
                    invoice,
                    items
                } = data;
                const isFr = config.locale === 'fr';

                // Fonction helper pour créer un champ de détail
                function createDetailRow(labelFr, labelEn, value, isDate = false) {
                    if (!value || value === '-') return '';

                    const label = isFr ? labelFr : labelEn;
                    const displayValue = isDate ? formatDate(value) : escapeHtml(value);

                    return `
                        <div class="detail-row">
                            <span class="detail-label">${label}:</span>
                            <span class="detail-value">${displayValue}</span>
                        </div>
                    `;
                }

                // Fonction pour les relations
                function createRelationRow(labelFr, labelEn, relation) {
                    if (!relation || !relation.libelle) return '';
                    return createDetailRow(labelFr, labelEn, relation.libelle);
                }

                // Fonction pour les pays
                function createCountryRow(labelFr, labelEn, country) {
                    if (!country) return '';
                    const countryName = isFr ? country.libelle_fr : country.libelle_en;
                    return createDetailRow(labelFr, labelEn, countryName);
                }

                return `
                    <div class="row">
                        <!-- Informations personnelles -->
                        <div class="col-md-6">
                            <div class="detail-card">
                                <div class="detail-card-header">
                                    <i class="voyager-person"></i>
                                    <h5>${isFr ? 'Informations Personnelles' : 'Personal Information'}</h5>
                                </div>
                                <div class="detail-card-body">
                                    ${createRelationRow('Civilité', 'Civility', participant.civility_id)}
                                    ${createDetailRow('Nom', 'Last Name', participant.lname)}
                                    ${createDetailRow('Prénom', 'First Name', participant.fname)}
                                    ${createRelationRow('Genre', 'Gender', participant.gender)}
                                    ${createDetailRow('Email', 'Email', participant.email)}
                                    ${createDetailRow('Téléphone', 'Phone', participant.phone)}
                                    ${createCountryRow('Nationalité', 'Nationality', participant.country)}
                                </div>
                            </div>

                            <!-- Informations professionnelles -->
                            <div class="detail-card">
                                <div class="detail-card-header">
                                    <i class="voyager-briefcase"></i>
                                    <h5>${isFr ? 'Informations Professionnelles' : 'Professional Information'}</h5>
                                </div>
                                <div class="detail-card-body">
                                    ${createDetailRow('Organisation', 'Organization', participant.organisation)}
                                    ${createRelationRow('Type d\'organisation', 'Organization Type', participant.organisation_type)}
                                    ${createDetailRow('Poste', 'Position', participant.job)}
                                    ${createCountryRow('Pays de travail', 'Job Country', participant.job_country)}
                                </div>
                            </div>
                        </div>

                        <!-- Informations d'inscription -->
                        <div class="col-md-6">
                            <div class="detail-card">
                                <div class="detail-card-header">
                                    <i class="voyager-ticket"></i>
                                    <h5>${isFr ? 'Informations d\'Inscription' : 'Registration Information'}</h5>
                                </div>
                                <div class="detail-card-body">
                                    ${createRelationRow('Catégorie', 'Category', participant.participant_category)}
                                    ${createDetailRow('Dîner de gala', 'Gala Dinner', participant.diner)}
                                    ${createDetailRow('Visite', 'Visit', participant.visite)}
                                    ${createRelationRow('Site de visite', 'Visit Site', participant.site_visite)}
                                </div>
                            </div>

                            <!-- Informations de facturation -->
                            <div class="detail-card">
                                <div class="detail-card-header">
                                    <i class="voyager-credit-card"></i>
                                    <h5>${isFr ? 'Informations de Facturation' : 'Billing Information'}</h5>
                                </div>
                                <div class="detail-card-body">
                                    ${invoice ? `
                                                                                        ${createDetailRow('Numéro de facture', 'Invoice Number', invoice.invoice_number)}
                                                                                        ${createDetailRow('Montant total', 'Total Amount', invoice.total_amount ? invoice.total_amount + ' ' + (invoice.currency || 'XAF') : null)}
                                                                                        ${invoice.status ? `
                                            <div class="detail-row">
                                                <span class="detail-label">${isFr ? 'Statut' : 'Status'}:</span>
                                                <span class="detail-value">
                                                    ${invoice.status === 'Paid' ? 
                                                        '<span class="label label-success">' + (isFr ? 'Payé' : 'Paid') + '</span>' : 
                                                     invoice.status === 'Unpaid' ? 
                                                        '<span class="label label-warning">' + (isFr ? 'Impayé' : 'Unpaid') + '</span>' : 
                                                        '<span class="label label-danger">' + (isFr ? 'Annulé' : 'Cancelled') + '</span>'}
                                                </span>
                                            </div>
                                        ` : ''}
                                                                                    ` : `<p class="text-muted text-center">${isFr ? 'Aucune facture disponible' : 'No invoice available'}</p>`}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Nettoyer le modal quand il est fermé
            $('#participantModal').on('hidden.bs.modal', function() {
                $('#participantModalBody').html('');
            });

            // Gestion de la suppression
            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (!id) return;

                if (confirm(isFr ? 'Êtes-vous sûr de vouloir supprimer ce participant ?' :
                        'Are you sure you want to delete this participant?')) {
                    $.ajax({
                        url: "{{ url('admin/participants') }}/" + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            location.reload();
                        },
                        error: function() {
                            alert(isFr ? 'Erreur lors de la suppression' : 'Error deleting');
                        }
                    });
                }
            });

            document.querySelectorAll('.btn-resend-email').forEach(button => {
                button.addEventListener('click', function() {
                    const participantId = this.dataset.id;
                    const type = this.dataset.type;

                    // Vérification de base
                    if (!participantId || !type) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Données manquantes'
                        });
                        return;
                    }

                    // Détermination de l'URL
                    const baseUrl = '/get_register/resend';
                    const url = `${baseUrl}/${participantId}/resend-${type}`;

                    // Demande de confirmation
                    Swal.fire({
                        title: appLocale === 'fr' ? 'Confirmation' : 'Confirmation',
                        html: appLocale === 'fr' ?
                            `Voulez-vous vraiment renvoyer ${getEmailTypeLabel(type, 'fr')} ?` :
                            `Do you really want to resend ${getEmailTypeLabel(type, 'en')} ?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: appLocale === 'fr' ? 'Oui, envoyer' :
                            'Yes, send',
                        cancelButtonText: appLocale === 'fr' ? 'Annuler' : 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Afficher le loader
                            Swal.fire({
                                title: appLocale === 'fr' ?
                                    'Envoi en cours...' : 'Sending...',
                                html: appLocale === 'fr' ?
                                    'Veuillez patienter' : 'Please wait',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Envoyer la requête
                            fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document
                                            .querySelector(
                                                'meta[name="csrf-token"]')
                                            .content,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(
                                            `HTTP error! status: ${response.status}`
                                        );
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    // Fermer le loader
                                    Swal.close();

                                    // Afficher le résultat
                                    Swal.fire({
                                        icon: data.success ?
                                            'success' : 'error',
                                        title: data.success ?
                                            (appLocale === 'fr' ?
                                                'Succès' : 'Success'
                                            ) : (appLocale === 'fr' ?
                                                'Erreur' : 'Error'),
                                        text: data.message,
                                        confirmButtonText: 'OK'
                                    });
                                })
                                .catch(error => {
                                    // Fermer le loader
                                    Swal.close();

                                    // Afficher l'erreur
                                    Swal.fire({
                                        icon: 'error',
                                        title: appLocale === 'fr' ?
                                            'Erreur' : 'Error',
                                        text: appLocale === 'fr' ?
                                            'Erreur réseau ou serveur' :
                                            'Network or server error',
                                        confirmButtonText: 'OK'
                                    });

                                    console.error('Erreur:', error);
                                });
                        }
                    });
                });
            });

            // Fonction utilitaire pour obtenir le libellé du type d'email
            function getEmailTypeLabel(type, locale) {
                const labels = {
                    'invoice': {
                        'fr': 'la facture',
                        'en': 'the invoice'
                    },
                    'invitation': {
                        'fr': 'la lettre d\'invitation',
                        'en': 'the invitation letter'
                    },
                    'confirmation': {
                        'fr': 'le mail de confirmation',
                        'en': 'the confirmation email'
                    }
                };

                return labels[type]?.[locale] || '';
            }

            // Déterminer la locale actuelle
            const appLocale = document.documentElement.lang || 'fr';
        });
    </script>





@stop
