<?php
return [
    'choose' => 'Veuillez choisir',
    'step1' => [
        'title' => 'Etape 1',
        'subtitle' => 'Informations personnelles',
        'label' => 'Renseignements personnels',
        'fields' => [
            'title' => 'Titre',
            'first_name' => 'Prénom',
            'last_name' => 'Nom',
            'education' => "Niveau d'études",
            'gender' => 'Sexe',
            'country' => 'Nationalité',
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
        'subtitle' => 'Coordonnées',
        'label' => 'Coordonnées',
        'fields' => [
            'email' => 'Email',
            'telephone' => 'Téléphone',
            'organisation' => 'Organisation',
            'type_organisation' => 'Type d\'organisation',
            'autre_type_org' => 'Autre type d\'organisation',
            'fonction' => 'Fonction',
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
        'subtitle' => 'Détails du congrès',
        'label' => 'Détails du congrès',
        'fields' => [
            'category' => 'Catégorie',
            'membership' => 'Adhésion',
            'membershipcode' => 'Code membre',
            'diner_gala' => 'Dîner Gala',
            'visite_touristique' => 'Visite Touristique',
            'num_passeport' => 'Numéro Passeport',
            'photo_passeport' => 'Photo Passeport',
            'lettre_invitation' => 'Lettre d\'invitation',
            'auteur' => 'Auteur',
            'oui' => 'Oui',
            'non' => 'Non',
        ],
        'placeholders' => [
            'membershipcode' => 'Entrez le code membre',
            'num_passeport' => 'Entrez le numéro de passeport',
        ],
        'buttons' => [
            'previous' => 'Précédent',
            'save_continue' => 'Enregistrer et Terminer',
        ],
    ],

];
