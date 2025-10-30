<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Groupée - Congrès</title>
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

        .summary p { margin: 4px 0; }

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

        tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }

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

        .status.paid, .status.payé { color: #2e7d32; } /* vert */
        .status.unpaid, .status.impayé { color: #c62828; } /* rouge */

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

    <div class="header">
        <img src="{{ Voyager::image(setting('site.logo')) }}">
        <span class="title">FACTURE GROUPÉE DU CONGRÈS</span>
        <img src="{{ public_path('images/logo_hote.png') }}" alt="Logo Hôte">
    </div>

    <div class="summary">
        <p><strong>Nombre de participants :</strong> {{ $participants->count() }}</p>
        <p><strong>Date de génération :</strong> {{ now()->format('d/m/Y') }}</p>
        <p><strong>Montant total :</strong>
            @if ($currency == 'EUR')
                {{ number_format($totalAmount, 0, ',', ' ') }} €
            @elseif (in_array($currency, ['USD', 'US']))
                ${{ number_format($totalAmount, 0, ',', ' ') }}
            @else
                {{ number_format($totalAmount, 0, ',', ' ') }} {{ $currency }}
            @endif
        </p>
    </div>

    {{-- Boucle sur chaque participant --}}
    @foreach ($participants as $index => $participant)
        @php
            $invoice = $participant->invoices->first();
            $status = strtolower($invoice->status ?? 'unpaid');
        @endphp

        <div class="participant-block">
            <div class="participant-header">
                {{ $index + 1 }}. {{ $participant->fname }} {{ $participant->lname }}
                — {{ $participant->organisation ?? 'Organisation non spécifiée' }}
            </div>

            <p>
                <strong>Montant total :</strong> 
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
                            <th>Description</th>
                            <th class="text-right">Prix</th>
                            <th>Devise</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td>
                                    {{ $item->description_fr }}
                                </td>
                                <td class="text-right">{{ number_format($item->price, 0, ',', ' ') }}</td>
                                <td>{{ $item->currency }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="font-size:12px; color:#999;">Aucun détail d’article disponible pour ce participant.</p>
            @endif
        </div>
    @endforeach

    <div class="total-section">
        <p>Total général :
            @if ($currency == 'EUR')
                {{ number_format($totalAmount, 0, ',', ' ') }} €
            @elseif (in_array($currency, ['USD', 'US']))
                ${{ number_format($totalAmount, 0, ',', ' ') }}
            @else
                {{ number_format($totalAmount, 0, ',', ' ') }} {{ $currency }}
            @endif
        </p>
    </div>

    <div class="payment-info">
        <h4>Informations de paiement / Virement bancaire</h4>
        <p><strong>Nom du bénéficiaire :</strong> Association Africaine de l’Eau (AfWASA)</p>
        <p><strong>Banque :</strong> Société Générale Côte d’Ivoire (SGCI)</p>
        <p><strong>Adresse de la banque :</strong> Abidjan Plateau – Avenue Marchand</p>
        <p><strong>Numéro de compte :</strong> 00001-12345678901-75</p>
        <p><strong>IBAN :</strong> CI70SGCI0000101234567890175</p>
        <p><strong>Code SWIFT :</strong> SGCICIAB</p>
        <p><strong>Référence du virement :</strong> FACT-GROUP-{{ now()->format('Ymd') }}</p>
    </div>

    <div class="footer">
        <p>Riviera Palmeraie – Abidjan – Côte d’Ivoire | contact@afwasa.org</p>
        <p>Ce document est généré automatiquement et ne nécessite pas de signature.</p>
    </div>

</body>
</html>
