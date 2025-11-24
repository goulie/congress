@extends('voyager::master')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/AdminLTE.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .recap-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .recap-header {
            background: linear-gradient(135deg, #2c80ff, #1a5fd0);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 25px;
            text-align: center;
        }

        .recap-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .recap-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .section-title {
            color: #2c80ff;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 200px;
        }

        .info-value {
            color: #333;
            text-align: right;
            flex: 1;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-paid {
            background: #d4edda;
            color: #155724;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .invoice-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }

        .total-amount {
            font-size: 32px;
            font-weight: 700;
            color: #2c80ff;
            text-align: center;
            margin: 15px 0;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-print {
            background: #28a745;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
        }

        .btn-download {
            background: #17a2b8;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
        }

        .btn-edit {
            background: #ffc107;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: #212529;
            font-weight: 600;
        }

        .btn-home {
            background: #6c757d;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
        }

        .file-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #2c80ff;
            text-decoration: none;
            font-weight: 500;
        }

        .file-download:hover {
            color: #1a5fd0;
            text-decoration: underline;
        }

        .empty-value {
            color: #999;
            font-style: italic;
        }

        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .confirmation-message {
            text-align: center;
            padding: 30px;
            background: #f8fff9;
            border-radius: 10px;
            margin: 20px 0;
        }
    </style>
@endsection

@section('page_title', __('Registration Summary'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="card recap-card">
                    <div class="recap-header">
                        <h1 class="recap-title">
                            <i class="bi bi-check-circle-fill"></i>
                            {{ __('registration.recap.title') }}
                        </h1>
                        <p class="recap-subtitle">
                            {{ __('registration.recap.subtitle') }}
                        </p>
                    </div>

                    <div class="card-body" style="padding: 30px;">
                        <!-- Message de confirmation -->
                        <div class="confirmation-message">
                            <i class="bi bi-check-circle success-icon"></i>
                            <h3 style="color: #28a745; margin-bottom: 10px;">
                                {{ __('registration.recap.registration_complete') }}
                            </h3>
                            <p class="text-muted">
                                {{ __('registration.recap.confirmation_email_sent') }}
                            </p>
                        </div>

                        <div class="row">
                            <!-- Informations Personnelles -->
                            <div class="col-md-6">
                                <div class="section-title">
                                    <i class="bi bi-person"></i>
                                    {{ __('registration.recap.personal_info') }}
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step1.fields.title') }}:</span>
                                    <span
                                        class="info-value">{{ $participant->civility->libelle ?? __('registration.recap.not_provided') }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step1.fields.full_name') }}:</span>
                                    <span class="info-value">{{ $participant->fname }} {{ $participant->lname }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step1.fields.gender') }}:</span>
                                    <span
                                        class="info-value">{{ $participant->gender->libelle ?? __('registration.recap.not_provided') }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step1.fields.education') }}:</span>
                                    <span
                                        class="info-value">{{ $participant->studentLevel->libelle ?? __('registration.recap.not_provided') }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step1.fields.country') }}:</span>
                                    <span class="info-value">
                                        {{ app()->getLocale() == 'fr'
                                            ? $participant->nationality->libelle_fr ?? __('registration.recap.not_provided')
                                            : $participant->nationality->libelle_en ?? __('registration.recap.not_provided') }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step1.fields.age_range') }}:</span>
                                    <span
                                        class="info-value">{{ $participant->ageRange->libelle ?? __('registration.recap.not_provided') }}</span>
                                </div>
                            </div>

                            <!-- Informations de Contact -->
                            <div class="col-md-6">
                                <div class="section-title">
                                    <i class="bi bi-telephone"></i>
                                    {{ __('registration.recap.contact_info') }}
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step2.fields.email') }}:</span>
                                    <span class="info-value">{{ $participant->email }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step2.fields.telephone') }}:</span>
                                    <span class="info-value">{{ $participant->phone }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step2.fields.organisation') }}:</span>
                                    <span class="info-value">{{ $participant->organisation }}</span>
                                </div>

                                <div class="info-row">
                                    <span
                                        class="info-label">{{ __('registration.step2.fields.type_organisation') }}:</span>
                                    <span class="info-value">
                                        @if ($participant->organisation_type_id == 10)
                                            {{ $participant->organisation_type_other }}
                                        @else
                                            {{ $participant->organisationType->libelle ?? __('registration.recap.not_provided') }}
                                        @endif
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step2.fields.fonction') }}:</span>
                                    <span class="info-value">{{ $participant->job }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">{{ __('registration.step2.fields.job_country') }}:</span>
                                    <span class="info-value">
                                        {{ app()->getLocale() == 'fr'
                                            ? $participant->jobCountry->libelle_fr ?? __('registration.recap.not_provided')
                                            : $participant->jobCountry->libelle_en ?? __('registration.recap.not_provided') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du Congrès -->
                        <div class="row" style="margin-top: 30px;">
                            <div class="col-md-12">
                                <div class="section-title">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ __('registration.recap.congress_info') }}
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-row">
                                            <span class="info-label">{{ __('registration.step3.fields.category') }}:</span>
                                            <span
                                                class="info-value">{{ $participant->participantCategory->libelle ?? __('registration.recap.not_provided') }}</span>
                                        </div>

                                        <div class="info-row">
                                            <span
                                                class="info-label">{{ __('registration.step3.fields.membership') }}:</span>
                                            <span class="info-value">
                                                @if ($participant->membre_aae == 'oui')
                                                    <span
                                                        class="badge-status badge-paid">{{ __('registration.step3.fields.oui') }}</span>
                                                @else
                                                    <span
                                                        class="badge-status badge-pending">{{ __('registration.step3.fields.non') }}</span>
                                                @endif
                                            </span>
                                        </div>

                                        @if ($participant->membre_aae == 'oui' && $participant->membership_code)
                                            <div class="info-row">
                                                <span
                                                    class="info-label">{{ __('registration.step3.fields.membershipcode') }}:</span>
                                                <span class="info-value">{{ $participant->membership_code }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-row">
                                            <span
                                                class="info-label">{{ __('registration.step3.fields.diner_gala') }}:</span>
                                            <span class="info-value">
                                                @if ($participant->diner == 'oui')
                                                    <span
                                                        class="badge-status badge-paid">{{ __('registration.step3.fields.oui') }}</span>
                                                @else
                                                    <span
                                                        class="badge-status badge-pending">{{ __('registration.step3.fields.non') }}</span>
                                                @endif
                                            </span>
                                        </div>

                                        <div class="info-row">
                                            <span
                                                class="info-label">{{ __('registration.step3.fields.visite_technical') }}:</span>
                                            <span class="info-value">
                                                @if ($participant->visite == 'oui')
                                                    <span
                                                        class="badge-status badge-paid">{{ __('registration.step3.fields.oui') }}</span>
                                                @else
                                                    <span
                                                        class="badge-status badge-pending">{{ __('registration.step3.fields.non') }}</span>
                                                @endif
                                            </span>
                                        </div>

                                        @if ($participant->visite == 'oui' && $participant->siteVisite)
                                            <div class="info-row">
                                                <span
                                                    class="info-label">{{ __('registration.step3.fields.choose_visit_site') }}:</span>
                                                <span class="info-value">{{ $participant->siteVisite->libelle }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-row">
                                            <span
                                                class="info-label">{{ __('registration.step3.fields.lettre_invitation') }}:</span>
                                            <span class="info-value">
                                                @if ($participant->invitation_letter == 'oui')
                                                    <span
                                                        class="badge-status badge-paid">{{ __('registration.step3.fields.oui') }}</span>
                                                @else
                                                    <span
                                                        class="badge-status badge-pending">{{ __('registration.step3.fields.non') }}</span>
                                                @endif
                                            </span>
                                        </div>

                                        @if ($participant->participant_category_id == 1 && $participant->pass_deleguate == 'oui')
                                            <div class="info-row">
                                                <span
                                                    class="info-label">{{ __('registration.step3.fields.day_pass') }}:</span>
                                                <span class="info-value">
                                                    @if (!empty($participant->deleguate_day))
                                                        @php
                                                            $days = json_decode($participant->deleguate_day);
                                                            $passDays = \App\Models\JourPassDelegue::whereIn(
                                                                'id',
                                                                $days,
                                                            )->get();
                                                        @endphp
                                                        {{ $passDays->count() }}
                                                        {{ __('registration.recap.days_selected') }}
                                                    @else
                                                        {{ __('registration.step3.fields.oui') }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fichiers Téléchargés -->
                        @if ($participant->passeport_pdf || $participant->student_card || $participant->student_letter)
                            <div class="row" style="margin-top: 30px;">
                                <div class="col-md-12">
                                    <div class="section-title">
                                        <i class="bi bi-files"></i>
                                        {{ __('registration.recap.uploaded_files') }}
                                    </div>

                                    <div class="row">
                                        @if ($participant->passeport_pdf)
                                            <div class="col-md-4">
                                                <div class="info-row">
                                                    <span
                                                        class="info-label">{{ __('registration.step3.fields.photo_passeport') }}:</span>
                                                    <span class="info-value">
                                                        <a href="{{ Voyager::image($participant->passeport_pdf) }}"
                                                            target="_blank" class="file-download">
                                                            <i class="bi bi-download"></i>
                                                            {{ __('registration.recap.download') }}
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($participant->student_card)
                                            <div class="col-md-4">
                                                <div class="info-row">
                                                    <span
                                                        class="info-label">{{ __('registration.step3.fields.student_card') }}:</span>
                                                    <span class="info-value">
                                                        <a href="{{ Voyager::image($participant->student_card) }}"
                                                            target="_blank" class="file-download">
                                                            <i class="bi bi-download"></i>
                                                            {{ __('registration.recap.download') }}
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($participant->student_letter)
                                            <div class="col-md-4">
                                                <div class="info-row">
                                                    <span
                                                        class="info-label">{{ __('registration.step3.fields.attestation_letter') }}:</span>
                                                    <span class="info-value">
                                                        <a href="{{ Voyager::image($participant->student_letter) }}"
                                                            target="_blank" class="file-download">
                                                            <i class="bi bi-download"></i>
                                                            {{ __('registration.recap.download') }}
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Récapitulatif de Facturation -->
                        @if ($invoice)
                            <div class="invoice-summary">
                                <div class="section-title" style="border-bottom: none; text-align: center;">
                                    <i class="bi bi-receipt"></i>
                                    {{ __('registration.recap.invoice_summary') }}
                                </div>

                                <div class="total-amount">
                                    {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
                                </div>

                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <strong>{{ __('registration.recap.invoice_number') }}:</strong><br>
                                        {{ $invoice->invoice_number }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>{{ __('registration.recap.invoice_date') }}:</strong><br>
                                        {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>{{ __('registration.recap.status') }}:</strong><br>
                                        <span
                                            class="badge-status {{ $invoice->status == 'paid' ? 'badge-paid' : 'badge-pending' }}">
                                            {{ $invoice->status }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Détails de la facture -->
                                @if ($invoice->items->count() > 0)
                                    <div style="margin-top: 20px;">
                                        <h5 style="color: #555; margin-bottom: 15px;">
                                            {{ __('registration.recap.invoice_details') }}</h5>
                                        @foreach ($invoice->items as $item)
                                            <div class="info-row">
                                                <span
                                                    class="info-label">{{ app()->getLocale() == 'fr' ? $item->description_fr : $item->description_en }}</span>
                                                <span class="info-value">{{ number_format($item->price, 2) }}
                                                    {{ $invoice->currency }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Boutons d'action -->
                        <div class="action-buttons">
                            <button onclick="window.print()" class="btn-print">
                                <i class="bi bi-printer"></i>
                                {{ __('registration.recap.print') }}
                            </button>

                            @if ($invoice)
                                <a href="{{ route('invoice.download', $invoice->id) }}" class="btn-download">
                                    <i class="bi bi-download"></i>
                                    {{ __('registration.recap.download_invoice') }}
                                </a>
                            @endif
                            
                            @if ($participant->type_participant == 'individual')
                                <a href="{{ route('form.edit.step') }}" class="btn-edit">
                                    <i class="bi bi-pencil"></i>
                                    {{ __('registration.recap.modify') }}
                                </a>
                            @else
                                <a href="{{ route('participant.edit', $participant->uuid) }}" class="btn-edit">
                                    <i class="bi bi-pencil"></i>
                                    {{ __('registration.recap.modify') }}
                                </a>
                               
                            @endif
                            <a href="{{ url('/') }}" class="btn-home">
                                <i class="bi bi-house"></i>
                                {{ __('registration.recap.return_home') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        // Impression de la page
        function printSummary() {
            window.print();
        }

        // Animation de confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const confirmationMessage = document.querySelector('.confirmation-message');
            if (confirmationMessage) {
                confirmationMessage.style.opacity = '0';
                confirmationMessage.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    confirmationMessage.style.transition = 'all 0.5s ease';
                    confirmationMessage.style.opacity = '1';
                    confirmationMessage.style.transform = 'translateY(0)';
                }, 300);
            }
        });
    </script>
@endsection
