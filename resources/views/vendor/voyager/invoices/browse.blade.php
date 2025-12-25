@extends('voyager::master')

@section('page_title', app()->getLocale() == 'fr' ? 'Factures' : 'Invoices')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ app()->getLocale() == 'fr' ? 'Facturations' : 'Invoices' }}
        </h1>
        @can('add', app($dataType->model_name))
            <a href="{{ route('voyager.' . $dataType->slug . '.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>{{ __('voyager::generic.add_new') }}</span>
            </a>
        @endcan
        @can('delete', app($dataType->model_name))
            @include('voyager::partials.bulk-delete')
        @endcan
        @can('edit', app($dataType->model_name))
            @if (!empty($dataType->order_column) && !empty($dataType->order_display_column))
                <a href="{{ route('voyager.' . $dataType->slug . '.order') }}" class="btn btn-primary btn-add-new">
                    <i class="voyager-list"></i> <span>{{ __('voyager::bread.order') }}</span>
                </a>
            @endif
        @endcan
        @can('delete', app($dataType->model_name))
            @if ($usesSoftDeletes)
                <input type="checkbox" @if ($showSoftDeleted) checked @endif id="show_soft_deletes"
                    data-toggle="toggle" data-on="{{ __('voyager::bread.soft_deletes_off') }}"
                    data-off="{{ __('voyager::bread.soft_deletes_on') }}">
            @endif
        @endcan
        @foreach ($actions as $action)
            @if (method_exists($action, 'massAction'))
                @include('voyager::bread.partials.actions', ['action' => $action, 'data' => null])
            @endif
        @endforeach
        @include('voyager::multilingual.language-selector')
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @if ($isServerSide)
                            <form method="get" class="form-search">
                                <div id="search-input">
                                    <div class="col-2">
                                        <select id="search_key" name="key">
                                            @foreach ($searchNames as $key => $name)
                                                <option value="{{ $key }}"
                                                    @if ($search->key == $key || (empty($search->key) && $key == $defaultSearchKey)) selected @endif>{{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <select id="filter" name="filter">
                                            <option value="contains" @if ($search->filter == 'contains') selected @endif>
                                                {{ __('voyager::generic.contains') }}</option>
                                            <option value="equals" @if ($search->filter == 'equals') selected @endif>=
                                            </option>
                                        </select>
                                    </div>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('voyager::generic.search') }}" name="s"
                                            value="{{ $search->value }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-lg" type="submit">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                @if (Request::has('sort_order') && Request::has('order_by'))
                                    <input type="hidden" name="sort_order" value="{{ Request::get('sort_order') }}">
                                    <input type="hidden" name="order_by" value="{{ Request::get('order_by') }}">
                                @endif
                            </form>
                        @endif
                        <form method="POST" action="{{ route('participants.invoices.export') }}" target="_blank">
                            @csrf
                            <div class="panel panel-default panel-custom" style="padding-top: 50px">
                                <div class="panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h4 class="pull-left">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            {{ app()->getLocale() == 'fr' ? 'Liste des Factures' : 'Invoices List' }}
                                        </h4>
                                        <div class="pull-right">
                                            {{-- <form id="paymentForm" action="{{ route('payment.pay') }}" method="POST"
                                                style="display: inline-block; width: 100%;">
                                                @csrf
                                                <input type="hidden" name="uuid"
                                                    value="{{ $participant->uuid ?? '' }}">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="glyphicon glyphicon-credit-card"></i>
                                                        {{ app()->getLocale() == 'fr' ? 'Payer la facture' : 'Pay Invoice' }}
                                                    </button>
                                            </form> --}}
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="input-group">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default" type="button">Email</button>
                                                        </span>
                                                        <input type="text" class="form-control"
                                                            style="text-transform: lowercase" name="email"
                                                            placeholder="Email de facturation">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="input-group">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-default" type="button">Organistation</button>
                                                        </span>
                                                        <input type="text" class="form-control" style="text-transform: uppercase" name="organization"
                                                            placeholder="Organisation à mettre sur la facture">
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="input-group">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="glyphicon glyphicon-download-alt"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Télécharger les factures groupées' : 'Download Grouped Invoices' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="dataTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>

                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Facture N°' : 'Invoice N°' }}
                                            </th>
                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Date' : 'Date' }}
                                            </th>
                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Nom complet' : 'Full Name' }}
                                            </th>
                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Email' : 'Email' }}
                                            </th>
                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Montant' : 'Amount' }}
                                            </th>
                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Statut' : 'Status' }}
                                            </th>
                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Moyen de paiement' : 'Payment Method' }}
                                            </th>
                                            <th>
                                                {{ app()->getLocale() == 'fr' ? 'Date de paiement' : 'Payment Date' }}
                                            </th>
                                            <th class="actions text-right dt-not-orderable">
                                                {{ __('voyager::generic.actions') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse ($dataTypeContent->where('total_amount', '>', 0) as $data)
                                            @if (
                                                $data->participant->participant_category_id == 4 &&
                                                    !auth()->user()->isAdmin() &&
                                                    $data->participant->isYwpOrStudent == false)
                                                <tr>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" disabled>

                                                    </td>
                                                    <td>
                                                        {{ app()->getLocale() == 'fr' ? $data->invoice_number : $data->invoice_number }}
                                                    </td>
                                                    <td>
                                                        {{ app()->getLocale() == 'fr' ? $data->invoice_date : $data->invoice_date }}
                                                    </td>
                                                    <td>
                                                        {{ $data->participant->lname . ' ' . $data->participant->fname }}
                                                    </td>
                                                    <td>
                                                        {{ $data->participant->email }}
                                                    </td>

                                                    <td colspan="5" class="text-center text-danger">
                                                        <div class="alert alert-danger">
                                                            <i class="bi bi-lock-fill"></i>
                                                            {{ app()->getLocale() == 'fr'
                                                                ? 'Cette facture sera accessible une fois votre inscription validée. Vous recevrez une notification par e-mail dès que votre dossier aura été accepté.'
                                                                : 'This invoice will be accessible once your registration has been approved. You will receive an email notification as soon as your application is accepted.' }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="participant_ids[]"
                                                            value="{{ $data->participant_id }}">

                                                    </td>
                                                    <td>
                                                        {{ app()->getLocale() == 'fr' ? $data->invoice_number : $data->invoice_number }}
                                                    </td>
                                                    <td>
                                                        {{ app()->getLocale() == 'fr' ? $data->invoice_date : $data->invoice_date }}
                                                    </td>
                                                    <td>
                                                        {{ $data->participant->lname . ' ' . $data->participant->fname }}
                                                    </td>
                                                    <td>
                                                        {{ $data->participant->email }}
                                                    </td>
                                                    <td>
                                                        <span style="text-weight:bold !important">
                                                            {{ $data->total_amount . ' ' . ($data->currency === 'USD' ? '$' : ($data->currency === 'EUR' ? '€' : $data->currency)) }}
                                                        </span>
                                                    </td>
                                                    @php
                                                        // Traductions des statuts
                                                        $translations = [
                                                            'Paid' => ['fr' => 'Payé', 'en' => 'Paid'],
                                                            'Unpaid' => ['fr' => 'Non payé', 'en' => 'Unpaid'],
                                                        ];

                                                        // Sélection du statut original
                                                        $status = $data->status;

                                                        // Récupération de la traduction selon la locale
                                                        $translatedStatus =
                                                            $translations[$status][app()->getLocale()] ?? $status;

                                                        // Détermination du badge
                                                        switch ($status) {
                                                            case 'Paid':
                                                                $badgeClass = 'badge-success';
                                                                break;

                                                            case 'Unpaid':
                                                                $badgeClass = 'badge-danger';
                                                                break;

                                                            default:
                                                                $badgeClass = 'badge-secondary';
                                                                break;
                                                        }
                                                    @endphp

                                                    <td>
                                                        <span class="badge {{ $badgeClass }}">
                                                            {{ $translatedStatus }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        {{ $data->payment_method ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $data->payment_date ?? 'N/A' }}
                                                    </td>

                                                    <td class="no-sort no-click bread-actions">
                                                        @foreach ($actions as $action)
                                                            @if (!method_exists($action, 'massAction'))
                                                                @include(
                                                                    'voyager::bread.partials.actions',
                                                                    [
                                                                        'action' => $action,
                                                                    ]
                                                                )
                                                            @endif
                                                        @endforeach
                                                        <a href="{{ route('invoices.download.participant', $data->participant_id) }}"
                                                            class="btn btn-xs btn-success"> <i class="voyager-download"></i>
                                                            {{ app()->getLocale() == 'fr' ? 'Télécharger la facture' : 'Download the invoice' }}
                                                        </a>
                                                        @if (!$data->status == 'Paid')
                                                            {{-- <form class="paymentForm" action="{{ route('payment.pay') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="uuid"
                                                                    value="{{ $data->participant->uuid ?? '' }}">
                                                                <button type="submit" class="btn btn-xs btn-info">
                                                                    <i class="bi bi-wallet2"></i>
                                                                    {{ app()->getLocale() == 'fr' ? 'Payer la facture' : 'Pay the invoice' }}
                                                                </button>
                                                            </form> --}}
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endif

                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">
                                                    {{ __('voyager::generic.no_results') }}
                                                </td>
                                            </tr>

                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </form>
                        @if ($isServerSide)
                            <div class="pull-left">
                                <div role="status" class="show-res" aria-live="polite">
                                    {{ trans_choice('voyager::generic.showing_entries', $dataTypeContent->total(), [
                                        'from' => $dataTypeContent->firstItem(),
                                        'to' => $dataTypeContent->lastItem(),
                                        'all' => $dataTypeContent->total(),
                                    ]) }}
                                </div>
                            </div>
                            <div class="pull-right">
                                {{ $dataTypeContent->appends([
                                        's' => $search->value,
                                        'filter' => $search->filter,
                                        'key' => $search->key,
                                        'order_by' => $orderBy,
                                        'sort_order' => $sortOrder,
                                        'showSoftDeleted' => $showSoftDeleted,
                                    ])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }}
                        {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                            value="{{ __('voyager::generic.delete_confirm') }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right"
                        data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('css')
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <link rel="stylesheet" href="{{ voyager_asset('lib/css/responsive.dataTables.min.css') }}">
    @endif
@stop

@section('javascript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.paymentForm').forEach(form => {

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const url = form.action;
                    const formData = new FormData(form);

                    Swal.fire({
                        title: '{{ app()->getLocale() == 'fr' ? 'Traitement du paiement...' : 'Processing payment...' }}',
                        text: '{{ app()->getLocale() == 'fr' ? 'Veuillez patienter, ne fermez pas cette page.' : 'Please wait, do not close this page.' }}',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')
                                    .value,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(async response => {
                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw data;
                            }

                            // ✅ Redirection vers la page de paiement DBS
                            if (data.payment_url) {
                                window.location.href = data.payment_url;
                            } else {
                                throw {
                                    message: '{{ app()->getLocale() == 'fr' ? 'URL de paiement introuvable' : 'Payment URL not found' }}'
                                };
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ app()->getLocale() == 'fr' ? 'Erreur de paiement' : 'Payment error' }}',
                                text: error.message ||
                                    '{{ app()->getLocale() == 'fr' ? 'Une erreur est survenue' : 'An error occurred' }}'
                            });
                        });
                });

            });

        });
    </script>


    <!-- DataTables -->
    @if (!$dataType->server_side && config('dashboard.data_tables.responsive'))
        <script src="{{ voyager_asset('lib/js/dataTables.responsive.min.js') }}"></script>
    @endif
    <script>
        $(document).ready(function() {
            @if (!$dataType->server_side)
                var table = $('#dataTable').DataTable({!! json_encode(
                    array_merge(
                        [
                            'order' => $orderColumn,
                            'language' => __('voyager::datatable'),
                            'columnDefs' => [['targets' => 'dt-not-orderable', 'searchable' => false, 'orderable' => false]],
                        ],
                        config('voyager.dashboard.data_tables', []),
                    ),
                    true,
                ) !!});
            @else
                $('#search-input select').select2({
                    minimumResultsForSearch: Infinity
                });
            @endif

            @if ($isModelTranslatable)
                $('.side-body').multilingual();
                //Reinitialise the multilingual features when they change tab
                $('#dataTable').on('draw.dt', function() {
                    $('.side-body').data('multilingual').init();
                })
            @endif

            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked')).trigger('change');
            });
        });


        var deleteFormAction;
        $('td').on('click', '.delete', function(e) {
            $('#delete_form')[0].action = '{{ route('voyager.' . $dataType->slug . '.destroy', '__id') }}'.replace(
                '__id', $(this).data('id'));
            $('#delete_modal').modal('show');
        });

        @if ($usesSoftDeletes)
            @php
                $params = [
                    's' => $search->value,
                    'filter' => $search->filter,
                    'key' => $search->key,
                    'order_by' => $orderBy,
                    'sort_order' => $sortOrder,
                ];
            @endphp
            $(function() {
                $('#show_soft_deletes').change(function() {
                    if ($(this).prop('checked')) {
                        $('#dataTable').before(
                            '<a id="redir" href="{{ route('voyager.' . $dataType->slug . '.index', array_merge($params, ['showSoftDeleted' => 1]), true) }}"></a>'
                        );
                    } else {
                        $('#dataTable').before(
                            '<a id="redir" href="{{ route('voyager.' . $dataType->slug . '.index', array_merge($params, ['showSoftDeleted' => 0]), true) }}"></a>'
                        );
                    }

                    $('#redir')[0].click();
                })
            })
        @endif
        $('input[name="row_id"]').on('change', function() {
            var ids = [];
            $('input[name="row_id"]').each(function() {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            $('.selected_ids').val(ids);
        });
    </script>

    <script>
        document.getElementById('selectAll').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('input[name="participant_ids[]"]');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });
    </script>
@stop
