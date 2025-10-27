<?php
return [

    'choose' => 'Please choose',
    'step1' => [
        'title' => 'Step 1',
        'subtitle' => 'Personal Information',
        'label' => 'Personal Details',
        'fields' => [
            'title' => 'Title',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'education' => 'Education Level',
            'gender' => 'Gender',
            'country' => 'Nationality',
        ],
        'placeholders' => [
            'first_name' => 'Enter your first name',
            'last_name' => 'Enter your last name',
            'education' => 'Ex: PhD Student',
            'country' => 'Enter your nationality',
        ],
        'buttons' => [
            'previous' => 'Previous',
            'save_continue' => 'Save and Continue',
        ],
    ],

    'step2' => [
        'title' => 'Step 2',
        'subtitle' => 'Contact Details',
        'label' => 'Contact Information',
        'fields' => [
            'email' => 'Email',
            'telephone' => 'Telephone',
            'organisation' => 'Organisation',
            'type_organisation' => 'Type of Organisation',
            'autre_type_org' => 'Other Type of Organisation',
            'fonction' => 'Position',
        ],
        'placeholders' => [
            'email' => 'Enter your email',
            'telephone' => 'Enter your phone number',
            'organisation' => 'Enter your organisation',
            'autre_type_org' => 'Specify other type',
            'fonction' => 'Enter your position',
        ],
        'buttons' => [
            'previous' => 'Previous',
            'save_continue' => 'Save and Continue',
        ],
    ],

    'step3' => [
        'title' => 'Step 3',
        'subtitle' => 'Congress Details',
        'label' => 'Congress Information',
        'fields' => [
            'category' => 'Category',
            'membership' => 'Membership',
            'membershipcode' => 'Membership Code',
            'diner_gala' => 'Gala Dinner',
            'visite_touristique' => 'Tourist Visit',
            'num_passeport' => 'Passport Number',
            'photo_passeport' => 'Passport Photo',
            'lettre_invitation' => 'Invitation Letter',
            'auteur' => 'Author',
            'oui' => 'Yes',
            'non' => 'No',
        ],
        'placeholders' => [
            'membershipcode' => 'Enter membership code',
            'num_passeport' => 'Enter passport number',
        ],
        'buttons' => [
            'previous' => 'Previous',
            'save_continue' => 'Save and Continue',
        ],
    ],

];
