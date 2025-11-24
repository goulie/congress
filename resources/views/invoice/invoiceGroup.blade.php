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
        }

        .status.unpaid,
        .status.impayé {
            color: #c62828;
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
    </style>
</head>

<body>
    @php
        $congres = App\Models\Congress::latest()->first();
    @endphp

    <div class="header">
        <div class="logo-container">
            <img src="{{ '/public/storage/' . $congres->banniere }}" style="width:100%">
            <div class="congres-text">
                @if (app()->getLocale() == 'fr')
                    <h5>CONGRÈS INTERNATIONAL ET EXPOSITION DE L'AAEA <br>
                        DU {{ \Carbon\Carbon::parse($congres->begin_date)->format('d') }} AU
                        {{ \Carbon\Carbon::parse($congres->end_date)->isoFormat('D MMMM YYYY') }} -
                        {{ $congres->hostCountry->libelle_fr ?? '' }}
                    </h5>
                @else
                    <h5>AfWASA INTERNATIONAL CONGRESS AND EXHIBITION <br>
                        FROM {{ \Carbon\Carbon::parse($congres->begin_date)->format('F d') }} TO
                        {{ \Carbon\Carbon::parse($congres->end_date)->format('F d, Y') }} -
                        {{ $congres->hostCountry->libelle_en ?? '' }}
                    </h5>
                @endif
            </div>
        </div>
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
            $status = strtolower($invoice->status ?? 'unpaid');
            if (app()->getLocale() == 'fr') {
                $status = $status == 'pending' ? 'impayé' : $status == 'paid' ? 'payé' : $status;
            } else {
                $status = $status == 'pending' ? 'unpaid' : $status == 'paid' ? 'paid' : $status;
            }

        @endphp

        <div class="participant-block">
            <div class="participant-header">
                {{ $index + 1 }}. {{ $participant->fname }} {{ $participant->lname }}
                —
                {{ $participant->organisation ?? (app()->getLocale() == 'fr' ? 'Organisation non spécifiée' : 'Organisation not specified') }}
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
                @if ($status === 'paid' || $status === 'payé')
                    <span class="status paid">{{ strtoupper($status) }}</span>
                @else
                    <span class="status unpaid">{{ strtoupper($status) }}</span>
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
        <p>
            @if (app()->getLocale() == 'fr')
                Ce document est généré automatiquement et ne nécessite pas de signature.
            @else
                This document is automatically generated and does not require a signature.
            @endif
        </p>
    </div>

</body>

</html>
