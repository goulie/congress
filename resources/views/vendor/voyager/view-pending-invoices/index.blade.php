@extends('voyager::master')

@section('page_title', 'Tableau de bord - Facturation ')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            TABLEAU DE BORD - GESTION DES FACTURES
        </h1>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .dashboard-card {
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }

        .card-icon {
            font-size: 40px;
            float: left;
            margin-right: 15px;
        }

        .card-content {
            overflow: hidden;
        }

        .card-title {
            font-size: 20px;
            color: #000000;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .panel-body-total {
            border-left: 5px solid blue !important;
        }

        .panel-body-validated {
            border-left: 5px solid green;
        }

        .panel-body-pending {
            border-left: 5px solid black;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
        }

        .btn-action {
            padding: 4px 8px;
            margin: 1px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .btn-action:hover {
            opacity: 0.8;
        }

        .btn-approve {
            background-color: #28a745;
            color: white;
        }

        .btn-reject {
            background-color: #dc3545;
            color: white;
        }

        .btn-details {
            background-color: #ffc107;
            color: white;
        }

        .loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: #3498db;
            z-index: 9999;
        }

        .hidden {
            display: none;
        }
    </style>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <!-- Loader -->
        <div id="loader" class="hidden">
            <i class="voyager-refresh voyager-animate-spin"></i>
        </div>

        <!-- Cartes de statistiques -->
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default dashboard-card">
                    <div class="panel-body panel-body-total">
                        <div class="card-icon">
                            <i class="bi bi-people-fill" style="color:blue"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">TOTAL À TRAITER</p>
                            <p class="card-value">Inscrits : {{ $stats['totalInvoices'] }}</p>
                            <p class="card-value">Montant :
                                {{ number_format($stats['amountTotal'], 0, ',', ' ') . ' ' . $congress->currency }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default dashboard-card">
                    <div class="panel-body panel-body-validated">
                        <div class="card-icon">
                            <i class="bi bi-patch-check-fill" style="color:green"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">PAYÉS</p>
                            <p class="card-value">Inscrits : {{ $stats['totalPaid'] }}</p>
                            <p class="card-value">Montant :
                                {{ number_format($stats['amountPaid'], 0, ',', ' ') . ' ' . $congress->currency }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default dashboard-card">
                    <div class="panel-body panel-body-pending">
                        <div class="card-icon">
                            <i class="bi bi-clock-fill" style="color:black"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">IMPRAYÉS</p>
                            <p class="card-value">Inscrits : {{ $stats['totalUnpaid'] }}</p>
                            <p class="card-value">Montant :
                                {{ number_format($stats['amountUnpaid'], 0, ',', ' ') . ' ' . $congress->currency }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions groupées -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-group">
                    <button class="btn btn-success btn-sm" onclick="approveSelected()">
                        <i class="voyager-check"></i> Valider sélection
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="rejectSelected()">
                        <i class="voyager-x"></i> Rejeter sélection
                    </button>
                    <button class="btn btn-info btn-sm" onclick="showSelected()">
                        <i class="voyager-eye"></i> Voir sélection
                    </button>
                </div>
                <span id="selectionCount" class="ml-2 badge badge-info">0 sélectionné(s)</span>
            </div>
        </div>

        <!-- Tableau -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <table id="inscritsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="checkAll" onchange="toggleAll()">
                                    </th>
                                    <th>FACTURE</th>
                                    <th>Participant</th>
                                    <th>Email</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr id="row-{{ $invoice->id }}">
                                        <td>
                                            @if ($invoice->status !== App\Models\Invoice::PAYMENT_STATUS_PAID)
                                                <input type="checkbox" class="invoice-check" value="{{ $invoice->id }}"
                                                    onchange="updateSelection()">
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('invoices.download.participant', $invoice->participant->id) }}"
                                                target="_blank">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $invoice->participant->lname . ' ' . $invoice->participant->fname }}</td>
                                        <td>{{ $invoice->participant->email }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ number_format($invoice->total_amount, 0, ',', ' ') . ' ' . $invoice->currency }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($invoice->status == App\Models\Invoice::PAYMENT_STATUS_PAID)
                                                <span class="badge badge-success">✓ Payé</span>
                                            @else
                                                <span class="badge badge-warning">⏳ En attente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($invoice->status !== App\Models\Invoice::PAYMENT_STATUS_PAID)
                                                <button class="btn-action btn-approve"
                                                    onclick="approveSingle({{ $invoice->id }})" title="Valider">
                                                    <i class="voyager-check"></i>
                                                </button>
                                                <button class="btn-action btn-reject"
                                                    onclick="rejectSingle({{ $invoice->id }})" title="Rejeter">
                                                    <i class="voyager-x"></i>
                                                </button>
                                            @endif
                                            <button class="btn-action btn-details"
                                                onclick="showDetails({{ $invoice->participant->id }})" title="Détails">
                                                <i class="voyager-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Swal = window.Swal;

        $(document).ready(function() {
            $('#inscritsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"
                },
                "pageLength": 25,
                "order": [
                    [1, "desc"]
                ]
            });
        });

        // === LOGIQUE SIMPLIFIÉE AVEC SWEETALERT ===

        // Gestion de la sélection
        function toggleAll() {
            const isChecked = $('#checkAll').prop('checked');
            $('.invoice-check').prop('checked', isChecked);
            updateSelection();
        }

        function updateSelection() {
            const count = $('.invoice-check:checked').length;
            $('#selectionCount').text(count + ' sélectionné(s)');
        }

        // === ACTIONS INDIVIDUELLES ===
        function approveSingle(id) {
            Swal.fire({
                title: 'Valider cette facture ?',
                text: "Cette action marquera la facture comme payée.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, valider',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.post(`/get_register/payment/approve/${id}`, {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        })
                        .done(res => {
                            updateRowStatus(id, 'approved');
                            Swal.fire({
                                icon: 'success',
                                title: 'Validé !',
                                text: res.message || 'Facture validée avec succès',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        })
                        .fail(xhr => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: xhr.responseJSON?.message || 'Erreur serveur'
                            });
                        })
                        .always(() => hideLoader());
                }
            });
        }

        function rejectSingle(id) {
            Swal.fire({
                title: 'Rejeter cette facture ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, rejeter',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.post(`/get_register/payment/reject/${id}`, {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        })
                        .done(res => {
                            updateRowStatus(id, 'rejected');
                            Swal.fire({
                                icon: 'success',
                                title: 'Rejeté !',
                                text: res.message || 'Facture rejetée avec succès',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        })
                        .fail(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Impossible de rejeter la facture'
                            });
                        })
                        .always(() => hideLoader());
                }
            });
        }

        function showDetails(id) {
            showLoader();
            $.get(`/get_register/payment/${id}/details`)
                .done(res => {
                    hideLoader();

                    let detailsHtml = `
                    <div style="text-align: left;">
                        <p><strong>Nom :</strong> ${res.lname}</p>
                        <p><strong>Prénom :</strong> ${res.fname}</p>
                        <p><strong>Email :</strong> ${res.email}</p>
                        <p><strong>Téléphone :</strong> ${res.phone || 'N/A'}</p>
                        <p><strong>Organisation :</strong> ${res.organisation || 'N/A'}</p>
                        <p><strong>Facture :</strong> ${res.invoice_number || res.id_invoice || 'N/A'}</p>
                        <p><strong>Montant :</strong> ${res.total_amount || 0} ${res.currency || ''}</p>
                        <p><strong>Statut :</strong> ${res.status || 'N/A'}</p>
                    </div>
                `;

                    Swal.fire({
                        title: 'Détails du participant',
                        html: detailsHtml,
                        icon: 'info',
                        confirmButtonText: 'Fermer'
                    });
                })
                .fail(() => {
                    hideLoader();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de charger les détails'
                    });
                });
        }

        // === ACTIONS GROUPÉES ===
        function approveSelected() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucune sélection',
                    text: 'Veuillez sélectionner au moins une facture',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            Swal.fire({
                title: `Valider ${ids.length} facture(s) ?`,
                text: "Cette action marquera toutes les factures sélectionnées comme payées.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Valider ${ids.length} facture(s)`,
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.post("{{ route('validation.approve_group.payment') }}", {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            invoices: ids.join(',')
                        })
                        .done(res => {
                            ids.forEach(id => updateRowStatus(id, 'approved'));
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès !',
                                text: res.message || `${ids.length} facture(s) validée(s)`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .fail(xhr => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: xhr.responseJSON?.message || 'Impossible de valider les factures'
                            });
                        })
                        .always(() => hideLoader());
                }
            });
        }

        function rejectSelected() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucune sélection',
                    text: 'Veuillez sélectionner au moins une facture',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            Swal.fire({
                title: `Rejeter ${ids.length} facture(s) ?`,
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Rejeter ${ids.length} facture(s)`,
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoader();
                    $.post("{{ route('validation.reject_group.payment') }}", {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            invoices: ids.join(',')
                        })
                        .done(res => {
                            ids.forEach(id => updateRowStatus(id, 'rejected'));
                            Swal.fire({
                                icon: 'success',
                                title: 'Rejeté !',
                                text: res.message || `${ids.length} facture(s) rejetée(s)`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .fail(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Impossible de rejeter les factures'
                            });
                        })
                        .always(() => hideLoader());
                }
            });
        }

        function showSelected() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Aucune sélection',
                    text: 'Aucune facture sélectionnée',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            Swal.fire({
                title: 'Factures sélectionnées',
                html: `
                    <p><strong>${ids.length} facture(s) sélectionnée(s)</strong></p>
                    <div style="max-height: 200px; overflow-y: auto; text-align: left;">
                        ${ids.map(id => `<div>• Facture ID: ${id}</div>`).join('')}
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // === FONCTIONS UTILITAIRES ===
        function getSelectedIds() {
            return $('.invoice-check:checked').map(function() {
                return $(this).val();
            }).get();
        }

        function updateRowStatus(id, action) {
            const row = $(`#row-${id}`);
            if (action === 'approved') {
                row.find('.invoice-check').parent().html('');
                row.find('.badge-warning').removeClass('badge-warning').addClass('badge-success').text('✓ Payé');
                row.find('.btn-approve, .btn-reject').remove();
            } else if (action === 'rejected') {
                row.fadeOut(300, function() {
                    $(this).remove();
                });
            }
            updateSelection();
        }

        function showLoader() {
            $('#loader').removeClass('hidden');
        }

        function hideLoader() {
            $('#loader').addClass('hidden');
        }

        // Notification automatique pour nouvelles factures (optionnel)
        setInterval(() => {
            const pendingCount = $('.badge-warning').length;
            if (pendingCount > 0) {
                document.title = `(${pendingCount}) Factures - Tableau de bord`;
            } else {
                document.title = 'Factures - Tableau de bord';
            }
        }, 5000);
    </script>
@stop
