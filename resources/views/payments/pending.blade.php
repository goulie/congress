@extends('voyager::master')

@section('css')
    <style>
        body {
            background-color: #fffbeb;
            /* Fond ambre très clair */
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        .pending-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .payment-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.1);
            padding: 40px;
            text-align: center;
            border: 1px solid #fef3c7;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #fef3c7;
            color: #d97706;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            position: relative;
        }

        /* Animation de rotation pour l'icône de chargement */
        .spinner-icon {
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        h1 {
            font-weight: 700;
            color: #92400e;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .lead-text {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .info-box {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }

        .status-timeline {
            margin: 20px 0;
            padding-left: 0;
            list-style: none;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #475569;
        }

        .timeline-item i {
            margin-right: 15px;
            font-size: 18px;
        }

        .timeline-item.active i {
            color: #d97706;
        }

        .timeline-item.completed i {
            color: #059669;
        }

        .btn-refresh-custom {
            background-color: #f59e0b;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
            border: none;
            margin: 10px 5px;
        }

        .btn-refresh-custom:hover {
            background-color: #d97706;
            color: white;
            transform: translateY(-1px);
        }

        .btn-outline-custom {
            background-color: #ffffff;
            color: #374151;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid #d1d5db;
            transition: all 0.2s;
            display: inline-block;
            margin: 10px 5px;
        }

        .btn-outline-custom:hover {
            background-color: #f9fafb;
            color: #111827;
        }

        .note-footer {
            margin-top: 30px;
            font-size: 13px;
            color: #64748b;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .pending-container {
                margin-top: 20px;
            }

            .payment-card {
                padding: 20px;
            }
        }
    </style>
@stop

@section('content')

    <div class="container-fluid">
        <div class="container pending-container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="payment-card">

                        <!-- Icône -->
                        <div class="icon-circle">
                            <i class="bi bi-arrow-repeat spinner-icon"></i>
                        </div>

                        <h1>{{ __('payment_page.payment_pending_title') }}</h1>

                        <p class="lead-text">
                            {!! __('payment_page.payment_pending_text') !!}
                        </p>

                        <!-- Timeline -->
                        <div class="info-box">
                            <ul class="status-timeline">
                                <li class="timeline-item completed">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>{{ __('payment_page.step_registration_received') }}</span>
                                </li>

                                <li class="timeline-item active">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>{{ __('payment_page.step_bank_confirmation') }}</span>
                                </li>

                                <li class="timeline-item" style="opacity: 0.5;">
                                    <i class="bi bi-circle"></i>
                                    <span>{{ __('payment_page.step_badge_generation') }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Actions -->
                        <div class="actions">
                            <a href="/" class="btn-outline-custom">
                                <i class="bi bi-house"></i> {{ __('payment_page.back_home') }}
                            </a>
                        </div>

                        <div class="note-footer">
                            <p>{{ __('payment_page.pending_note') }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
