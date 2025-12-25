@extends('voyager::master')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/AdminLTE.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />

    <style>
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

        .panel-custom {
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(1, 33, 160, 0.08);
            border: 1px solid rgba(1, 33, 160, 0.08);
            margin-top: 20px;
        }

        /* Barre de contrôle */
        .batch-controls {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e9ecef;
        }

        .batch-controls .form-check {
            margin-bottom: 0;
        }

        .selection-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .selected-count {
            font-weight: bold;
            color: #2c80ff;
            font-size: 16px;
            background: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            border: 1px solid #2c80ff;
        }

        .print-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .print-btn:hover {
            background: linear-gradient(135deg, #218838, #1aa179);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
        }

        .print-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Checkbox styling */
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-container input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Selected row styling */
        .selected-row {
            background-color: rgba(44, 128, 255, 0.08) !important;
        }

        .selected-row td {
            border-left: 3px solid #2c80ff;
        }

        /* Actions rapides */
        .quick-actions {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .quick-btn {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-btn:hover {
            background: #f8f9fa;
            border-color: #2c80ff;
        }

        /* Table adjustments */
        .table-responsive {
            padding: 0;
        }

        .table>tbody>tr>td:first-child {
            text-align: center;
            vertical-align: middle;
        }

        .table>thead>tr>th:first-child {
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .batch-controls {
                padding: 12px 15px;
            }

            .selection-info {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .quick-actions {
                margin-left: 0;
                justify-content: center;
            }

            .print-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('page_title', __('Gestion des Badges'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="box-header">
                        <h1>{{ app()->getLocale() == 'fr' ? 'GESTION DES BADGES' : 'BADGES MANAGEMENT' }}</h1>
                        <p class="help-block" style="margin-top: 10px;">
                            {{ app()->getLocale() == 'fr' ? 'Sélectionnez les badges à imprimer' : 'Select badges to print' }}
                        </p>
                    </div>
                    <div class="container">
                        <div class="row text-center">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#AddInviteModal">
                                {{ app()->getLocale() == 'fr' ? 'Ajouter un invité' : 'Add an invite' }}
                            </button>
                        </div>
                    </div>

                    <!-- Barre de contrôle pour la sélection -->
                    <div class="batch-controls">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="selection-info">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="checkAll" class="form-check-input">
                                    <label for="checkAll" class="form-check-label mb-0">
                                        <strong>{{ app()->getLocale() == 'fr' ? 'Tout sélectionner' : 'Select all' }}</strong>
                                    </label>
                                </div>

                                <span class="selected-count">
                                    <span id="selectedCount">0</span>
                                    {{ app()->getLocale() == 'fr' ? 'sélectionné(s)' : 'selected' }}
                                </span>

                                <div class="quick-actions">
                                    <button type="button" class="quick-btn" id="selectVisibleBtn">
                                        {{ app()->getLocale() == 'fr' ? 'Sélectionner visibles' : 'Select visible' }}
                                    </button>
                                    <button type="button" class="quick-btn" id="deselectAllBtn">
                                        {{ app()->getLocale() == 'fr' ? 'Tout désélectionner' : 'Deselect all' }}
                                    </button>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('badges.print.selected') }}" id="printForm"
                                target="_blank" class="mt-2 mt-md-0">
                                @csrf
                                <button type="submit" class="print-btn" id="printBtn" style="margin-top:10px" disabled>
                                    <i class="voyager-printer"></i>
                                    <span id="printBtnText">
                                        {{ app()->getLocale() == 'fr' ? 'Imprimer la sélection' : 'Print selection' }}
                                    </span>
                                    (<span id="printCount">0</span>)
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="panel panel-bordered panel-custom" style="margin-top:20px">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="participants-table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="3%">
                                                <input type="checkbox" id="checkAllHeader">
                                            </th>
                                            <th width="15%">
                                                {{ app()->getLocale() == 'fr' ? 'Nom & Prénoms' : 'Full Name' }}</th>
                                            <th width="15%">
                                                {{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}</th>
                                            <th width="15%">{{ app()->getLocale() == 'fr' ? 'Fonction' : 'Function' }}
                                            </th>
                                            <th width="15%">{{ app()->getLocale() == 'fr' ? 'Rôle' : 'Role' }}</th>
                                            <th width="15%">
                                                {{ app()->getLocale() == 'fr' ? 'Nationalité' : 'Nationality' }}</th>
                                            <th width="7%" class="actions text-right">
                                                {{ app()->getLocale() == 'fr' ? 'Actions' : 'Actions' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($badges as $badge)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="badge-checkbox" name="badge_ids[]"
                                                        value="{{ $badge->id }}"
                                                        data-name="{{ $badge->badge_full_name ?? $badge->civility?->libelle . ' ' . $badge->fname . ' ' . $badge->lname }}">
                                                </td>
                                                <td>{{ $badge->badge_full_name ?? $badge->civility?->libelle . ' ' . $badge->fname . ' ' . $badge->lname }}
                                                </td>
                                                <td>{{ $badge->organisation }}</td>
                                                <td>{{ $badge->role_badge_congres ?? $badge->job }}</td>
                                                <td>
                                                    <span style="color: {{ $badge->badge_color->color }};font-weight:bold">
                                                        {{ $badge->badge_color->libelle }}
                                                    </span>
                                                </td>
                                                <td>{{ $badge->nationality->libelle_fr }}</td>
                                                <td class="actions text-right">
                                                    {{-- <a class="btn btn-sm btn-primary action-btn"
                                                        href="{{ route('badge.view', $badge->id) }}"
                                                        title="{{ app()->getLocale() == 'fr' ? 'Voir' : 'View' }}">
                                                        <i class="voyager-eye"></i>
                                                    </a> --}}
                                                    <button class="btn btn-sm btn-primary action-btn btn-edit-badge"
                                                        data-id="{{ $badge->id }}"
                                                        data-name="{{ $badge->badge_full_name ?? $badge->civility?->libelle . ' ' . $badge->fname . ' ' . $badge->lname }}"
                                                        data-organisation="{{ $badge->organisation }}"
                                                        data-role="{{ $badge->role_badge_congres ?? $badge->job }}"
                                                        data-color="{{ $badge->badge_color_id }}"
                                                        data-nationality="{{ $badge->nationality_id }}"
                                                        title="{{ app()->getLocale() == 'fr' ? 'Modifier' : 'Edit' }}">
                                                        <i class="voyager-edit"></i>
                                                    </button>

                                                    @if ($badge->type_participant == 'invite')
                                                    
                                                        <button class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $badge->id }}"
                                                            title="{{ app()->getLocale() == 'fr' ? 'Supprimer' : 'Delete' }}">
                                                            <i class="voyager-trash"></i>
                                                        </button>
                                                    @endif
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
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade" id="editBadgeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ app()->getLocale() == 'fr' ? 'Modifier le badge' : 'Edit badge' }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editBadgeForm">
                        @csrf
                        <input type="hidden" id="badge_id">
                        <div class="form-group">
                            <label>{{ app()->getLocale() == 'fr' ? 'Nom complet' : 'Full name' }}</label>
                            <input type="text" id="badge_full_name" name="badge_full_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}</label>
                            <input type="text" id="organisation" name="organisation" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ app()->getLocale() == 'fr' ? 'Fonction' : 'Function' }}</label>
                            <input type="text" id="role_badge_congres" name="role_badge_congres"
                                class="form-control">
                        </div>
                        <div class="form-group">
                            <label>{{ app()->getLocale() == 'fr' ? 'Couleur du badge' : 'Badge color' }}</label>
                            <select id="badge_color_id" name="badge_color_id" class="form-control">
                                @foreach (App\Models\BadgeColor::get() as $color)
                                    <option value="{{ $color->id }}">{{ $color->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ app()->getLocale() == 'fr' ? 'Nationalité' : 'Nationality' }}</label>
                            <select id="nationality_id" name="nationality_id" class="form-control">
                                @foreach (App\Models\Country::get() as $n)
                                    <option value="{{ $n->id }}">{{ $n->libelle_fr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">
                        {{ app()->getLocale() == 'fr' ? 'Annuler' : 'Cancel' }}
                    </button>
                    <button class="btn btn-primary" id="saveBadgeBtn">
                        {{ app()->getLocale() == 'fr' ? 'Sauvegarder' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout/modification -->
    <div class="modal fade" id="AddInviteModal" tabindex="-1" role="dialog" aria-labelledby="inviteModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="inviteModalLabel">
                        {{ app()->getLocale() == 'fr' ? 'Ajouter un invité' : 'Add an invite' }}
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="inviteForm">
                        @csrf
                        <input type="hidden" id="invite_id" name="invite_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">{{ app()->getLocale() == 'fr' ? 'Nom' : 'Last Name' }} *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                    <div class="invalid-feedback" id="nom-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenoms">{{ app()->getLocale() == 'fr' ? 'Prénoms' : 'First Name' }}
                                        *</label>
                                    <input type="text" class="form-control" id="prenoms" name="prenoms" required>
                                    <div class="invalid-feedback" id="prenoms-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="genre">{{ app()->getLocale() == 'fr' ? 'Genre' : 'Gender' }} *</label>
                                    <select class="form-control" id="genre" name="genre" required required>
                                        <option selected disabled value="">
                                            {{ __('registration.choose') ?? 'Select' }}</option>
                                        @forelse (App\Models\Gender::get()->translate(app()->getLocale(), 'fallbackLocale') as $gender)
                                            <option value="{{ $gender->id }}"
                                                {{ isset($participant) && $participant->gender_id == $gender->id ? 'selected' : '' }}>
                                                {{ $gender->libelle }}
                                            </option>
                                        @empty
                                            <option disabled>{{ __('registration.no_data') ?? 'No data' }}</option>
                                        @endforelse
                                    </select>
                                    <div class="invalid-feedback" id="genre-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label
                                        for="nationalite">{{ app()->getLocale() == 'fr' ? 'Nationalité' : 'Nationality' }}
                                        *</label>
                                    <select class="form-control" id="nationalite" name="nationalite" required>
                                        <option value="">
                                            {{ app()->getLocale() == 'fr' ? 'Sélectionner' : 'Select' }}</option>
                                        <!-- Les options seront chargées dynamiquement -->
                                    </select>
                                    <div class="invalid-feedback" id="nationalite-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">{{ app()->getLocale() == 'fr' ? 'Téléphone' : 'Phone' }}
                                        *</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" required>
                                    <div class="invalid-feedback" id="telephone-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label
                                        for="organisation">{{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}
                                        *</label>
                                    <input type="text" class="form-control" id="organisation" name="organisation"
                                        required>
                                    <div class="invalid-feedback" id="organisation-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="job">{{ app()->getLocale() == 'fr' ? 'Fonction' : 'Job' }} *</label>
                                    <input type="text" class="form-control" id="job" name="job" required>
                                    <div class="invalid-feedback" id="job-error"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        {{ app()->getLocale() == 'fr' ? 'Fermer' : 'Close' }}
                    </button>
                    <button type="button" class="btn btn-primary" id="saveInviteBtn">
                        {{ app()->getLocale() == 'fr' ? 'Enregistrer' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Initialisation de DataTables
            const dataTable = $('#participants-table').DataTable({
                language: {
                    url: "{{ app()->getLocale() == 'fr' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}",
                    search: "{{ app()->getLocale() == 'fr' ? 'Rechercher:' : 'Search:' }}",
                    searchPlaceholder: "{{ app()->getLocale() == 'fr' ? 'Rechercher un participant...' : 'Search a participant...' }}"
                },
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "{{ app()->getLocale() == 'fr' ? 'Tous' : 'All' }}"]
                ],
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                        orderable: false,
                        targets: [0, 6]
                    },
                    {
                        searchable: false,
                        targets: [0, 6]
                    },
                    {
                        className: 'dt-body-center',
                        targets: [0]
                    }
                ],
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                stateSave: true,
                initComplete: function() {
                    // Restaurer les sélections au chargement
                    restoreSelections();
                    updateSelectionUI();
                },
                drawCallback: function(settings) {
                    // Restaurer les sélections après chaque redessinage
                    restoreSelections();
                    updateSelectionUI();
                    // Réattacher les événements
                    attachCheckboxEvents();
                }
            });

            // Variables globales - Stocke TOUTES les sélections
            let selectedBadges = [];

            // Charger les sélections initiales
            document.querySelectorAll('.badge-checkbox:checked').forEach(checkbox => {
                selectedBadges.push(checkbox.value);
            });

            // Restaurer les sélections après pagination/recherche
            function restoreSelections() {
                if (selectedBadges.length === 0) return;

                document.querySelectorAll('.badge-checkbox').forEach(checkbox => {
                    const isSelected = selectedBadges.includes(checkbox.value);
                    checkbox.checked = isSelected;

                    const row = checkbox.closest('tr');
                    if (isSelected) {
                        row.classList.add('selected-row');
                    } else {
                        row.classList.remove('selected-row');
                    }
                });
            }

            // Réattacher les événements aux checkboxes
            function attachCheckboxEvents() {
                document.querySelectorAll('.badge-checkbox').forEach(checkbox => {
                    // Remplacer le checkbox pour éviter les doublons d'événements
                    const newCheckbox = checkbox.cloneNode(true);
                    checkbox.parentNode.replaceChild(newCheckbox, checkbox);

                    newCheckbox.addEventListener('change', function(e) {
                        const badgeId = this.value;
                        const row = this.closest('tr');

                        if (this.checked) {
                            // Ajouter à la sélection si pas déjà présent
                            if (!selectedBadges.includes(badgeId)) {
                                selectedBadges.push(badgeId);
                            }
                            row.classList.add('selected-row');
                        } else {
                            // Retirer de la sélection
                            const index = selectedBadges.indexOf(badgeId);
                            if (index > -1) {
                                selectedBadges.splice(index, 1);
                            }
                            row.classList.remove('selected-row');
                        }

                        updateSelectionUI();
                    });
                });
            }

            // Fonction pour mettre à jour le compteur et le bouton
            function updateSelectionUI() {
                const totalSelected = selectedBadges.length;

                document.getElementById('selectedCount').textContent = totalSelected;
                document.getElementById('printCount').textContent = totalSelected;

                const printBtn = document.getElementById('printBtn');
                const printBtnText = document.getElementById('printBtnText');

                if (totalSelected === 0) {
                    printBtn.disabled = true;
                    printBtnText.textContent =
                        "{{ app()->getLocale() == 'fr' ? 'Imprimer la sélection' : 'Print selection' }}";
                } else {
                    printBtn.disabled = false;
                    if (totalSelected === 1) {
                        printBtnText.textContent =
                            "{{ app()->getLocale() == 'fr' ? 'Imprimer 1 badge' : 'Print 1 badge' }}";
                    } else {
                        printBtnText.textContent = "{{ app()->getLocale() == 'fr' ? 'Imprimer' : 'Print' }} " +
                            totalSelected + " {{ app()->getLocale() == 'fr' ? 'badges' : 'badges' }}";
                    }
                }

                // Mettre à jour "Tout sélectionner" pour les éléments VISIBLES uniquement
                const visibleCheckboxes = document.querySelectorAll('.badge-checkbox');
                const allVisibleSelected = visibleCheckboxes.length > 0 &&
                    Array.from(visibleCheckboxes).every(cb => cb.checked);

                document.getElementById('checkAll').checked = allVisibleSelected;
                document.getElementById('checkAllHeader').checked = allVisibleSelected;

                return totalSelected;
            }

            // Sélectionner/désélectionner toutes les lignes VISIBLES
            function toggleAllVisible(checked) {
                // Récupérer toutes les lignes visibles (via DataTables API)
                const visibleRows = dataTable.rows({
                    search: 'applied'
                }).nodes();

                $(visibleRows).find('.badge-checkbox').each(function() {
                    const badgeId = this.value;

                    if (checked) {
                        // Cocher et ajouter à la sélection
                        this.checked = true;
                        if (!selectedBadges.includes(badgeId)) {
                            selectedBadges.push(badgeId);
                        }
                        this.closest('tr').classList.add('selected-row');
                    } else {
                        // Décocher et retirer de la sélection
                        this.checked = false;
                        const index = selectedBadges.indexOf(badgeId);
                        if (index > -1) {
                            selectedBadges.splice(index, 1);
                        }
                        this.closest('tr').classList.remove('selected-row');
                    }
                });

                updateSelectionUI();
            }

            // "Tout sélectionner" dans la barre de contrôle
            document.getElementById('checkAll').addEventListener('change', function() {
                toggleAllVisible(this.checked);
            });

            // "Tout sélectionner" dans l'en-tête du tableau
            document.getElementById('checkAllHeader').addEventListener('change', function() {
                toggleAllVisible(this.checked);
            });

            // "Sélectionner les visibles" - ajoute les visibles à la sélection existante
            document.getElementById('selectVisibleBtn').addEventListener('click', function() {
                const visibleRows = dataTable.rows({
                    search: 'applied'
                }).nodes();

                $(visibleRows).find('.badge-checkbox').each(function() {
                    const badgeId = this.value;

                    if (!this.checked) {
                        this.checked = true;
                        if (!selectedBadges.includes(badgeId)) {
                            selectedBadges.push(badgeId);
                        }
                        this.closest('tr').classList.add('selected-row');
                    }
                });

                updateSelectionUI();
            });

            // "Tout désélectionner" - vide complètement la sélection
            document.getElementById('deselectAllBtn').addEventListener('click', function() {
                // Décocher tous les checkboxes visibles
                document.querySelectorAll('.badge-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.closest('tr').classList.remove('selected-row');
                });

                // Vider toutes les sélections
                selectedBadges = [];
                updateSelectionUI();
            });

            // Réattacher les événements initialement
            attachCheckboxEvents();

            // Soumission du formulaire d'impression
            document.getElementById('printForm').addEventListener('submit', function(e) {
                if (selectedBadges.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: "{{ app()->getLocale() == 'fr' ? 'Attention' : 'Attention' }}",
                        text: "{{ app()->getLocale() == 'fr' ? 'Veuillez sélectionner au moins un badge' : 'Please select at least one badge' }}",
                        confirmButtonText: "{{ app()->getLocale() == 'fr' ? 'OK' : 'OK' }}"
                    });
                    return false;
                }

                e.preventDefault();
                Swal.fire({
                    title: "{{ app()->getLocale() == 'fr' ? 'Confirmer l\'impression' : 'Confirm print' }}",
                    html: "{{ app()->getLocale() == 'fr' ? 'Êtes-vous sûr de vouloir imprimer' : 'Are you sure you want to print' }} <strong>" +
                        selectedBadges.length +
                        " {{ app()->getLocale() == 'fr' ? 'badge(s)' : 'badge(s)' }}</strong> ?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: "{{ app()->getLocale() == 'fr' ? 'Oui, imprimer' : 'Yes, print' }}",
                    cancelButtonText: "{{ app()->getLocale() == 'fr' ? 'Annuler' : 'Cancel' }}",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Nettoyer les anciens inputs
                        this.querySelectorAll('input[name="badge_ids[]"]').forEach(el => el
                            .remove());

                        // Ajouter tous les badges sélectionnés
                        selectedBadges.forEach(badgeId => {
                            let input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'badge_ids[]';
                            input.value = badgeId;
                            this.appendChild(input);
                        });

                        this.submit();
                    }
                });
            });

            // Initialiser l'UI
            updateSelectionUI();

            // Gestion des alertes de session
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

            // Édition des badges avec event delegation
            $(document).on('click', '.btn-edit-badge', function() {
                document.getElementById('badge_id').value = this.dataset.id;
                document.getElementById('badge_full_name').value = this.dataset.name;
                document.getElementById('organisation').value = this.dataset.organisation;
                document.getElementById('role_badge_congres').value = this.dataset.role;
                document.getElementById('badge_color_id').value = this.dataset.color;
                document.getElementById('nationality_id').value = this.dataset.nationality;
                $('#editBadgeModal').modal('show');
            });

            // Sauvegarde AJAX
            document.getElementById('saveBadgeBtn').addEventListener('click', function() {
                let id = document.getElementById('badge_id').value;
                const btn = this;
                const originalText = btn.innerHTML;

                btn.innerHTML = '<i class="voyager-refresh voyager-animate-spin"></i> ' +
                    "{{ app()->getLocale() == 'fr' ? 'Sauvegarde...' : 'Saving...' }}";
                btn.disabled = true;

                fetch(`/get_register/badge/update/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            badge_full_name: document.getElementById('badge_full_name').value,
                            organisation: document.getElementById('organisation').value,
                            role_badge_congres: document.getElementById('role_badge_congres')
                                .value,
                            badge_color_id: document.getElementById('badge_color_id').value,
                            nationality_id: document.getElementById('nationality_id').value,
                            participant_id: document.getElementById('badge_id').value,
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;

                        if (data.success) {
                            $('#editBadgeModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: '{{ app()->getLocale() == 'fr' ? 'Succès' : 'Success' }}',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ app()->getLocale() == 'fr' ? 'Erreur' : 'Error' }}',
                                text: data.message ||
                                    '{{ app()->getLocale() == 'fr' ? 'Erreur lors de la sauvegarde' : 'Error saving' }}'
                            });
                        }
                    })
                    .catch(error => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        Swal.fire({
                            icon: 'error',
                            title: '{{ app()->getLocale() == 'fr' ? 'Erreur' : 'Error' }}',
                            text: '{{ app()->getLocale() == 'fr' ? 'Erreur lors de la sauvegarde' : 'Error saving' }}'
                        });
                    });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const locale = "{{ app()->getLocale() }}";

            // Charger les nationalités
            loadNationalities();



            // Événement de sauvegarde
            $('#saveInviteBtn').click(function() {
                saveInvite();
            });

            // Fermer le modal et réinitialiser le formulaire
            $('#AddInviteModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            // Fonction pour charger les nationalités
            function loadNationalities() {
                $.ajax({
                    url: '{{ route('nationalities.list') }}', // Créez cette route
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        const select = $('#nationalite');
                        select.empty();
                        select.append(
                            '<option value="">{{ app()->getLocale() == 'fr' ? 'Sélectionner' : 'Select' }}</option>'
                        );

                        data.forEach(function(nationality) {
                            const label = locale == 'fr' ? nationality.libelle_fr : nationality
                                .libelle_en;
                            select.append(
                                `<option value="${nationality.id}">${label}</option>`);
                        });
                    },
                    error: function(xhr) {
                        console.error('Erreur chargement nationalités:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: locale == 'fr' ? 'Erreur!' : 'Error!',
                            text: locale == 'fr' ? 'Impossible de charger les nationalités' :
                                'Unable to load nationalities'
                        });
                    }
                });
            }

            // Fonction pour sauvegarder (créer ou modifier)
            function saveInvite() {
                const formData = $('#inviteForm').serialize();
                const inviteId = $('#invite_id').val();
                const isEdit = inviteId !== '';

                // Désactiver le bouton pendant la requête
                const saveBtn = $('#saveInviteBtn');
                const originalText = saveBtn.html();
                saveBtn.prop('disabled', true);
                saveBtn.html('<i class="fa fa-spinner fa-spin"></i> ' +
                    (locale == 'fr' ? 'Enregistrement...' : 'Saving...'));

                // Réinitialiser les erreurs
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: isEdit ? `/invites/${inviteId}` : '{{ route('invites.store') }}',
                    type: isEdit ? 'PUT' : 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Fermer le modal
                            $('#AddInviteModal').modal('hide');

                            // Afficher message de succès
                            Swal.fire({
                                icon: 'success',
                                title: locale == 'fr' ? 'Succès!' : 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Recharger la liste
                            loadInvites();

                            // Réinitialiser le formulaire
                            resetForm();
                        } else {
                            // Afficher les erreurs de validation
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}-error`).text(response.errors[key][0]);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: locale == 'fr' ? 'Erreur!' : 'Error!',
                                    text: response.message
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}-error`).text(errors[key][0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: locale == 'fr' ? 'Erreur!' : 'Error!',
                                text: locale == 'fr' ? 'Une erreur est survenue' :
                                    'An error occurred'
                            });
                        }
                    },
                    complete: function() {
                        // Réactiver le bouton
                        saveBtn.prop('disabled', false);
                        saveBtn.html(originalText);
                    }
                });
            }


            // Fonction pour supprimer un invité
            function deleteInvite(id) {
                Swal.fire({
                    title: locale == 'fr' ? 'Êtes-vous sûr?' : 'Are you sure?',
                    text: locale == 'fr' ? 'Cette action est irréversible!' :
                        'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: locale == 'fr' ? 'Oui, supprimer!' : 'Yes, delete!',
                    cancelButtonText: locale == 'fr' ? 'Annuler' : 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/invites/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: csrfToken
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Supprimer la ligne du tableau
                                    $(`#invite-${id}`).remove();

                                    // Afficher message de succès
                                    Swal.fire({
                                        icon: 'success',
                                        title: locale == 'fr' ? 'Supprimé!' :
                                            'Deleted!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // Si le tableau est vide, afficher un message
                                    if ($('#invitesTableBody tr').length === 0) {

                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: locale == 'fr' ? 'Erreur!' : 'Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: locale == 'fr' ? 'Erreur!' : 'Error!',
                                    text: locale == 'fr' ? 'Impossible de supprimer' :
                                        'Unable to delete'
                                });
                            }
                        });
                    }
                });
            }

            // Fonction pour réinitialiser le formulaire
            function resetForm() {
                $('#inviteForm')[0].reset();
                $('#invite_id').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#inviteModalLabel').text(locale == 'fr' ? 'Ajouter un invité' : 'Add an invite');
            }


            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const name = $(this).data('name') || 'cet élément';

                deleteInvite(id, name);
            });

            function deleteInvite(id, name = 'cet élément') {
                Swal.fire({
                    title: locale == 'fr' ? 'Êtes-vous sûr?' : 'Are you sure?',
                    html: locale == 'fr' ?
                        `Voulez-vous vraiment supprimer <strong>${name}</strong> ?<br>Cette action est irréversible!` :
                        `Do you really want to delete <strong>${name}</strong> ?<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: locale == 'fr' ? 'Oui, supprimer!' : 'Yes, delete!',
                    cancelButtonText: locale == 'fr' ? 'Annuler' : 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Afficher un loader
                        Swal.fire({
                            title: locale == 'fr' ? 'Suppression...' : 'Deleting...',
                            text: locale == 'fr' ? 'Veuillez patienter' : 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/get_register/invites/${id}`, // Ou /badges/${id} selon votre route
                            type: 'DELETE',
                            data: {
                                _token: csrfToken
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: locale == 'fr' ? 'Supprimé!' :
                                            'Deleted!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    // Supprimer la ligne du tableau ou rafraîchir la page
                                    setTimeout(() => {
                                        location.reload(); // Rafraîchir la page
                                        // OU supprimer la ligne spécifique si vous avez un tableau
                                        // $(`tr#invite-${id}`).remove();
                                    }, 1000);

                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: locale == 'fr' ? 'Erreur!' : 'Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: locale == 'fr' ? 'Erreur!' : 'Error!',
                                    text: locale == 'fr' ? 'Impossible de supprimer' :
                                        'Unable to delete'
                                });
                            }
                        });
                    }
                });
            }
        });
    </script>

@endsection
