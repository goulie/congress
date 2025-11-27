@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="{{ $dataType->icon }}"></i> {{ $dataType->getTranslatedAttribute('display_name_plural') }}
        </h1>
    </div>
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
                            <label for="search">Recherche</label>
                            <input type="text" class="form-control" id="search" placeholder="Nom, prénom, email...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <select class="form-control" id="category">
                                <option value="">Toutes</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="country">Pays</label>
                            <select class="form-control" id="country">
                                <option value="">Tous</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select class="form-control" id="status">
                                <option value="">Tous</option>
                                <option value="paid">Payé</option>
                                <option value="pending">En attente</option>
                                <option value="cancelled">Annulé</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-primary" id="apply-filters">
                                    <i class="voyager-search"></i> Appliquer
                                </button>
                                <button type="button" class="btn btn-default" id="reset-filters">
                                    <i class="voyager-refresh"></i> Réinitialiser
                                </button>
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
                        <span class="info-box-text">Total Participants</span>
                        <span class="info-box-number">{{ $totalParticipants }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green">
                        <i class="voyager-check"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Inscriptions payées</span>
                        <span class="info-box-number">{{ $paidParticipants }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow">
                        <i class="voyager-bubble"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Conférenciers</span>
                        <span class="info-box-number">{{ $speakersCount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-red">
                        <i class="voyager-world"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pays représentés</span>
                        <span class="info-box-number">{{ $countriesCount }}</span>
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
                                <th>
                                    <input type="checkbox" class="select_all">
                                </th>
                                <th>ID</th>
                                <th>Nom & Prénom</th>
                                <th>Email</th>
                                <th>Organisation</th>
                                <th>Pays</th>
                                <th>Catégorie</th>
                                <th>Statut</th>
                                <th>Date d'inscription</th>
                                <th class="actions text-right">{{ __('voyager::generic.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $participant)
                            <tr>
                                <td>
                                    <input type="checkbox" name="row_id" id="checkbox_{{ $participant->id }}" value="{{ $participant->id }}">
                                </td>
                                <td>{{ $participant->id }}</td>
                                <td>
                                    <div class="participant-info">
                                        <strong>{{ $participant->lname }} {{ $participant->fname }}</strong>
                                        @if($participant->civility)
                                            <small class="text-muted">({{ $participant->civility->name }})</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $participant->email }}</td>
                                <td>
                                    @if($participant->organisation)
                                        {{ $participant->organisation }}
                                        @if($participant->organisation_type)
                                            <br><small class="text-muted">{{ $participant->organisation_type->name }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($participant->country)
                                        <span class="flag-icon flag-icon-{{ strtolower($participant->country->code) }}"></span>
                                        {{ $participant->country->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($participant->participantCategory)
                                        <span class="label label-info">{{ $participant->participantCategory->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($participant->status == 'paid')
                                        <span class="label label-success">Payé</span>
                                    @elseif($participant->status == 'pending')
                                        <span class="label label-warning">En attente</span>
                                    @else
                                        <span class="label label-danger">Annulé</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $participant->created_at->format('d/m/Y') }}
                                </td>
                                <td class="no-sort no-click bread-actions">
                                    @can('delete', $participant)
                                        <a href="javascript:;" title="Delete" class="btn btn-sm btn-danger delete" data-id="{{ $participant->id }}" id="delete-{{ $participant->id }}">
                                            <i class="voyager-trash"></i>
                                        </a>
                                    @endcan
                                    @can('edit', $participant)
                                        <a href="{{ route('voyager.participants.edit', $participant->id) }}" title="Edit" class="btn btn-sm btn-primary edit">
                                            <i class="voyager-edit"></i>
                                        </a>
                                    @endcan
                                    @can('read', $participant)
                                        <a href="{{ route('voyager.participants.show', $participant->id) }}" title="View" class="btn btn-sm btn-warning view">
                                            <i class="voyager-eye"></i>
                                        </a>
                                    @endcan
                                    <a href="{{ route('participants.badge', $participant->id) }}" title="Badge" class="btn btn-sm btn-info" target="_blank">
                                        <i class="voyager-ticket"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($participants->isEmpty())
                    <div class="text-center" style="padding: 50px;">
                        <i class="voyager-people" style="font-size: 48px; color: #ccc;"></i>
                        <h4 style="color: #999;">Aucun participant trouvé</h4>
                        <p>Commencez par ajouter des participants à votre congrès.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Affichage de {{ $participants->firstItem() }} à {{ $participants->lastItem() }} sur {{ $participants->total() }} participants
            </div>
            <div>
                {{ $participants->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <style>
        .info-box {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 5px;
            background: #fff;
            margin-bottom: 20px;
        }
        .info-box-icon {
            border-radius: 5px 0 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            font-size: 24px;
        }
        .info-box-content {
            padding: 10px;
        }
        .participant-info {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .table-responsive {
            border-radius: 5px;
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
        .panel-body {
            padding: 20px;
        }
        .btn-sm {
            padding: 4px 8px;
            margin: 0 2px;
        }
        .text-muted {
            color: #6c757d !important;
        }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function () {
            // Filtrage des participants
            $('#apply-filters').click(function() {
                applyFilters();
            });

            $('#reset-filters').click(function() {
                $('#search').val('');
                $('#category').val('');
                $('#country').val('');
                $('#status').val('');
                applyFilters();
            });

            function applyFilters() {
                const search = $('#search').val();
                const category = $('#category').val();
                const country = $('#country').val();
                const status = $('#status').val();

                // Ici, vous pouvez implémenter le filtrage via AJAX
                // ou rediriger vers une URL avec les paramètres de filtre
                let url = new URL(window.location.href);
                let params = new URLSearchParams(url.search);
                
                if (search) params.set('search', search);
                else params.delete('search');
                
                if (category) params.set('category', category);
                else params.delete('category');
                
                if (country) params.set('country', country);
                else params.delete('country');
                
                if (status) params.set('status', status);
                else params.delete('status');

                window.location.href = url.pathname + '?' + params.toString();
            }

            // Recherche en temps réel
            $('#search').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });

            // Sélection multiple
            $('.select_all').on('click', function(e) {
                $('input[name="row_id"]').prop('checked', $(this).prop('checked'));
            });
        });
    </script>
@stop