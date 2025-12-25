<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Letter</title>
    <style>
        body {
            font-family: 'Arial', sans-serif !important;
            line-height: 1.4;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 5px;
            font-size: 13px;
            background-color: #ffffff;
        }

        .letter-container {
            background-color: white;
            padding: 3px;
            border-radius: 5px;
            text-align: justify;
            page-break-inside: avoid;
        }

        .header {
            /* max-width: 900px; */
            text-align: center;
            padding-bottom: 8px;
        }

        .logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
            text-align: center;
        }

        .date {
            text-align: right;
            margin-bottom: 4px;
            font-size: 12px;
        }

        .recipient-address {
            margin-bottom: 8px;
            font-size: 12px;
        }

        .content {
            margin-bottom: 8px;
            text-align: justify;

        }

        .content p {
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 8px;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .recipient-address.lang-fr {
            text-align: right;
        }

        .recipient-address.lang-other {
            text-align: left;
        }

        .highlight {
            font-weight: bold;
        }

        /* Assurer que tout tient sur une page */
        @media print {
            body {
                padding: 10px;
                margin: 0;
            }

            .letter-container {
                padding: 5px;
                margin: 0;
            }
        }

        /* Style pour la signature */
        .signature {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width:200px
            
        }

        /* Pour la position center */
        .signature[class*="center"] {
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }

        /* Pour la position right */
        .signature[class*="right"] {
            justify-content: flex-end;
            align-items: flex-end;
            text-align: right;
            margin-left: auto;
        }

        /* Pour la position left */
        .signature[class*="left"] {
            justify-content: flex-start;
            align-items: flex-start;
            text-align: left;
            margin-right: auto;
        }

        /* Style pour l'image de signature */
        .signature img {
            height: 90px !important;
            width: auto !important;
            margin-bottom: 10px;
        }

        /* Style pour le texte */
        .signature p {
            margin: 0;
            line-height: 1.0;
        }

        .signature strong {
            display: block;
            margin-bottom: 2px;
        }
    </style>
</head>

<body>
    @php
        use Carbon\Carbon;

        // Dates du congrès
        $dateBeginFr = Carbon::parse($data->congres->begin_date)->locale('fr')->isoFormat('D MMMM YYYY');
        $dateEndFr = Carbon::parse($data->congres->end_date)->locale('fr')->isoFormat('D MMMM YYYY');

        $dateBeginEn = Carbon::parse($data->congres->begin_date)->locale('en')->isoFormat('MMMM D, YYYY');
        $dateEndEn = Carbon::parse($data->congres->end_date)->locale('en')->isoFormat('MMMM D, YYYY');

        // Date de création de la lettre
        $dateCreatedFr = Carbon::parse($content->created_at ?? now())
            ->locale('fr')
            ->isoFormat('D MMMM YYYY');
        $dateCreatedEn = Carbon::parse($content->created_at ?? now())
            ->locale('en')
            ->isoFormat('MMMM D, YYYY');

        // Variables de remplacement basées sur le modèle Participant
        $variables = [
            '{fullname}' => $data->fname . ' ' . $data->lname,
            '{first_name}' => $data->fname,
            '{last_name}' => $data->lname,
            '{email}' => $data->email,
            '{organisation}' => $data->organisation,
            '{contact}' => $data->phone,
            '{job}' => $data->job,
            '{passeport_number}' => $data->passeport_number,
            '{nationality}' => $data->nationality->libelle_fr ?? ($data->nationality->libelle_en ?? ''),
            '{country}' => $data->country->libelle_fr ?? ($data->country->libelle_en ?? ''),
            '{gender}' => $data->gender->libelle ?? '',
            '{civility}' => $data->civility->translate($content->langue, 'fallbackLocale')->libelle ?? '',
            '{participant_category}' => $data->participantCategory->libelle ?? '',
            '{type_member}' => $data->typeMember->libelle ?? '',
            '{type_accompagning}' => $data->type_accompagning->libelle ?? '',

            '{congres_title}' =>
                $data->congres->translate($content->langue ?? app()->getLocale(), 'fallbackLocale')->title ?? '',
            '{congres_theme}' =>
                $data->congres->translate($content->langue ?? app()->getLocale(), 'fallbackLocale')->theme ?? '',
            '{congres_date_begin_fr}' => $dateBeginFr ?? '',
            '{congres_date_end_fr}' => $dateEndFr ?? '',
            '{congres_date_begin_en}' => $dateBeginEn ?? '',
            '{congres_date_end_en}' => $dateEndEn ?? '',
            '{congres_host}' => $data->congres->host_name ?? '',
            '{congres_host_country}' =>
                $data->congres->hostCountry->libelle_fr ?? ($data->congres->hostCountry->libelle_en ?? ''),

            '{dear}' =>  ($content->langue ?? app()->getLocale()) == 'fr'? ($data->gender_id == 2 ? 'Madame ': 'Monsieur ') : 'Dear ',
            '{madam}' => ($content->langue ?? app()->getLocale()) == 'fr' ? 'Madame' : 'Madam',
            '{sir}' => ($content->langue ?? app()->getLocale()) == 'fr' ? 'Monsieur' : 'Sir',
        ];

        $LetterContent = str_replace(array_keys($variables), array_values($variables), $content->content ?? '');

        $signature_job = str_replace(';', '<br>', $content->signatory_job ?? '');
    @endphp
    
    <div class="letter-container">
        <div class="header">
            <img src="/public/storage/{{ $content->header_logo }}" alt="Logo"
                class="logo" loading="lazy">
        </div>

        <div class="date">
            <p>{{ ($content->langue ?? app()->getLocale()) == 'fr' ? 'Fait le ' . $dateCreatedFr : $dateCreatedEn }}</p>
        </div>

        <div class="recipient-address">
            <p>{{ ($content->langue ?? app()->getLocale()) == 'fr' ? 'À:' : 'To:' }}<br />
                <span class="highlight">
                    {{ $data->civility->translate($content->langue, 'fallbackLocale')->libelle ?? '' }}
                    {{ $data->fname . ' ' . $data->lname }}</span><br />
                <span class="highlight">{{ $data->job }}</span><br />
                <span class="highlight">{{ $data->organisation }}</span><br />
                <span
                    class="highlight">{{ ($content->langue ?? app()->getLocale()) == 'fr' ? 'Passeport N°: ' : 'Passport N°: ' }}
                    <strong>{{ $data->passeport_number }}</strong></span><br />
                @if ($data->nationality)
                    <span
                        class="highlight">{{ ($content->langue ?? app()->getLocale()) == 'fr' ? 'Nationalité: ' : 'Nationality: ' }}
                        <strong>{{ $data->nationality->libelle_fr ?? $data->nationality->libelle_en }}</strong></span>
                @endif
            </p>
        </div>

        <div class="content">
            <p><strong>{{ ($content->langue ?? app()->getLocale()) == 'fr' ? 'Objet :' : 'RE :' }}
                    {{ $content->subject ?? '' }}</strong></p>

            {!! $LetterContent !!}

            <br>
            <p>{{ ($content->langue ?? app()->getLocale()) == 'fr' ? 'Cordialement,' : 'Sincerely,' }}</p>
        </div>

        @if ($content->position_cachet == 'center')
            <div class="signature center">
                <img src="/public/storage/{{$content->signature}}" alt="Signature"
                    class="signature-img">
                <p><strong>{{ $content->signatory_name ?? '' }}</strong><br />{!! $content->signatory_job ?? null !!}</p>
            </div>
        @endif
      
        @if ($content->position_cachet == 'right')
            <div class="signature right">
                <img src="/public/storage/{{$content->signature}}" alt="Signature"
                    class="signature-img">
                <p><strong>{{ $content->signatory_name ?? '' }}</strong><br />{!! $content->signatory_job ?? null !!}</p>
            </div>
        @endif

        @if ($content->position_cachet == 'left')
            <div class="signature left">
                <img src="/public/storage/{{$content->signature}}" alt="Signature"
                    class="signature-img">
                <p><strong>{!! $content->signatory_name ?? '' !!}</strong><br />{!! $content->signatory_job ?? 'rien vu' !!}</p>
            </div>
        @endif

    </div>
</body>

</html>
