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
    <style>
        .dashboard-card {
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
            transition: transform 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
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
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 0;
        }

        label {
            font-weight: bold;
            color: #000000
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

        .panel-body-rejected {
            border-left: 5px solid red;
        }


        /* Modal Material */
        .modal-content {
            border-radius: 14px !important;
            padding: 10px;
        }

        .modal-header {
            border: none !important;
        }

        .modal-footer {
            border: none !important;
        }

        /* File preview */
        .preview-box {
            border: 1px solid #e0e0e0;
            padding: 12px;
            border-radius: 10px;
            background: #fafafa;
        }
    </style>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <!-- Cartes de statistiques -->
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default dashboard-card card-total">
                    <div class="panel-body panel-body-total">
                        <div class="card-icon">
                            <i class="bi bi-people-fill" style="color:blue;font-weight:bold"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">TOTAL</p>
                            <p class="card-value">{{ $stats['totalInvoices'] }}</p>
                            <p class="card-value">{{ $stats['amountTotal'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default dashboard-card card-validated">
                    <div class="panel-body panel-body-validated">
                        <div class="card-icon">
                            <i class="bi bi-patch-check-fill" style="color:green;font-weight:bold"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">PAYES</p>
                            <p class="card-value">{{ $stats['totalPaid'] }}</p>
                            <p class="card-value">{{ $stats['amountPaid'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default dashboard-card card-pending">
                    <div class="panel-body panel-body-pending">
                        <div class="card-icon">
                            <i class="bi bi-clock-fill" style="color:rgb(0, 0, 0);font-weight:bold"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">IMPAYES</p>
                            <p class="card-value">{{ $stats['totalUnpaid'] }}</p>
                            <p class="card-value">{{ $stats['amountUnpaid'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Filtres -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('voyager.view-validation-ywp-students.index') }}"
                            id="filterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type_filter">Type d'inscrit:</label>
                                        <select name="type_filter" id="type_filter" class="form-control select2">
                                            <option value="">Tous les types</option>
                                            <option value="student"
                                                {{ request('type_filter') == 'student' ? 'selected' : '' }}>Étudiant
                                            </option>
                                            <option value="ywp" {{ request('type_filter') == 'ywp' ? 'selected' : '' }}>
                                                YWP</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status_filter">Statut validation:</label>
                                        <select name="status_filter" id="status_filter" class="form-control select2">
                                            <option value="">Tous les statuts</option>
                                            @foreach (\App\Models\StudentYwpValidation::getStatuses() as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ request('status_filter') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="voyager-search"></i> Filtrer
                                        </button>
                                        <a href="{{ route('voyager.view-validation-ywp-students.index') }}"
                                            class="btn btn-default">
                                            <i class="voyager-refresh"></i> Réinitialiser
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau avec DataTables -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="inscritsTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID FACTURE</th>
                                        <th>Nom complet</th>
                                        <th>Catégorie</th>
                                        <th>Email</th>
                                        <th>Methode Paiement</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Validé par</th>
                                        <th class="actions text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($participants as $participant)
                                        <tr>
                                            <td>
                                                <a href="#" style="text-decoration: none">
                                                    <span class="label label-info">
                                                        {{ $participant->invoice_number }}
                                                    </span>
                                                </a>

                                            </td>
                                            <td>{{ $participant->participant->lname . ' ' . $participant->participant->fname }}
                                            </td>
                                            <td>{{ $participant->participant->participantCategory->libelle }}</td>
                                            <td>{{ $participant->participant->email }}</td>
                                            <td>{{ $participant->payment_method ?? 'N/A' }}</td>
                                            <td>
                                                <span class="label label-info" style="font-weight: bold">
                                                    {{ $participant->total_amount . ' ' . $participant->currency }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($participant->status == App\Models\Invoice::PAYMENT_STATUS_PAID)
                                                    <span class="label label-success">{{ App\Models\Invoice::PAYMENT_STATUS_PAID }}</span>
                                                @else
                                                    <span class="label label-danger">Impayé</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $participant->userValidation->name ?? 'N/A' }}
                                            </td>

                                            <td class="no-sort no-click bread-actions">
                                                <a href="javascript:void(0);"
                                                    onclick="showParticipantDetails({{ $participant->participant->id }})"
                                                    class="btn btn-sm btn-warning pull-right" title="Voir">
                                                    <i class="voyager-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-success pull-right" title="Approuver"
                                                    style="margin-right: 5px;"
                                                    onclick="validatePayment({{ $participant->id }})">
                                                    <i class="voyager-check"></i>
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
    </div>

    {{-- Modal pour les détails du participant --}}
    <div class="modal fade" id="participantModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-badge mr-2"></i>
                        <span id="modalName">Participant</span>
                    </h4>
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">

                    <div class="row">

                        <!-- Informations personnelles -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 rounded">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="bi bi-person-lines-fill"></i> Informations personnelles
                                    </h5>
                                </div>
                                <div class="card-body">

                                    <p><strong>Nom :</strong> <span id="modalLname"></span></p>
                                    <p><strong>Prénoms :</strong> <span id="modalFname"></span></p>
                                    <p><strong>Email :</strong> <span id="modalEmail"></span></p>
                                    <p><strong>Téléphone :</strong> <span id="modalPhone"></span></p>
                                    <p><strong>Nationalité :</strong> <span id="modalNationality"></span></p>
                                    <p><strong>Catégorie :</strong> <span id="modalCategory"></span></p>
                                    <p><strong>Organisation :</strong> <span id="modalOrganisation"></span></p>
                                    <p><strong>Paiement Validé par :</strong> <span id="modalValidator"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Informations facture -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 rounded">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="bi bi-receipt"></i> Informations de la facture
                                    </h5>
                                </div>
                                <div class="card-body">

                                    <p><strong>Numéro facture :</strong><br>
                                        <span class="text-primary font-weight-bold" id="modalInvoiceNumber"></span>
                                    </p>

                                    <p><strong>Date facture :</strong>
                                        <span id="modalInvoiceDate"></span>
                                    </p>

                                    <p><strong>Montant total :</strong>
                                        <span class="badge badge-info p-2" id="modalTotalAmount"></span>
                                    </p>

                                    <p><strong>Montant payé :</strong>
                                        <span class="badge badge-success p-2" id="modalAmountPaid"></span>
                                    </p>

                                    <p><strong>Statut paiement :</strong>
                                        <span id="modalInvoiceStatus"></span>
                                    </p>

                                    <p><strong>Méthode de paiement :</strong>
                                        <span id="modalPaymentMethod"></span>
                                    </p>

                                    <p><strong>Date de paiement :</strong>
                                        <span id="modalPaymentDate"></span>
                                    </p>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Fermer
                    </button>
                </div>

            </div>
        </div>
    </div>

@stop



@section('javascript')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialisation de DataTables
            var table = $('#inscritsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"
                },
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 25,
                "responsive": true
            });

            // Initialisation de Select2
            $('.select2').select2();

            // Soumission automatique du formulaire de filtrage
            $('#type_filter, #status_filter, #congres_filter').on('change', function() {
                $('#filterForm').submit();
            });

            // Confirmation pour l'approbation
            $('form[action*="approve"]').on('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir approuver cette inscription ?')) {
                    e.preventDefault();
                }
            });
        });

        //Affichage des détails du participant
        function showParticipantDetails(id) {
            $.ajax({
                url: "/get_register/payment/" + id + "/details",
                method: "GET",
                success: function(res) {

                    // ==== Informations personnelles ====
                    $("#modalName").text(res.fname + " " + res.lname);
                    $("#modalFname").text(res.fname);
                    $("#modalLname").text(res.lname);
                    $("#modalEmail").text(res.email);
                    $("#modalPhone").text(res.phone);
                    $("#modalCategory").text(res.category);
                    $("#modalNationality").text(res.nationality);
                    $("#modalOrganisation").text(res.organisation);
                    $("#modalValidator").text(res.validator);
                    // ==== Informations facture ====
                    $("#modalInvoiceNumber").text(res.id_invoice);
                    $("#modalInvoiceDate").text(res.invoice_date);
                    $("#modalTotalAmount").text(res.total_amount + " " + res.currency);
                    $("#modalAmountPaid").text(res.amount_paid + " " + res.currency);

                    // Statut Paiement
                    let statusBadge = {
                        "paid": "<span class='badge badge-success'>Payée</span>",
                        "pending": "<span class='badge badge-warning'>En attente</span>",
                        "unpaid": "<span class='badge badge-danger'>Non payée</span>"
                    };
                    $("#modalInvoiceStatus").html(statusBadge[res.status] ?? res.status);

                    $("#modalPaymentMethod").text(res.payment_method ?? "N/A");
                    $("#modalPaymentDate").text(res.payment_date ?? "N/A");

                    $("#participantModal").modal("show");
                }
            });
        }


        //
        let paymentMethods = @json(\App\Models\Invoice::getPaymentMethod());

        function validatePayment(id) {

            let options = `<option value="">-- Sélectionner --</option>`;
            for (const [key, label] of Object.entries(paymentMethods)) {
                options += `<option value="${key}">${label}</option>`;
            }

            Swal.fire({
                title: "Valider le paiement",
                html: `
            <div class="text-left">
                <label><strong>Mode de paiement :</strong></label>
                <select id="paymentMethod" class="swal2-select" style="width:100%; padding:8px;">
                    ${options}
                </select>
            </div>
        `,
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Valider",
                cancelButtonText: "Annuler",
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",

                preConfirm: () => {
                    let method = document.getElementById("paymentMethod").value;

                    if (!method) {
                        Swal.showValidationMessage("Veuillez choisir une méthode de paiement.");
                    }

                    return {
                        method: method
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "/get_register/payment/approve/" + id ,
                        method: "POST",
                        data: {
                            _token: $("meta[name='csrf-token']").attr("content"),
                            payment_method: result.value.method
                        },
                        success: function(res) {

                            Swal.fire({
                                icon: "success",
                                title: "Paiement validé",
                                text: res.message,
                                confirmButtonColor: "#28a745"
                            });

                            $("#participantModal").modal("hide");

                            setTimeout(() => location.reload(), 1200);
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Erreur",
                                text: "Une erreur s'est produite lors de la validation.",
                            });
                        }
                    });

                }
            });
        }
    </script>
@stop
