<?php

namespace App\Observers;

use App\Models\CategorieRegistrant;
use App\Models\Invoice;
use App\Models\JourPassDelegue;
use App\Models\Participant;
use App\Models\StudentYwpValidation;
use App\Models\User;
use App\Notifications\StudentOrYwpregistrantNotification;
use App\Services\EmailService;
use App\Services\SingleRegistrantInvoice;
use Illuminate\Support\Facades\Log;

class SingleRegitrantInvoiceObserver
{
    protected $SingleRegistrantInvoice, $emailService;

    public function __construct(SingleRegistrantInvoice $SingleRegistrantInvoice, EmailService $emailService)
    {
        $this->SingleRegistrantInvoice = $SingleRegistrantInvoice;
        $this->emailService = $emailService;
    }

    /**
     * Handle the Participant "created" event.
     */
    public function created(Participant $participant)
    {
        try {

            $this->generateOrUpdateInvoice($participant);

            //Envoi de l'email de confirmation
            $this->emailService->sendRegistrationConfirmation($participant);

            //Envoi de l'email de avec aux délégues facture
            if ($participant->participant_category_id == 1) {
                $this->emailService->SendInvoiceEmail($participant->invoices()->first());
            }
        } catch (\Exception $ex) {
            Log::error('SingleRegitrantInvoiceObserver created error: ' . $ex->getMessage());
        }
    }

    /**
     * Handle the Participant "updated" event.
     */
    public function updated(Participant $participant)
    {
        try {

            // Vérifier si les champs liés à la facturation ont changé
            if ($this->hasBillingChanges($participant)) {
                $this->generateOrUpdateInvoice($participant);
                //Envoi de l'email de avec facture
                if ($participant->participant_category_id == 1) {
                    $this->emailService->SendInvoiceEmail($participant->invoices()->first());
                }
            }

            //Envoi de l'email de confirmation
            $emailSent = $this->emailService->sendRegistrationConfirmation($participant);
        } catch (\Exception $ex) {
            Log::error('SingleRegitrantInvoiceObserver updated error: ' . $ex->getMessage());
        }
    }

    /**
     * Handle the Participant "deleted" event.
     */
    public function deleted(Participant $participant)
    {
        // Supprimer les factures associées

        $participant->invoices()->items()->delete();
        $participant->invoices()->delete();
    }

    /**
     * Handle the Participant "restored" event.
     */
    public function restored(Participant $participant)
    {
        //
    }

    /**
     * Handle the Participant "force deleted" event.
     */
    public function forceDeleted(Participant $participant)
    {
        //
    }

    /**
     * Vérifie si les champs liés à la facturation ont changé
     */
    protected function hasBillingChanges(Participant $participant)
    {
        return $participant->isDirty([
            'participant_category_id',
            'type_member_id',
            'diner',
            'visite',
            'deleguate_day',
            'pass_deleguate',
            'membre_aae'
        ]);
    }

