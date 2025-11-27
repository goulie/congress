<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture AfWASA - Congress</title>
    <style>
        /* Styles de base */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.2;
            /* color: #333; */
            margin: 0;
            padding: 0;
        }

        .float-container {
            border: 3px solid #fff;
            padding: 20px;
        }

        .float-child {
            width: 50%;
            float: left;
            padding: 20px;
            border: 2px solid red;
        }

        .invoice-container {
            max-width: 800px;
            margin-top: -10px !important;
            padding-top: 0px !important;
        }

        /* En-tête avec logo pleine largeur */
        .header {
            margin-bottom: 10px;
            /*  border-bottom: 2px solid #2c3e50; */
            padding-bottom: 10px;
        }


        /* .logo-container {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px 0;
            /*  background-color: #f8f9fa; *
            border-radius: 5px;
        } */

        .logo {
            font-size: 32px;
            font-weight: bold;
            /* color: #2c3e50; */
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-info {
            display: flex;

            gap: 1px;
        }


        .bloc {
            display: inline-block;
            width: 300px;
            padding: 20px;
            margin: 5px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .invoice-date {
            font-size: 14px;
        }

        .paid {
            display: inline-block;
            padding: 8px 15px;
            background-color: #27ae60;
            color: white;
            border-radius: 3px;
            font-weight: bold;
            margin-top: 10px;
            font-size: 14px;
        }

        .unpaid {
            display: inline-block;
            padding: 8px 15px;
            background-color: #ff0000;
            color: white;
            border-radius: 3px;
            font-weight: bold;
            margin-top: 10px;
            font-size: 14px;
        }

        /* Tableau des articles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #2c3e50;
        }

        .summary-section {
            background-color: #f8f9fa;
            padding: 5px 0;
        }

        /* Section paiement */
        .payment-info {
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #2c3e50;
        }

        .payment-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
            color: #2c3e50;
        }

        .bank-info {
            line-height: 1.3;
        }

        /* Pied de page */
        .footer {
            margin-top: 0px;
            text-align: center;
            font-size: 12px;
            color: #777;
            padding-top: 0px;
            border-top: 1px solid #ddd;
        }

        /* Pour l'impression */
        @media print {
            body {
                padding: 0;
            }

            .invoice-container {
                padding: 0;
            }

            /* .logo-container {
                background-color: transparent !important;
            } */
        }




        .logo_afwasa img,
        .logo_host img {
            width: 200px;
            height: auto;
        }

        .congres-text {
            flex: 1;
            text-align: center;
            padding: 0 20px;
            font-family: Arial, sans-serif;
            font-size: 18px;
            color: #2304a1;
        }
    </style>
</head>

<body>
    @php
        /* if (app()->getLocale() == 'fr') {
            \Carbon\Carbon::setLocale('fr');
        } */
        $congres = App\Models\Congress::latest()->first();
        $transfert_info = App\Models\CongressBankTransfer::where('congres_id', $congres->id)->first();

        $periode = App\Models\Periode::PeriodeActive(App\Models\Congress::latest()->first()->id);
        $locale = $invoice->participant->langue; // app()->getLocale(); // 'fr' or 'en'
        \Carbon\Carbon::setLocale($locale);
        $start = \Carbon\Carbon::parse($periode->start_date);
        $end = \Carbon\Carbon::parse($periode->end_date);
        $daysRemaining = $periode->joursRestants();

        $dateFormattedStart = $locale === 'fr' ? $start->translatedFormat('d F Y') : $start->translatedFormat('F d, Y');
        $dateFormattedEnd = $locale === 'fr' ? $end->translatedFormat('d F Y') : $end->translatedFormat('F d, Y');

    @endphp
    <div class="invoice-container">
        <!-- En-tête avec logo pleine largeur -->
        <div class="header">
            <div class="logo-container">

                <img src="{{ '/public/storage/' . $invoice->congres->banniere }}" style="width:100%">

                <div class="congres-text">
                    @if ($locale == 'fr')
                        <h5>CONGRÈS INTERNATIONAL ET EXPOSITION DE L'AAEA <br />
                            DU {{ \Carbon\Carbon::parse($invoice->congres->begin_date)->format('d') }} au
                            {{ \Carbon\Carbon::parse($invoice->congres->end_date)->isoFormat('D MMMM YYYY') }} -
                            {{ $invoice->congres->hostCountry->libelle_fr ?? '' }}
                        </h5>
                    @else
                        <h5>AfWASA INTERNATIONAL CONGRESS AND EXHIBITION <br />
                            FROM {{ \Carbon\Carbon::parse($invoice->congres->begin_date)->format('F d') }} to
                            {{ \Carbon\Carbon::parse($invoice->congres->end_date)->format('F d, Y') }} -
                            {{ $invoice->congres->hostCountry->libelle_en ?? '' }}
                        </h5>
                    @endif
                    {{-- <p>{!! $invoice->congres->translate(app()->getLocale(), 'fallbackLocale')->theme !!}</p> --}}
                </div>

            </div>


            @php
                use Carbon\Carbon;
                $date = $invoice->payment_date ?? $invoice->created_at;
                $dateFr = Carbon::parse($date)->locale('fr')->isoFormat('D MMMM YYYY');
                $dateEn = Carbon::parse($date)->locale('en')->isoFormat('MMMM D, YYYY');

            @endphp

            <div class="invoice-info">
                <div class="bloc" style="float: left !important; width: 300px !important;">
                    <div class="invoice-title">{{ __('facture.facture') }}</div>
                    <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                    <div class="invoice-date">
                        {{ __('Date') }} :
                        {{ $locale == 'fr' ? $dateFr : $dateEn }}
                    </div>

                    @if ($invoice->status === App\Models\Invoice::PAYMENT_STATUS_PAID)
                        <div class="paid">{{ $locale == 'fr' ? 'Payé' : 'Paid' }}</div>
                    @else
                        <div class="unpaid">{{ $locale == 'fr' ? 'Non payé' : 'Unpaid' }}</div>
                    @endif
                </div>

                <div class="bloc" style="float: right !important; width: 300px !important; text-align: right;">
                    <div class="invoice-title" style="font-weight: bold;">{{ __('facture.invoiceto') }} :</div>
                    <div class="invoice-number">
                        {{ $invoice->participant->fname ?? '' }} {{ $invoice->participant->lname ?? '' }}
                    </div>
                    <div class="invoice-number">{{ $invoice->participant->email ?? '' }}</div>
                    <div class="invoice-number">{{ $invoice->participant->organisation ?? '' }}</div>
                </div>
            </div>
        </div>

        <!-- Tableau des articles -->
        <table style="padding-top:150px">
            <thead>
                <tr>
                    <th>{{ __('facture.descrp') }}</th>
                    <th class="text-right">{{ __('facture.cout') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description_fr }}</td>
                        <td class="text-right">
                            @if ($item->currency == 'EUR')
                                {{ number_format($item->price, 0, ',', ' ') }} €
                            @elseif (in_array($item->currency, ['USD', 'US']))
                                ${{ number_format($item->price, 0, ',', ' ') }}
                            @else
                                {{ number_format($item->price, 0, ',', ' ') }} {{ $item->currency }}
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td class="text-right">TOTAL</td>
                    <td class="text-right">
                        @if ($invoice->currency == 'EUR')
                            {{ number_format($invoice->total_amount, 0, ',', ' ') }} €
                        @elseif (in_array($invoice->currency, ['USD', 'US']))
                            ${{ number_format($invoice->total_amount, 0, ',', ' ') }}
                        @else
                            {{ number_format($invoice->total_amount, 0, ',', ' ') }} {{ $invoice->currency }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>


        <!-- Informations de paiement -->
        <div class="payment-info">

            <div class="payment-title">
                {{ $locale == 'fr' ? '- INFORMATIONS DE PAIEMENT' : '- PAYMENT INFORMATION' }}</div>
            <br>
            <strong>{{ __('facture.vir_bank') }}</strong>
            <div class="bank-info">
                {{ __('facture.smsVbank') }}<br>
                <strong>{{ __('facture.name') }}:</strong> {{ $transfert_info->beneficiary_name }}<br>
                <strong>{{ __('facture.bank') }}:</strong> {{ $transfert_info->bank_name }}<br>
                <strong>{{ __('facture.adresse') }}:</strong> {{ $transfert_info->bank_address }}<br>
                <strong>{{ __('facture.numcompte') }}:</strong> {{ $transfert_info->account_number }}<br>
                {{-- <strong>RIB :</strong> <br> --}}
                <strong>IBAN :</strong> {{ $transfert_info->iban }}<br>
                <strong>SWIFT Code:</strong> {{ $transfert_info->swift }}<br>
            </div>
            <p><strong> {{ __('facture.bank_instructions') }}:</strong></p>
            <p><strong>{{ $locale == 'fr' ? '- PAIEMENT EN LIGNE' : '- ONLINE PAYMENT' }}</strong> <br>
                <a href="https://congress.afwasa.org/get_register/admin/invoices"
                    target="_blank">https://congress.afwasa.org/get_register/admin/invoices</a>
            </p>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            {{-- <p style="text-align: center">
                Riviera Palmeraie – Rond point, place de la renaissance-Immeuble SODECI-2ème étage 25 BP 1174 Abidjan 25
                Côte d’Ivoire /Tél. : +225 27 22 49 96 11 / +225 27 22 49 96 13 - Fax +225 27 22 49 23 30 <br>
                Email : afwasamembershipservices@afwasa.org
            </p> --}}
            <p style="text-align: center">
                Contact support - Email : event@afwasa.org
            </p>
            <p style="text-align: center;color: #ff0000">
                {{ $locale == 'fr' ? 'Cette facture est valable jusqu’au ' : 'This invoice is valid until ' }}
                <strong>{{ $dateFormattedEnd }}</strong>
                {{ $locale == 'fr' ? '. Après cette date, les montants de la présente facture seront automatiquement mis à jour et remplacés par les frais du package en vigueur au moment du paiement. ' : 'After this date, the amounts on this invoice will automatically be updated and replaced by the applicable package fees at the time of payment.' }}
            </p>
            <p class="text-muted">
                <span
                    style="color: #ff0000"><strong><sup>*</sup></strong></span>{{ $locale == 'fr' ? 'Ce document est généré automatiquement et ne nécessite pas de signature.' : 'This document is automatically generated and does not require a signature.' }}
            </p>
        </div>
    </div>
</body>

</html>
