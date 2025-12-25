@extends('voyager::master')

@section('page_title', app()->getLocale() == 'fr' ? 'Factures' : 'Invoices')




@section('content')

    <div class="container-fluid">
        <form id="exportForm" method="POST" class="filter-bar form-inline"
            action="{{ route('participants.invoices.export') }}">
            @csrf

            <!-- Champs cachés pour stocker les IDs sélectionnés -->
            <input type="hidden" name="selected_ids" id="selectedIds" value="">

            <div class="form-group">
                <label>{{ app()->getLocale() == 'fr' ? 'Email' : 'Email' }}</label>
                <input type="email" name="email" class="form-control"
                    placeholder="{{ app()->getLocale() == 'fr' ? 'email@exemple.com' : 'email@example.com' }}" required>
            </div>

            <div class="form-group" style="margin-left:10px;">
                <label>{{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}</label>
                <input type="text" name="organization" class="form-control"
                    placeholder="{{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}"
                    style="text-transform: uppercase" required>
            </div>

            <div class="form-group" style="margin-left:10px;">
                <label>{{ app()->getLocale() == 'fr' ? 'Adresse' : 'Address' }}</label>
                <input type="text" name="Adresse" class="form-control"
                    placeholder="{{ app()->getLocale() == 'fr' ? 'Adresse' : 'Address' }}"
                    >
            </div>

            <button type="submit" class="btn btn-primary" style="margin-left:10px;" id="exportButton" target="_blank">
                <i class="glyphicon glyphicon-download-alt"></i>
                {{ app()->getLocale() == 'fr' ? 'Exporter les factures sélectionnées' : 'Export selected invoices' }}
            </button>

            <!-- Afficher le nombre de factures sélectionnées -->
            <span id="selectedCount" style="margin-left: 15px; color: #666; font-size: 14px;">
                0 {{ app()->getLocale() == 'fr' ? 'facture(s) sélectionnée(s)' : 'invoice(s) selected' }}
            </span>



            <!-- Tableau -->
            <div class="table-container">
                <table id="invoiceTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>{{ app()->getLocale() == 'fr' ? 'Facture N°' : 'Invoice #' }}</th>
                            <th>{{ app()->getLocale() == 'fr' ? 'Nom complet' : 'Full name' }}</th>
                            <th>Email</th>
                            <th>{{ app()->getLocale() == 'fr' ? 'Organisation' : 'Organization' }}</th>
                            <th>{{ app()->getLocale() == 'fr' ? 'Montant' : 'Amount' }}</th>
                            <th>{{ app()->getLocale() == 'fr' ? 'Statut' : 'Status' }}</th>
                            <th>{{ app()->getLocale() == 'fr' ? 'Action' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            @if ($invoice->participant->email)
                                <tr>
                                    <td>
                                        @if ($invoice->participant->participant_category_id == 4 && $invoice->participant->isYwpOrStudent == false)
                                            <input type="checkbox" disabled>
                                        @else
                                            <input type="checkbox" name="participant_ids[]"
                                                value="{{ $invoice->participant_id }}" class="invoice-checkbox">
                                        @endif
                                    </td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->participant->lname . ' ' . $invoice->participant->fname }}</td>
                                    <td>{{ $invoice->participant->email }}</td>
                                    <td>{{ $invoice->participant->sigle_organisation ?? $invoice->participant->organisation }}
                                    </td>
                                    <td>{{ $invoice->total_amount }}</td>
                                    <td>
                                        @if ($invoice->status === App\Models\Invoice::PAYMENT_STATUS_PAID)
                                            <span class="label label-success">
                                                {{ app()->getLocale() == 'fr' ? 'Payé' : 'Paid' }}
                                            </span>
                                        @else
                                            <span class="label label-danger">
                                                {{ app()->getLocale() == 'fr' ? 'Non payé' : 'Unpaid' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->participant->participant_category_id == 4 && $invoice->participant->isYwpOrStudent == false)
                                            <i class="glyphicon glyphicon-lock"
                                                title="{{ app()->getLocale() == 'fr' ? 'Ce participant doit être approuvé' : 'This participant must be approved' }}"></i>
                                        @else
                                            <a href="{{ route('invoices.download.participant', $invoice->participant_id) }}"
                                                class="btn btn-success btn-xs">
                                                <i class="glyphicon glyphicon-download"></i>
                                                {{ app()->getLocale() == 'fr' ? 'Télécharger' : 'Download' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    {{ app()->getLocale() == 'fr' ? 'Aucune facture disponible' : 'No invoices available' }}
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>

        </form>

    </div>

@stop

@section('css')
    <style>
        .page-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .filter-bar {
            background: #fff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .table-container {
            background: #fff;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .badge-paid {
            background: #5cb85c;
        }

        .badge-unpaid {
            background: #d9534f;
        }
    </style>
@stop

@section('javascript')
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#invoiceTable').DataTable({
                pageLength: 10,
                columnDefs: [{
                    orderable: false,
                    targets: [0, 7]
                }]
            });

            // Sélectionner/désélectionner tout
            $('#selectAll').on('change', function() {
                const checked = this.checked;
                $('.invoice-checkbox').each(function() {
                    this.checked = checked;
                });
                updateSelectedCount();
            });

            // Mettre à jour quand une checkbox change
            $(document).on('change', '.invoice-checkbox', function() {
                if (!this.checked) {
                    $('#selectAll').prop('checked', false);
                }
                updateSelectedCount();
            });

            // Avant soumission du formulaire d'export
            $('#exportForm').on('submit', function(e) {
                const selectedIds = getSelectedIds();

                if (selectedIds.length === 0) {
                    e.preventDefault();
                    alert(
                        '{{ app()->getLocale() == 'fr' ? 'Veuillez sélectionner au moins une facture à exporter' : 'Please select at least one invoice to export' }}'
                    );
                    return false;
                }

                // Mettre les IDs sélectionnés dans le champ caché
                $('#selectedIds').val(selectedIds.join(','));

                // Optionnel: Afficher un message de chargement
                $('#exportButton').html(
                        '<i class="glyphicon glyphicon-hourglass"></i> {{ app()->getLocale() == 'fr' ? 'Génération en cours...' : 'Generating...' }}'
                    )
                    .prop('disabled', true);
            });

            // Fonction pour obtenir les IDs sélectionnés
            function getSelectedIds() {
                const selectedIds = [];
                $('.invoice-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });
                return selectedIds;
            }

            // Fonction pour mettre à jour le compteur
            function updateSelectedCount() {
                const count = $('.invoice-checkbox:checked').length;
                $('#selectedCount').html(
                    count +
                    ' {{ app()->getLocale() == 'fr' ? 'facture(s) sélectionnée(s)' : 'invoice(s) selected' }}'
                );

                // Activer/désactiver le bouton d'export
                if (count > 0) {
                    $('#exportButton').prop('disabled', false);
                }
            }

            // Initialiser le compteur
            updateSelectedCount();
        });
    </script>
@stop
