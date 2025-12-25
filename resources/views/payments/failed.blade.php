@extends('voyager::master')

@section('css')
    <style>
        body {
            background-color: #fef2f2;
            /* Fond très légèrement rosé */
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        .failure-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .payment-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(185, 28, 28, 0.1);
            padding: 40px;
            text-align: center;
            border: 1px solid #fee2e2;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #fee2e2;
            color: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        h1 {
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .lead-text {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .error-details {
            background-color: #fffaf0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
            border: 1px solid #ffedd5;
        }

        .error-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .error-item i {
            color: #d97706;
            margin-right: 12px;
            font-size: 18px;
            margin-top: 2px;
        }

        .error-text {
            color: #92400e;
            font-size: 14px;
            line-height: 1.5;
        }

        .btn-retry-custom {
            background-color: #dc2626;
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

        .btn-retry-custom:hover {
            background-color: #b91c1c;
            color: white;
            transform: translateY(-1px);
        }

        .btn-support-custom {
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

        .btn-support-custom:hover {
            background-color: #f9fafb;
            color: #111827;
        }

        .help-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            font-size: 13px;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .failure-container {
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
        <div class="container failure-container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="payment-card">

                        <!-- Icône -->
                        <div class="icon-circle">
                            <i class="bi bi-x-lg"></i>
                        </div>

                        <h1>{{ __('payment_page.payment_failed_title') }}</h1>

                        <p class="lead-text">
                            {!! __('payment_page.payment_failed_text') !!}
                        </p>

                        <!-- Détails erreur -->
                        <div class="error-details">
                            <div class="error-item">
                                <i class="bi bi-exclamation-triangle"></i>
                                <div class="error-text">
                                    <strong>{{ __('payment_page.error_reason') }}</strong>
                                    {{ __('payment_page.error_reason_text') }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="actions">
                            <a href="mailto:event@afwasa.org" class="btn-support-custom">
                                <i class="bi bi-headset"></i>
                                {{ __('payment_page.contact_support') }}
                            </a>
                        </div>

                        <div style="margin-top: 20px;">
                            <a href="/" class="btn btn-link" style="color: #6b7280; text-decoration: none;">
                                <i class="bi bi-arrow-left"></i>
                                {{ __('payment_page.return_congress_site') }}
                            </a>
                        </div>

                        <div class="help-info">
                            <p><strong>{{ __('payment_page.need_help') }}</strong></p>
                            <p>{{ __('payment_page.help_text') }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
