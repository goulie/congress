@extends('voyager::master')

@section('css')
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        .success-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .payment-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            text-align: center;
            border: none;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #d1fae5;
            color: #059669;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        h1 {
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .lead-text {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .receipt-box {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px dashed #e5e7eb;
            padding-bottom: 8px;
        }

        .receipt-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
            font-weight: bold;
            color: #111827;
        }

        .label {
            color: #6b7280;
            font-weight: normal;
            font-size: 14px;
        }

        .value {
            color: #111827;
            font-weight: 600;
            font-size: 14px;
        }

        .btn-primary-custom {
            background-color: #2563eb;
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

        .btn-primary-custom:hover {
            background-color: #1d4ed8;
            color: white;
            transform: translateY(-1px);
        }

        .btn-secondary-custom {
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

        .btn-secondary-custom:hover {
            background-color: #f3f4f6;
            color: #111827;
        }

        .footer-info {
            margin-top: 30px;
            font-size: 13px;
            color: #9ca3af;
        }

        .badge-success-custom {
            background-color: #d1fae5;
            color: #065f46;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Flex helpers for BS3 */
        .flex-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 768px) {
            .success-container {
                margin-top: 20px;
            }

            .payment-card {
                padding: 20px;
            }
        }
    </style>
@stop

@section('content')
    <div class="container success-container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="payment-card">

                    <!-- Icône -->
                    <div class="icon-circle">
                        <i class="bi bi-check-lg"></i>
                    </div>

                    <h1>{{ __('payment_page.payment_success_title') }}</h1>

                    <p class="lead-text">
                        {!! __('payment_page.payment_success_text') !!}
                    </p>

                    <div style="margin-top: 20px;">
                        <a href="/get_register/admin" class="btn btn-link" style="color: #6b7280; text-decoration: none;">
                            <i class="bi bi-arrow-left"></i>
                            {{ __('payment_page.back_congress_site') }}
                        </a>
                    </div>

                    <div class="footer-info">
                        {{ __('payment_page.success_footer') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

@stop
