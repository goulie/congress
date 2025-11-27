@extends('voyager::master')

@section('css')

    <style>
        /* Conteneur de la page A4 */
        .a4-page {
            width: 210mm;
            height: 297mm;
            padding: 10mm;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        /* Conteneur pour les 4 badges */
        .badge-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            /* Centre les badges avec un peu d'espace */
            align-content: space-around;
            height: 100%;
            box-sizing: border-box;
        }

        /* Style de chaque badge individuel */
        .badge-card {
            width: 95mm;
            /* Ajusté pour laisser un peu plus de marge entre */
            height: 135mm;
            border: none;
            /* Pas de bordure directe, l'ombre suffira */
            border-radius: 12px;
            background: linear-gradient(to bottom right, #f7f9fc, #e9ecef);
            /* Dégradé doux */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            text-align: center;
            position: relative;
            /* Pour positionner des éléments à l'intérieur */
            transition: all 0.3s ease;
            /* Animation douce au survol (non visible à l'impression) */
        }

        .badge-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        /* En-tête du badge (peut contenir un logo ou une image de fond subtile) */
        .badge-header {
           
            /* Dégradé de bleu pour l'en-tête */
            padding: 15px 10px;
            color: rgb(4, 31, 122);
            font-size: 14px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
             /* display: inline-flex; */
            /*justify-content: space-between;
            align-items: center; */
        }

        .badge-header {
            max-height: 30px;
            /* Taille du logo */
            width: auto;
            margin-right: 10px;
        }

        /* Contenu principal du badge */
        .badge-content {
            padding-top: 82px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .badge-image-upload {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            /* Image ronde */
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            background-color: #f0f0f0;
            /* Fond par défaut si pas d'image */
        }

        .badge-name {
            font-size: 26px;
            font-weight: 700;
            /* Plus gras */
            color: #212529;
            margin-bottom: 3px;
            line-height: 1.2;
        }

        .badge-org {
            font-size: 17px;
            color: #5a6268;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-org i {
            margin-right: 5px;
            color: #6c757d;
        }

        .badge-country {
            font-size: 15px;
            color: #6c757d;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-country i {
            margin-right: 5px;
            color: #6c757d;
        }

        .badge-qr {
            width: 90px;
            height: 90px;
            padding: 5px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-top: auto;
            /* Pousse le QR code vers le bas du contenu */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-qr img {
            max-width: 100%;
            height: auto;
            display: block;
            /* Supprime l'espace sous l'image */
        }

        /* La bande bleue pour le rôle (au bas du badge) */
        .badge-role {
            
            /* Dégradé vert pour le rôle */
            color: white;
            font-size: 18px;
            font-weight: 600;
            padding: 12px 10px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            letter-spacing: 0.5px;
        }

        /* Styles d'impression */
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }

            .a4-page {
                margin: 0;
                padding: 0;
                box-shadow: none;
                width: 100%;
                height: 100%;
                page-break-after: always;
                /* S'assurer qu'une nouvelle page commence après chaque A4-page */
            }

            .badge-container {
                padding: 10mm;
                width: 210mm;
                height: 297mm;
                justify-content: space-between;
                /* Plus serré pour l'impression */
                align-content: space-between;
            }

            .badge-card {
                box-shadow: 0 0 0 1px #ccc;
                /* Bordure légère pour l'impression */
                border-radius: 0;
                /* Pas d'arrondi à l'impression si le cutter est droit */
                background: none;
                /* Pas de dégradé à l'impression pour économiser l'encre */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                /* Force l'impression des couleurs de fond */
            }

            .badge-header,
            .badge-role {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection

@section('page_title', __('Registration Form'))

@section('content')

    <div class="container-fluid">
        <div class="row">
            <!-- La page A4 -->
            <div class="a4-page">
                <!-- Le conteneur des 4 badges -->
                <div class="badge-container">

                    <!-- Badge 1 -->
                    <div class="badge-card">
                        <div class="badge-header">
                            
                            <p style="font-weight: bold;font-size: 15px">
                                {!! $participant->congres->title !!}
                            </p>
                            <p style="font-weight: bold;font-size:10px">
                                {!! $participant->congres->title !!}
                            </p>
                            <hr style="height: 10px">
                        </div>
                        <div class="badge-content">
                            <img src="{{ asset('/public/storage' .'/' . $participant->congres->banniere) }}" style="width:100%">
                            
                            <div class="badge-name">{{ $participant->badge_full_name ?? $participant->lname .' '. $participant->fname }}</div>
                            <div class="badge-org"><i class="fa fa-building"></i> {{ $participant->organisation }}</div>
                            <div class="badge-country"><i class="fa fa-globe"></i> {{ $participant->nationality->libelle_fr }}</div>
                            <div class="badge-qr">
                                <img src="{{ asset('/public'.'/'.$participant->code_path) }}" alt="Code QR">
                            </div>
                        </div>
                        <div class="badge-role" style="background-color: {{ $participant->badge_color->color }}">
                            {{ $participant->badge_color->libelle }}
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

    <script></script>
@endsection
