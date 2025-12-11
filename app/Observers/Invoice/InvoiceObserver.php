<?php

namespace App\Observers\Invoice;

use App\Models\CategorieRegistrant;
use App\Models\Invoice;
use App\Models\JourPassDelegue;
use App\Models\Participant;

class InvoiceObserver
{
    protected $participantService;
    /**
     * Handle the Invoice "created" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "updated" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function restored(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function forceDeleted(Invoice $invoice)
    {
        //
    }

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