    /**
     * Génère ou met à jour la facture.
     */
    protected function generateOrUpdateInvoice(Participant $participant)
    {
        $congres_id = $participant->congres_id;
        $dinner = CategorieRegistrant::DinnerforCongress($congres_id);
        $tours = CategorieRegistrant::ToursforCongress($congres_id);
        $deleguate = CategorieRegistrant::deleguateForCongress($congres_id);
        $student_ywp = CategorieRegistrant::studentForCongress($congres_id);
        $non_member = CategorieRegistrant::NonMemberPriceforCongress($congres_id);
        $passDeleguate = CategorieRegistrant::PassDeleguateforCongress($congres_id);

        $data = [
            'participant_id' => $participant->id,
            'status' => Invoice::PAYMENT_STATUS_UNPAID,
        ];

        $items = [];

        // FACTURATION POUR ACCOMPAGNATEUR
        if ($participant->type_participant == 'accompagning') {
            $items = [
                [
                    'description' => $participant->langue == 'fr'
                        ? 'Frais d\'inscription - Accompagnateur'
                        : 'Registration fee - Accompanying person',
                    'price' => $accompanying->montant ?? 0
                ],
                [
                    'description' => $participant->langue == 'fr' ? 'Diner gala' : 'Gala dinner',
                    'price' => $participant->diner == 'oui' ? $dinner->montant : 0
                ],
                [
                    'description' => $participant->langue == 'fr' ? 'Visites techniques' : 'Technical Tours',
                    'price' => $participant->visite == 'oui' ? $tours->montant : 0
                ],
            ];
        }

        // FACTURATION POUR PARTICIPANT INDIVIDUEL (DÉLÉGUÉ/ÉTUDIANT)
        elseif ($participant->type_participant == 'individual') {

            // Récupérer la catégorie du participant
            $categoryId = $participant->participant_category_id;

            // DÉLÉGUÉ (category_id = 1)
            if ($categoryId == 1) {
                $items = $this->getDelegateItems($participant, $deleguate, $non_member, $dinner, $tours, $passDeleguate);
            }
            // ÉTUDIANT/YWP (category_id = 4)
            elseif ($categoryId == 4) {
                $items = $this->getStudentItems($participant, $student_ywp, $student_ywp_member = null, $dinner, $tours);
            } else {
                $items = $this->getOtherCategoryItems($participant, $dinner, $tours);
            }
        } elseif ($participant->type_participant == 'grouped') {

            // Récupérer la catégorie du participant
            $categoryId = $participant->participant_category_id;

            // DÉLÉGUÉ (category_id = 1)
            if ($categoryId == 1) {
                $items = $this->getDelegateItems($participant, $deleguate, $non_member, $dinner, $tours, $passDeleguate);
            } elseif ($categoryId == 4) {
                $isMember = false;
                if ($participant->membership == 'oui' && isset($participant->membership_code)) {

                    $isMember = true;
                }
                $items = $this->getStudentItems($participant, $student_ywp, $student_ywp_member = null, $dinner, $tours, $isMember);
            } else {
                $items = $this->getOtherCategoryItems($participant, $dinner, $tours);
            }
        } else {
            $categorie = CategorieRegistrant::find($participant->type_member_id);
            $items = [
                [
                    'description' => $participant->langue == 'fr'
                        ? 'Frais d\'inscription - ' . $participant->participantCategory->libelle
                        : 'Registration fee - ' . $participant->participantCategory->libelle,
                    'price' => $categorie->montant ?? 0
                ],
                [
                    'description' => $participant->langue == 'fr' ? 'Diner gala' : 'Gala dinner',
                    'price' => $participant->diner == 'oui' ? $dinner->montant : 0
                ],
                [
                    'description' => $participant->langue == 'fr' ? 'Visites techniques' : 'Technical Tours',
                    'price' => $participant->visite == 'oui' ? $tours->montant : 0
                ],
            ];
        }

        $this->SingleRegistrantInvoice->createOrUpdateInvoice($data, $items);
    }

