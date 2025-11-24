<?php
return [
    'forms' =>
    [
        'header_title' => 'Formulaire d\'inscription individuelle',
        'header_subtitle' => 'Inscrivez-vous facilement en 3 étapes',
    ],
    'choose' => 'Veuillez choisir',
    'step1' => [
        'title' => 'Etape 1',
        'subtitle' => 'Informations générales',
        'label' => 'Informations générales',

        'fields' => [
            'title' => 'Titre',
            'first_name' => 'Prénom',
            'last_name' => 'Nom',
            'education' => "Niveau d'études",
            'gender' => 'Sexe',
            'country' => 'Nationalité',
            'age_range' => 'Tranche d\'Age',
        ],
        'placeholders' => [
            'first_name' => 'Entrez votre prénom',
            'last_name' => 'Entrez votre nom',
            'education' => 'Ex: Étudiant(e) en PhD',
            'country' => 'Entrez votre nationalité',
        ],
        'buttons' => [
            'previous' => 'Précédent',
            'save_continue' => 'Enregistrer et continuer',
        ],
    ],

    'step2' => [
        'title' => 'Etape 2',
        'subtitle' => 'Informations personnelles',
        'label' => 'Renseignements personnels',
        'fields' => [
            'email' => 'Email',
            'telephone' => 'Téléphone',
            'organisation' => 'Entrer le nom de votre Organisation',
            'type_organisation' => 'Type d\'organisation',
            'autre_type_org' => 'Autre type d\'organisation',
            'fonction' => 'Fonction',
            'job_country' => 'Pays de fonction',
        ],
        'placeholders' => [
            'email' => 'Entrez votre email',
            'telephone' => 'Entrez votre numéro de téléphone',
            'organisation' => 'Entrez votre organisation',
            'autre_type_org' => 'Précisez autre type',
            'fonction' => 'Entrez votre fonction',
        ],
        'buttons' => [
            'previous' => 'Précédent',
            'save_continue' => 'Enregistrer et continuer',
        ],
    ],

    'step3' => [
        'title' => 'Etape 3',
        'subtitle' => 'Informations de contact',
        'label' => 'Informations de contact',
        'fields' => [
            'category' => 'Catégorie',
            'membership' => 'Etes-vous membre de AAEA ?',
            'membershipcode' => 'Code membre',
            'diner_gala' => 'Dîner Gala',
            'visite_technical' => 'Visite technique',
            'num_passeport' => 'Numéro Passeport',
            'photo_passeport' => 'Photo Passeport',
            'lettre_invitation' => 'Lettre d\'invitation',
            'auteur' => 'Auteur',
            'oui' => 'Oui',
            'non' => 'Non',
            'day_pass' => 'Passe 1 Jour',
            'choose_pass_dates' => 'Choisir la/les date(s) du pass',
            'no_pass_dates' => 'Aucune date de pass délégué enregistrée.',
            'choose_visit_site' => 'Choisir le site de la visite',
            'current_file' => 'Fichier actuel',
            'upload_to_replace' => 'Télécharger le fichier pour remplacer le fichier actuel',
            'total_to_pay' => 'Total à payer',
            'type_ywp_student' => 'Vous êtes Jeune professionnel(le) ou Etudiant(e) ?',
            'ywp' => 'Jeune professionnel',
            'student' => 'Etudiant',
            'student_card' => 'Joindre votre Carte d\'étudiant',
            'attestation_letter' => 'Joindre votre lettre de confirmation par votre président de réseaux nationale',
            'date_passeport' => 'Date expiration du passeport',
            'date_required' => 'La date expiration du passeport est obligatoire',
            'date_must_be_after' => 'La date expiration du passeport doit être supérieure à la date du congres',
        ],
        'placeholders' => [
            'membershipcode' => 'Entrez le code membre',
            'num_passeport' => 'Entrez le numéro de passeport',
            'date_passeport' => 'Entrez la date d\'expiration du passeport',
        ],
        'buttons' => [
            'previous' => 'Précédent',
            'save_continue' => 'Enregistrer et Terminer',
            'update_continue' => 'Mettre à jour et continuer',
            'update' => 'Mettre à jour et Terminer',

        ],
    ],

    // Boutons et états
    'processing' => 'Traitement en cours...',
    'continue' => 'Continuer',
    'understand' => 'Je comprends',

    // Messages de succès
    'success' => 'Succès',
    'registration_successful' => 'Inscription réussie',
    'data_saved' => 'Données enregistrées avec succès',

    // Messages d'erreur
    'error' => 'Erreur',
    'error_occurred' => 'Une erreur s\'est produite',
    'unknown_error' => 'Une erreur inconnue s\'est produite',
    'server_error' => 'Erreur serveur. Veuillez réessayer.',
    'validation_error' => 'Veuillez corriger les erreurs dans le formulaire',

    // Validation spécifique
    'required_field' => 'Ce champ est obligatoire',
    'invalid_email' => 'L\'adresse email n\'est pas valide',
    'email_exists' => 'Cette adresse email est déjà utilisée',

    // Étapes du processus
    'step_completed' => 'Étape terminée',
    'next_step' => 'Étape suivante',
    'previous_step' => 'Étape précédente',

    // Confirmation
    'confirm_submission' => 'Confirmer l\'envoi',
    'submission_confirmed' => 'Envoi confirmé',

    // Redirection
    'redirecting' => 'Redirection en cours...',
    'please_wait' => 'Veuillez patienter',
    'contactby_email' => 'Contacter par email',
];
