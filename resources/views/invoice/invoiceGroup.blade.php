<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>
        @if (app()->getLocale() == 'fr')
            Facture Groupée - Congrès
        @else
            Group Invoice - Congress
        @endif
    </title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0a4d8c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            height: 60px;
            margin: 0 25px;
            vertical-align: middle;
        }

        .title {
            display: inline-block;
            font-size: 18px;
            font-weight: bold;
            color: #0a4d8c;
            vertical-align: middle;
        }

        .summary {
            background: #f1f6fb;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #0a4d8c;
            margin-bottom: 25px;
        }

        .summary p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #0a4d8c;
            color: white;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }

        td {
            border-bottom: 1px solid #ccc;
            padding: 8px;
            font-size: 13px;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .participant-block {
            margin-bottom: 25px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .participant-header {
            font-weight: bold;
            color: #0a4d8c;
            margin-bottom: 5px;
            font-size: 15px;
        }

        .items-table th {
            background: #e3e9f1;
            color: #333;
            font-weight: 600;
        }

        .status {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin-left: 10px;
        }

        .status.paid,
        .status.payé {
            color: #2e7d32;
            background: #c8e6c9;
            padding: 2px 8px;
            border-radius: 3px;
        }

        .status.unpaid,
        .status.impayé {
            color: #c62828;
            background: #ffcdd2;
            padding: 2px 8px;
            border-radius: 3px;
        }

        .total-section {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }

        .payment-info {
            margin-top: 35px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fafafa;
        }

        .payment-info h4 {
            margin-top: 0;
            color: #0a4d8c;
            text-transform: uppercase;
            font-size: 13px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .payment-info p {
            margin: 3px 0;
            font-size: 12px;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-top: 30px;
        }

        /* Additional styles */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 40px;
        }

        .invoice-header {
            display: block;
            width: 100%;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .header-left,
        .header-right {
            display: inline-block;
            vertical-align: top;
            width: 48%;
        }

        .header-left {
            float: left;
        }

        .header-right {
            float: right;
            text-align: right;
        }

        .address-block {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-top: 10px;
            font-size: 12px;
            line-height: 1.4;
        }

        /* Rest of your existing styles remain the same */
        .header {
            text-align: center;
            border-bottom: 2px solid #0a4d8c;
            padding-bottom: 10px;
            margin-bottom: 20px;
            clear: both;
        }

        .summary {
            background: #f1f6fb;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #0a4d8c;
            margin-bottom: 25px;
            clear: both;
        }

        /* end additional styles */
    </style>
</head>

<body>
    @php
        $congres = App\Models\Congress::latest()->first();
        // Get the first participant's invoice for header info
        $firstParticipant = $participants->first();
        $firstInvoice = $firstParticipant ? $firstParticipant->invoices->first() : null;
    @endphp

    <div class="header">
        <div class="logo-container">
            <img src="{{ '/public/storage/' . $congres->banniere }}" style="width:100%">
            <div class="congres-text">
                @if (app()->getLocale() == 'fr')
                    <h5>CONGRÈS INTERNATIONAL ET EXPOSITION DE L'AAEA <br>
                        DU {{ \Carbon\Carbon::parse($congres->begin_date)->format('d') }} au
                        {{ \Carbon\Carbon::parse($congres->end_date)->isoFormat('D MMMM YYYY') }} -
                        {{ $congres->hostCountry->libelle_fr ?? '' }}
                    </h5>
                @else
                    <h5>AfWASA INTERNATIONAL CONGRESS AND EXHIBITION <br>
                        FROM {{ \Carbon\Carbon::parse($congres->begin_date)->format('F d') }} to
                        {{ \Carbon\Carbon::parse($congres->end_date)->format('F d, Y') }} -
                        {{ $congres->hostCountry->libelle_en ?? '' }}
                    </h5>
                @endif
            </div>
        </div>
    </div>


    <!-- New Invoice Header Block -->
    <div class="invoice-header">
        <!-- Left Block: Invoice Details -->
        <div class="header-left">
            <div style="font-size: 16px; font-weight: bold; color: #0a4d8c; margin-bottom: 8px;">
                @if (app()->getLocale() == 'fr')
                    FACTURE DE GROUPE
                @else
                    GROUP INVOICE
                @endif
            </div>

            <div style="margin-bottom: 5px;">
                <strong>
                    @if (app()->getLocale() == 'fr')
                        Numéro de facture :
                    @else
                        Invoice number:
                    @endif
                </strong>

                GRP-{{ now()->format('Ymd') }}-{{ str_pad($participants->count(), 3, '0', STR_PAD_LEFT) }}

            </div>

            <div style="margin-bottom: 5px;">

                @php
                    // Initialiser les variables pour le statut global
                    $allPaid = true;
                    $hasInvoices = false;

                    // Vérifier chaque participant
                    foreach ($participants as $participant) {
                        $invoice = $participant->invoices->first();
                        if ($invoice) {
                            $hasInvoices = true;
                            // Si une seule facture n'est pas payée, le statut global est "non payé"
        if ($invoice->status !== App\Models\Invoice::PAYMENT_STATUS_PAID) {
            $allPaid = false;
            // Pas besoin de continuer à vérifier si on a trouvé un non-payé
            break;
        }
    }
}

// Déterminer le texte et le style selon le statut global
if ($hasInvoices) {
    if ($allPaid) {
        $statusText = app()->getLocale() == 'fr' ? 'PAYÉE' : 'PAID';
        $statusColor = '#2e7d32';
        $statusBg = '#c8e6c9';
    } else {
        $statusText = app()->getLocale() == 'fr' ? 'NON PAYÉE' : 'UNPAID';
        $statusColor = '#c62828';
        $statusBg = '#ffcdd2';
    }
} else {
    // Cas où aucune facture n'existe
                        $statusText = app()->getLocale() == 'fr' ? 'NON PAYÉE' : 'UNPAID';
                        $statusColor = '#c62828';
                        $statusBg = '#ffcdd2';
                    }
                @endphp


            </div>
            <div style="margin-bottom: 5px;">
                <strong>
                    @if (app()->getLocale() == 'fr')
                        Statut :
                    @else
                        Status:
                    @endif
                </strong>
                <span
                    style="color: {{ $statusColor }}; font-weight: bold; background: {{ $statusBg }}; padding: 2px 8px; border-radius: 3px;">
                    {{ $statusText }}
                </span>

                @if (!$allPaid && $hasInvoices)
                    <div style="font-size: 11px; color: #666; margin-top: 3px;">
                        @if (app()->getLocale() == 'fr')
                            (Certaines factures individuelles ne sont pas payées)
                        @else
                            (Some individual invoices are unpaid)
                        @endif
                    </div>
                @endif
            </div>

            <div style="margin-bottom: 5px;">
                <strong>
                    @if (app()->getLocale() == 'fr')
                        Date de facture :
                    @else
                        Invoice date:
                    @endif
                </strong>
                {{ now()->format(app()->getLocale() == 'fr' ? 'd/m/Y' : 'm/d/Y') }}
            </div>
        </div>

        <!-- Right Block: Bill To Information -->
        <div class="header-right">
            <div style="font-size: 16px; font-weight: bold; color: #0a4d8c; margin-bottom: 8px;">
                @if (app()->getLocale() == 'fr')
                    FACTURÉ À
                @else
                    BILL TO
                @endif
            </div>


            <div class="address-block">
                <div><strong
                        style="font-weight: bold;text-transform: uppercase">{{ $organisation ?? (app()->getLocale() == 'fr' ? 'Organisation non spécifiée' : 'Organization not specified') }}</strong>
                </div>
                <div>{{ $email ?? 'N/A' }}</div>
                <div>{{ $Adresse ?? 'N/A' }}</div>
            </div>

        </div>

        <!-- Clear float -->
        <div style="clear: both;"></div>
    </div>


    <div class="summary">
        <p>
            @if (app()->getLocale() == 'fr')
                <strong>Nombre de participants :</strong> {{ $participants->count() }}
            @else
                <strong>Number of participants:</strong> {{ $participants->count() }}
            @endif
        </p>

        <p>
            @if (app()->getLocale() == 'fr')
                <strong>Date de génération :</strong> {{ now()->format('d/m/Y') }}
            @else
                <strong>Generation date:</strong> {{ now()->format('m/d/Y') }}
            @endif
        </p>

        <p>
            @if (app()->getLocale() == 'fr')
                <strong>Montant total :</strong>
            @else
                <strong>Total amount:</strong>
            @endif

            @if ($currency == 'EUR')
                {{ number_format($totalAmount, 0, ',', ' ') }} €
            @elseif (in_array($currency, ['USD', 'US']))
                ${{ number_format($totalAmount, 0, ',', ' ') }}
            @else
                {{ number_format($totalAmount, 0, ',', ' ') }} {{ $currency }}
            @endif
        </p>
    </div>

    {{-- Loop on participants --}}
    @foreach ($participants as $index => $participant)
        @php

            $invoice = $participant->invoices->first();

            if ($invoice && $invoice->items) {
                $invoice->items = $invoice->items
                    ->sortBy(function ($item) {
                        $text = $item->description_en ?? ($item->description_fr ?? '');

                        // FORMAT EN / ISO : 2026-02-09
                        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $text, $m)) {
                            return \Carbon\Carbon::createFromDate((int) $m[1], (int) $m[2], (int) $m[3]);
                        }

                        // FORMAT FR : 09 février 2026
                        if (preg_match('/(\d{2})\s+février\s+(\d{4})/iu', $text, $m)) {
                            return \Carbon\Carbon::createFromDate((int) $m[2], 2, (int) $m[1]);
                        }

                        // Pas de date → à la fin
                        return \Carbon\Carbon::maxValue();
                    })
                    ->values(); // réindexation propre
            }

            $status =
                $invoice && $invoice->status == App\Models\Invoice::PAYMENT_STATUS_PAID
                    ? App\Models\Invoice::PAYMENT_STATUS_PAID
                    : App\Models\Invoice::PAYMENT_STATUS_UNPAID;

            $congres = App\Models\Congress::latest()->first();
            $transfert_info = App\Models\CongressBankTransfer::where('congres_id', $congres->id)->first();

            $periode = App\Models\Periode::PeriodeActive($congres->id);

            $locale = app()->getLocale(); // fr | en
            \Carbon\Carbon::setLocale($locale);

            $start = \Carbon\Carbon::parse($periode->start_date);
            $end = \Carbon\Carbon::parse($periode->end_date);

            $daysRemaining = $periode->joursRestants();

            $dateFormattedStart =
                $locale === 'fr' ? $start->translatedFormat('d F Y') : $start->translatedFormat('F d, Y');

            $dateFormattedEnd = $locale === 'fr' ? $end->translatedFormat('d F Y') : $end->translatedFormat('F d, Y');
        @endphp


        <div class="participant-block">
            <div class="participant-header">
                {{ $index + 1 }}. {{ $participant->fname }} {{ $participant->lname }}
                —
                {{ $participant->sigle_organisation ? $participant->sigle_organisation : $participant->organisation }}
            </div>

            <p>
                <strong>
                    @if (app()->getLocale() == 'fr')
                        Montant total :
                    @else
                        Total amount:
                    @endif
                </strong>
                {{ $invoice ? number_format($invoice->total_amount, 0, ',', ' ') : '—' }}
                {{ $invoice->currency ?? '' }}
                @if ($status === App\Models\Invoice::PAYMENT_STATUS_PAID)
                    <span class="status paid">{{ app()->getLocale() == 'fr' ? 'Payé' : 'Paid' }} </span>
                    @isset($invoice->payment_method)
                        {{ app()->getLocale() == 'fr' ? '- Méthode de paiement' : '- Payment method' }} <strong>
                            {{ $invoice->payment_method ?? '' }} </strong>
                    @endisset
                @else
                    <span class="status unpaid">{{ app()->getLocale() == 'fr' ? 'Non payé' : 'Unpaid' }}</span>
                @endif
            </p>

            @if ($invoice && $invoice->items->count())
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>
                                @if (app()->getLocale() == 'fr')
                                    Description
                                @else
                                    Description
                                @endif
                            </th>
                            <th class="text-right">
                                @if (app()->getLocale() == 'fr')
                                    Prix
                                @else
                                    Price
                                @endif
                            </th>
                            <th>
                                @if (app()->getLocale() == 'fr')
                                    Devise
                                @else
                                    Currency
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td>
                                    {{ app()->getLocale() == 'fr' ? $item->description_fr : $item->description_en ?? $item->description_fr }}
                                </td>
                                <td class="text-right">{{ number_format($item->price, 0, ',', ' ') }}</td>
                                <td>{{ $item->currency }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="font-size:12px; color:#999;">
                    @if (app()->getLocale() == 'fr')
                        Aucun détail d’article disponible pour ce participant.
                    @else
                        No item details available for this participant.
                    @endif
                </p>
            @endif
        </div>
    @endforeach

    <div class="total-section">
        <p>
            @if (app()->getLocale() == 'fr')
                Total général :
            @else
                Grand total:
            @endif
            @if ($currency == 'EUR')
                {{ number_format($totalAmount, 0, ',', ' ') }} €
            @elseif (in_array($currency, ['USD', 'US']))
                ${{ number_format($totalAmount, 0, ',', ' ') }}
            @else
                {{ number_format($totalAmount, 0, ',', ' ') }} {{ $currency }}
            @endif
        </p>
    </div>
    @php
        $transfert_info = App\Models\CongressBankTransfer::where('congres_id', $congres->id)->first();
    @endphp
    <div class="payment-info">
        <h4>
            @if (app()->getLocale() == 'fr')
                Informations de paiement / Virement bancaire
            @else
                Payment Information / Bank Transfer
            @endif
        </h4>
        <p><strong>
                @if (app()->getLocale() == 'fr')
                    Nom du bénéficiaire :
                @else
                    Beneficiary name:
                @endif
            </strong> {{ $transfert_info->beneficiary_name }}</p>
        <p><strong>Banque :</strong> {{ $transfert_info->bank_name }}</p>
        <p><strong>
                @if (app()->getLocale() == 'fr')
                    Adresse de la banque :
                @else
                    Bank address:
                @endif
            </strong> {{ $transfert_info->bank_address }}</p>
        <p><strong>
                @if (app()->getLocale() == 'fr')
                    Numéro de compte :
                @else
                    Account number:
                @endif
            </strong> {{ $transfert_info->account_number }}</p>
        <p><strong>IBAN :</strong> {{ $transfert_info->iban }}</p>
        <p><strong>SWIFT Code :</strong> {{ $transfert_info->swift }}</p>
        {{-- <p><strong>
                @if (app()->getLocale() == 'fr')
                    Référence du virement :
                @else
                    Transfer reference:
                @endif
            </strong> FACT-GROUP-{{ now()->format('Ymd') }}
        </p> --}}
    </div>

    <div class="footer">
        <p>contact support | event@afwasa.org</p>
        <p style="text-align: center;color: #ff0000">
            {{ app()->getLocale() == 'fr' ? 'Cette facture est valable jusqu’au ' : 'This invoice is valid until ' }}
            <strong>{{ $dateFormattedEnd }}</strong>
            {{ app()->getLocale() == 'fr' ? '. Apres cette date les frais du package en cours pendant cette période seront automatiquement appliqués et remplaceront les montants affichés dans cette facture. ' : 'After this date, the current package fees applicable during this period will automatically apply and replace the amounts currently listed on this invoice.' }}
        </p>
        <p class="text-muted">
            <span
                style="color: #ff0000"><strong><sup>*</sup></strong></span>{{ app()->getLocale() == 'fr' ? 'Ce document est généré automatiquement et ne nécessite pas de signature.' : 'This document is automatically generated and does not require a signature.' }}
        </p>

    </div>

</body>

</html>