    /**
     * Items de facturation pour les DÉLÉGUÉS
     */
    protected function getDelegateItems($participant, $deleguate, $non_member, $dinner, $tours, $passDeleguate)
    {
        $items = [];
        // Gestion sécurisée de deleguate_day
        $deleguateDay = $participant->deleguate_day;
        $passIds = [];

        if (!empty($deleguateDay)) {
            $decoded = json_decode($deleguateDay, true);
            if (is_array($decoded) && !empty($decoded)) {
                $passIds = $decoded;
            }
        }

        $pass = JourPassDelegue::whereIn('id', $passIds)->get();

        if ($pass->count() > 0) {
            // Facturer chaque pass journalier sélectionné
            foreach ($pass as $passItem) {


                $items[] = [
                    'description' => $participant->langue == 'fr'
                        ? 'Pass délégué - ' . \Carbon\Carbon::parse($passItem->date)->translatedFormat('d F Y')
                        : 'Delegate pass - ' . $passItem->date,
                    'price' => $passDeleguate->montant ?? 0,
                    'tarif_id' => $passDeleguate->tarif_id ?? 0
                ];
            }

            /* // Ajouter supplément non-membre si applicable
            if ($participant->membre_aae == 'non' || $participant->isMember == false) {
                $items[] = [
                    'description' => $participant->langue == 'fr'
                        ? 'Frais d\'inscription - Délégué'
                        : 'Registration fee - Delegate',
                    'price' => $non_member->montant ?? 0
                ];
            } */
        } else {


            // Facturer si non-membre
            if ($participant->membre_aae == 'non') {

                // Frais de base délégué
                $items[] = [
                    'description' => $participant->langue == 'fr'
                        ? 'Frais d\'inscription - Délégué ' . ($pass->count() > 0 ? '' : 'non-membre')
                        : 'Registration fee - Delegate ' . ($pass->count() > 0 ? '' : 'non-member'),
                    'price' => $non_member->montant ?? 0,
                    'tarif_id' => $non_member->tarif_id ?? 0
                ];
            } else {
                $items[] = [
                    'description' => $participant->langue == 'fr'
                        ? 'Frais d\'inscription - Délégué'
                        : 'Registration fee - Delegate',
                    'price' => $deleguate->montant ?? 0,
                    'tarif_id' => $deleguate->tarif_id ?? 0
                ];
            }

            // Pass délégué global (si oui mais pas de dates spécifiques)
            /* if ($participant->pass_deleguate == 'oui') {
                $items[] = [
                    'description' => $participant->langue == 'fr'
                        ? 'Pass délégué '
                        : 'Delegate pass ',
                    'price' => $passDeleguate->montant ?? 0
                ];
            } */
        }

        // Ajouter les options
        $items[] = [
            'description' => $participant->langue == 'fr' ? 'Diner gala' : 'Gala dinner',
            'price' => $participant->diner == 'oui' ? $dinner->montant : 0,
            'tarif_id' => $dinner->tarif_id ?? 0
        ];

        $items[] = [
            'description' => $participant->langue == 'fr' ? 'Visites techniques' : 'Technical Tours',
            'price' => $participant->visite == 'oui' ? $tours->montant : 0,
            'tarif_id' => $tours->tarif_id ?? 0
        ];

        return $items;
    }

    /**
     * Items de facturation pour les ÉTUDIANTS/YWP
     */
    protected function getStudentItems($participant, $student_ywp, $student_ywp_member, $dinner, $tours)
    {
        $items = [];

        // Détection correcte du statut membre
        $isMember = ($participant->membre_aae === 'oui');

        // Frais d'inscription
        $items[] = [
            'description' => $participant->langue == 'fr'
                ? 'Frais d\'inscription - Étudiant/YWP' /* . ($isMember ? ' - Membre' : '') */
                : 'Registration fee - Student/YWP' /* . ($isMember ? ' - Member' : '') */,
            'price' => $student_ywp->montant ?? 0,
            'tarif_id' => $student_ywp->tarif_id ?? 0
        ];

        // Dîner
        $items[] = [
            'description' => $participant->langue == 'fr' ? 'Diner gala' : 'Gala dinner',
            'price' => $participant->diner == 'oui' ? $dinner->montant : 0,
            'tarif_id' => $dinner->tarif_id ?? 0
        ];

        // Visites techniques
        $items[] = [
            'description' => $participant->langue == 'fr' ? 'Visites techniques' : 'Technical Tours',
            'price' => $participant->visite == 'oui' ? $tours->montant : 0,
            'tarif_id' => $tours->tarif_id ?? 0
        ];

        return $items;
    }


    /**
     * Items de facturation pour les autres catégories
     */
    protected function getOtherCategoryItems($participant, $dinner, $tours)
    {
        $items = [];

        // Frais de base selon la catégorie
        $items[] = [
            'description' => $participant->langue == 'fr'
                ? 'Frais d\'inscription - ' . ($participant->participantCategory->libelle ?? 'Autre')
                : 'Registration fee - ' . ($participant->participantCategory->libelle ?? 'Other'),
            'price' => 0 // À adapter selon votre logique
        ];

        // Ajouter les options
        $items[] = [
            'description' => $participant->langue == 'fr' ? 'Diner gala' : 'Gala dinner',
            'price' => $participant->diner == 'oui' ? $dinner->montant : 0,
            'tarif_id' => $participant->diner == 'oui' ? $dinner->tarif_id : null,

        ];

        $items[] = [
            'description' => $participant->langue == 'fr' ? 'Visites techniques' : 'Technical Tours',
            'price' => $participant->visite == 'oui' ? $tours->montant : 0,
            'tarif_id' => $tours->tarif_id ?? 0
        ];

        return $items;
    }
}
