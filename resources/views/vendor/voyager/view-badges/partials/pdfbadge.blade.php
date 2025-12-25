<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badges Multiples A5 - Export PDF</title>
    <style>
        /* --- CONFIGURATION D'IMPRESSION A5 --- */
        @page {
            size: A5;
            /* Format A5: 148mm x 210mm */
            margin: 0;
        }

        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            /* Force chaque badge à commencer sur une nouvelle page */
            .a5-page {
                break-after: page;
                page-break-after: always;
            }
        }

        /* --- STYLE GÉNÉRAL --- */
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            font-family: 'Arial', 'Helvetica', sans-serif;
            /* Pour la prévisualisation écran : on affiche les badges en colonne */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding-top: 10px;
            padding-bottom: 50px;
        }

        /* Conteneur unique pour le badge (taille A5) */
        .a5-page {
            width: 148mm;
            height: 205mm;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            text-align: center;
            margin-bottom: 30px;
            /* Espace entre les badges à l'écran */
            overflow: hidden;
            /* Sécurité pour ne pas déborder */
        }

        /* Indicateur de trou */
        .hole-indicator {
            position: absolute;
            top: 5mm;
            left: 50%;
            transform: translateX(-50%);
            width: 8mm;
            height: 8mm;
            border: 1px dashed #999;
            border-radius: 50%;
            z-index: 10;
            background-color: transparent;
            box-sizing: border-box;
            pointer-events: none;
        }

        /* --- NOUVELLE BANIÈRE SPONSOR (en bas) --- */
        .sponsor-banner-bottom {
            margin: 5px;
            text-align: center;
            height: 100px;
            /*  background-color: #07c70a4f; */
            /*border-top: 2px solid #0055ff; */
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: auto;
        }

        .sponsor-banner-bottom img {
            width: 100%;
            height: auto;
            padding-top: 16mm;
            padding-bottom: 6mm;
            /* object-fit: cover;*/
        }

        /* --- CONTENU DU BADGE --- */

        .header {
            padding-bottom: 5mm;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            line-height: 1.2;
            margin-bottom: 2mm;
            padding-top: 15px;
        }

        .date-loc {
            font-size: 10pt;
            color: #4b0082;
            font-weight: bold;
            margin-bottom: 5mm;
        }

        .separator {
            width: 80%;
            height: 2px;
            background-color: #00a651;
            margin: 0 auto;
        }

        /* Corps du badge */
        .participant-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* padding: 10mm; */
        }

        .name {
            font-size: 30pt;
            /* Légèrement réduit pour les longs noms */
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 10mm;
            line-height: 1.1;
        }

        .role {
            font-size: 18pt;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 15mm;
        }

        .country {
            font-size: 24pt;
            font-weight: bold;
            color: #00334e;
            text-transform: uppercase;
        }

        .organization {
            font-size: 24pt;
            /* font-weight: bold; */
            color: #00334e;
            text-transform: uppercase;
        }

        /* Section QR/Logos */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 0 10mm 15mm 10mm;
            box-sizing: border-box;
        }

        /* Logo principal AfWASA */
        .main-logo-bottom {
            width: 60mm;
            height: auto;
            text-align: left;
        }

        .main-logo-bottom img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(1px 1px 1px rgba(0, 0, 0, 0.3));
        }

        .qr-container {
            width: 35mm;
            height: 35mm;
            flex-shrink: 0;
            margin-bottom: 60px;
            /* Espace pour ne pas toucher le footer */
        }

        .qr-code {
            width: 100%;
            height: 100%;
            background-size: cover;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        /* Pied de page Bleu */
        .footer-bar {
            background-color: #0055ff;
            height: 30mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48pt;
            font-weight: bold;
            letter-spacing: 2px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        /* --- INSTRUCTIONS UI --- */
        .ui-controls {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            text-align: left;
            padding: 20px;
            font-family: sans-serif;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-width: 300px;
        }

        .btn {
            background: #0055ff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            margin-top: 10px;
            display: block;
            width: 100%;
        }

        .btn:hover {
            background: #0033cc;
        }
    </style>
</head>

<body>

    <div class="ui-controls no-print">
        <h3>Générateur de Badges</h3>
        <p>Ce script génère automatiquement une page A5 par personne listée.</p>
        <button class="btn" onclick="window.print()">Exporter en PDF</button>
        <p style="font-size: 12px; color: #666; margin-top: 10px;">
            Options d'impression :<br>
            • Destination: PDF<br>
            • Taille: A5<br>
            • Marges: Aucune<br>
            • Graphiques d'arrière-plan: Coché
        </p>
    </div>

    <!-- C'est ici que les badges seront injectés par Javascript -->
    <div id="conteneur-badges">
        @foreach ($participants as $participant)
            <div class="a5-page">
                <!-- Indicateur trou -->
                <div class="hole-indicator no-print"></div>
                @php
                    $congres = App\Models\Congress::latest()->first();

                    \Carbon\Carbon::setLocale(app()->getLocale());
                @endphp
                <!-- En-tête -->
                
                <div class="header">
                    <div class="sponsor-banner-bottom">
                        <img src="{{ asset('/public/storage' . '/' . $participant->congres->banniere_badge) }}"
                            style="width: 100%;">
                    </div>
                    <div class="title">{!! $congres->translate(app()->getLocale(), 'fallbackLocale')->title !!}</div>
                    <div class="date-loc">
                        {{ \Carbon\Carbon::parse($congres->begin_date)->translatedFormat('d') }}
                        -
                        {{ \Carbon\Carbon::parse($congres->end_date)->translatedFormat('d F Y') }}
                        | {{ $congres->event_place ?? '' }} - {{ app()->getLocale() == 'fr' ? $congres->hostCountry->libelle_fr : $congres->hostCountry->libelle_en }}
                    </div>
                    <div class="separator"></div>
                </div>

                <!-- Info Participant (Données dynamiques) -->
                <div class="participant-info">
                    <div class="name">
                        {{ $participant->badge_full_name ?? $participant->civility?->libelle . ' ' . $participant->fname . ' ' . $participant->lname }}
                    </div>
                    <div class="role">{{ $participant->role_badge_congres ?? $participant->job }} </div>
                    <div class="organization">{{ $participant->sigle_organisation ?? $participant->organisation }}</div>
                    <div class="country">{{ $participant->country->libelle_fr }}</div>
                </div>

                <!-- Bannière Sponsor -->
                

                <!-- Bas de page : Logo & QR -->
                <div class="bottom-section">
                    <div class="main-logo-bottom" style="margin-bottom: 40px;">
                        {{-- <img src="{{ Voyager::image(setting('site.logo')) }}" alt="Logo AfWASA"> --}}
                    </div>
                    <div class="qr-container">
                        <div class="qr-code"
                            style="background-image: url({{ asset('public/' . $participant->code_path) }})"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="footer-bar" style="background-color: {{ $participant->badge_color->color }}">
                    {{ $participant->badge_color->libelle }}
                </div>
                {{-- <div class="footer-bar">DELEGATE</div> --}}
            </div>
        @endforeach
    </div>



</body>

</html>
