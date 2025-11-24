@extends('voyager::master')

@section('page_title', 'Tableau de bord - Traitement des Jeunes professionnels et étudiants')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
           TABLEAU DE BORD - DOSSIERS YWP & ÉTUDIANTS
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
            <div class="col-md-3">
                <div class="panel panel-default dashboard-card card-total">
                    <div class="panel-body panel-body-total">
                        <div class="card-icon">
                            <i class="bi bi-people-fill" style="color:blue;font-weight:bold"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">TOTAL</p>
                            <p class="card-value">{{ $stats['totalParticipants'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="panel panel-default dashboard-card card-validated">
                    <div class="panel-body panel-body-validated">
                        <div class="card-icon">
                            <i class="bi bi-patch-check-fill" style="color:green;font-weight:bold"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">VALIDES</p>
                            <p class="card-value">{{ $stats['validatedParticipants'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="panel panel-default dashboard-card card-pending">
                    <div class="panel-body panel-body-pending">
                        <div class="card-icon">
                            <i class="bi bi-clock-fill" style="color:rgb(0, 0, 0);font-weight:bold"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">A VALIDER</p>
                            <p class="card-value">{{ $stats['pendingValidations'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default dashboard-card card-pending">
                    <div class="panel-body panel-body-rejected">
                        <div class="card-icon">
                            <i class="bi bi-x-octagon-fill" style="color:red;font-weight:bold"></i>
                        </div>
                        <div class="card-content">
                            <p class="card-title">REJETES</p>
                            <p class="card-value">{{ $stats['rejectedValidations'] }}</p>
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
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Date d'inscription</th>
                                        <th>Type</th>
                                        <th>Statut Validation</th>
                                        <th class="actions text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($participants as $participant)
                                        <tr>
                                            <td>{{ $participant->id }}</td>
                                            <td>{{ $participant->lname }}</td>
                                            <td>{{ $participant->fname }}</td>
                                            <td>{{ $participant->email }}</td>
                                            <td>{{ $participant->phone }}</td>
                                            <td>{{ $participant->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if ($participant->ywp_or_student == 'student')
                                                    <span class="label label-info">Étudiant</span>
                                                @elseif($participant->ywp_or_student == 'ywp')
                                                    <span class="label label-warning">YWP</span>
                                                @else
                                                    <span class="label label-default">Régulier</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $validation = $participant->validation_ywp_student->last();
                                                @endphp

                                                @if ($validation)
                                                    @switch($validation->status)
                                                        @case(\App\Models\StudentYwpValidation::STATUS_APPROVED)
                                                            <span class="label label-success">Validé</span>
                                                        @break

                                                        @case(\App\Models\StudentYwpValidation::STATUS_REJECTED)
                                                            <span class="label label-danger">Rejeté</span>
                                                        @break

                                                        @default
                                                            <span class="label label-warning">En attente</span>
                                                    @endswitch
                                                @else
                                                    <span class="label label-default">Non soumis</span>
                                                @endif
                                            </td>

                                            <td class="no-sort no-click bread-actions">
                                                <a href="javascript:void(0);"
                                                    onclick="showParticipantDetails({{ $participant->id }})"
                                                    class="btn btn-sm btn-warning pull-right" title="Voir">
                                                    <i class="voyager-eye"></i>
                                                </a>

                                                @if ($participant->ywp_or_student && $participant->isYwpOrStudent == false)
                                                    <form action="{{ route('validation.approve', $participant->id) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success pull-right"
                                                            title="Approuver" style="margin-right: 5px;">
                                                            <i class="voyager-check"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-danger pull-right"
                                                        data-toggle="modal"
                                                        data-target="#rejectModal{{ $participant->id }}" title="Rejeter"
                                                        style="margin-right: 5px;">
                                                        <i class="voyager-x"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Modal Rejet -->
                                        @if ($participant->ywp_or_student && $participant->isYwpOrStudent == false)
                                            <div class="modal fade" id="rejectModal{{ $participant->id }}"
                                                tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('validation.reject', $participant->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">Rejeter l'inscription de
                                                                    {{ $participant->fname }} {{ $participant->lname }}
                                                                </h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="reason">Raison du rejet:</label>
                                                                    <textarea class="form-control" id="reason" name="reason" rows="4"
                                                                        placeholder="Veuillez spécifier la raison du rejet..." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Annuler</button>
                                                                <button type="submit" class="btn btn-danger">Confirmer le
                                                                    rejet</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="bi bi-person-badge"></i>
                        <span id="modalName">Participant</span>
                    </h4>
                    <button type="button" class="btn btn-light" data-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6">
                            <h5><i class="bi bi-person-lines-fill"></i> Informations personnelles</h5>
                            <p><strong>Nom :</strong> <span id="modalLname"></span></p>
                            <p><strong>Prénoms :</strong> <span id="modalFname"></span></p>
                            <p><strong>Email :</strong> <span id="modalEmail"></span></p>
                            <p><strong>Téléphone :</strong> <span id="modalPhone"></span></p>
                            <p><strong>Type :</strong> <span id="modalType"></span></p>
                            <p><strong>Statut :</strong> <span id="modalStatus"></span></p>
                        </div>

                        <div class="col-md-6">
                            <h5><i class="bi bi-file-earmark"></i> Document fourni</h5>

                            <div id="modalDocumentBox" class="preview-box">
                                <div id="modalDocType"></div>
                                <a href="" id="modalDocLink" class="btn btn-sm btn-primary mt-2" target="_blank">
                                    <i class="bi bi-box-arrow-up-right"></i> Ouvrir le fichier
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-m" data-dismiss="modal">
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
                url: "/get_register/validator/" + id + "/details",
                method: "GET",
                success: function(res) {

                    $("#modalName").text(res.fname + " " + res.lname);
                    $("#modalFname").text(res.fname);
                    $("#modalLname").text(res.lname);
                    $("#modalEmail").text(res.email);
                    $("#modalPhone").text(res.phone);

                    // Type
                    let type = res.ywp_or_student === "student" ?
                        "<span class='badge badge-info badge-m'>Étudiant</span>" :
                        "<span class='badge badge-warning badge-m'>YWP</span>";

                    $("#modalType").html(type);

                    // Statut
                    let statusBadge = {
                        "approved": "<span class='badge badge-success badge-m'>Validé</span>",
                        "rejected": "<span class='badge badge-danger badge-m'>Rejeté</span>",
                        "pending": "<span class='badge badge-warning badge-m'>En attente</span>"
                    };
                    $("#modalStatus").html(statusBadge[res.validation_status]);

                    // Document joint
                    if (res.document_url) {
                        $("#modalDocType").html("<strong>Fichier :</strong> " + res.document_type);
                        $("#modalDocLink").attr("href", res.document_url).show();
                    } else {
                        $("#modalDocType").html("<strong>Aucun document fourni.</strong>");
                        $("#modalDocLink").hide();
                    }

                    $("#participantModal").modal("show");
                }
            });
        }
    </script>
@stop
