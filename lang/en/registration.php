<?php
return [
    'forms' =>
    [
        'header_title' => 'Individual Registration Form',
        'header_subtitle' => 'Register easily in 3 steps',
    ],
    'choose' => 'Please choose',
    'step1' => [
        'subtitle' => 'General Informations',
        'label' => 'General Informations',
        'fields' => [
            'title' => 'Title',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'education' => 'Education Level',
            'gender' => 'Gender',
            'country' => 'Nationality',
            'age_range' => 'Age Range',

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
            'update_continue' => 'Update and Continue',
            'update' => 'Update and Save',
            'save' => 'Save my information',
        ],
    ],

    'step2' => [
        'title' => 'Step 2',
        'subtitle' => 'Personal Information',
        'label' => 'Personal Details',

        'fields' => [
            'email' => 'Email',
            'telephone' => 'Telephone',
            'organisation' => 'Enter your Organisation name',
            'type_organisation' => 'Type of Organisation',
            'autre_type_org' => 'Other Type of Organisation',
            'fonction' => 'Position',
            'job_country' => 'Job Country',
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
        'subtitle' => 'Contact Details',
        'label' => 'Contact Information',
        'fields' => [
            'category' => 'Category',
            'membership' => 'Are you a member of AfWASA ?',
            'membershipcode' => 'Membership Code',
            'diner_gala' => 'Gala Dinner',
            'visite_technical' => 'Tecnical Visit',
            'num_passeport' => 'Passport Number',
            'photo_passeport' => 'Passport Photo',
            'lettre_invitation' => 'Invitation Letter',
            'auteur' => 'Author',
            'oui' => 'Yes',
            'non' => 'No',
            'day_pass' => '1-Day Pass',
            'choose_pass_dates' => 'Choose pass date(s)',
            'no_pass_dates' => 'No delegate pass dates registered.',
            'choose_visit_site' => 'Choose visit site',
            'current_file' => 'Current file',
            'upload_to_replace' => 'Upload file to replace current file',
            'total_to_pay' => 'Total to pay',
            'type_ywp_student' => 'You are YWP or Student ?',
            'ywp' => 'Young Water Professional',
            'student' => 'Student',
            'student_card' => 'Join your student ID card',
            'attestation_letter' => 'Join your attestation letter by your national network president',
            'date_passeport' => 'Passport Expiration Date',
            'date_required' => 'Passport expiration date is required',
            'date_must_be_after' => 'Passport expiration date must be after congress date',

        ],
        'placeholders' => [
            'membershipcode' => 'Enter membership code',
            'num_passeport' => 'Enter passport number',
            'date_passeport' => 'Enter passport expiration date',
        ],
        'buttons' => [
            'previous' => 'Previous',
            'save_continue' => 'Save and Continue',
            'update_continue' => 'Update and Continue',
            'update' => 'Update and Save',
            'save' => 'Save my information',
        ],

    ],

    // Buttons and states
    'processing' => 'Processing...',
    'continue' => 'Continue',
    'understand' => 'I understand',

    // Success messages
    'success' => 'Success',
    'registration_successful' => 'Registration successful',
    'data_saved' => 'Data saved successfully',

    // Error messages
    'error' => 'Error',
    'error_occurred' => 'An error occurred',
    'unknown_error' => 'An unknown error occurred',
    'server_error' => 'Server error. Please try again.',
    'validation_error' => 'Please correct the errors in the form',

    // Specific validation
    'required_field' => 'This field is required',
    'invalid_email' => 'The email address is not valid',
    'email_exists' => 'This email address is already in use',

    // Process steps
    'step_completed' => 'Step completed',
    'next_step' => 'Next step',
    'previous_step' => 'Previous step',

    // Confirmation
    'confirm_submission' => 'Confirm submission',
    'submission_confirmed' => 'Submission confirmed',

    // Redirection
    'redirecting' => 'Redirecting...',
    'please_wait' => 'Please wait',

    // Contact
    'contactby_email' => 'Contact by email',
    'fields_required' => 'All fields marked with <span class="text-danger font-weight-bold">*</span> are required ', //'Tous les champs marqués par <span class="text-danger font-weight-bold">*</span> sont obligatoires',
    'sigle' => 'Acronym of the organization',
    'maj_only' => 'Only in capital letters',
    'caracteres' => 'characters',
];
