<?php

namespace App\Observers;

use App\Models\CategorieRegistrant;
use App\Models\JourPassDelegue;
use App\Models\Participant;
use App\Services\InvoiceService;

class ParticipantInvoiceObserver
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle the Participant "created" event.
     */
    public function created(Participant $participant)
    {
        /*  if ($participant->diner && $participant->visite) {
            $this->generateOrUpdateInvoice($participant);
        } */
    }

    /**
     * Handle the Participant "updated" event.
     */
    public function updated(Participant $participant)
    {
        /* if ($participant->diner && $participant->visite) {
            $this->generateOrUpdateInvoice($participant);
        } */
    }
    /**
     * Handle the Participant "deleted" event.
     *
     * @param  \App\Models\Participant  $participant
     * @return void
     */
    public function deleted(Participant $participant)
    {
        //dete invoice associate
        $participant->invoices()->delete();
    }

    /**
     * Handle the Participant "restored" event.
     *
     * @param  \App\Models\Participant  $participant
     * @return void
     */
    public function restored(Participant $participant)
    {
        //
    }

    /**
     * Handle the Participant "force deleted" event.
     *
     * @param  \App\Models\Participant  $participant
     * @return void
     */
    public function forceDeleted(Participant $participant)
    {
        //
    }

    /**
     * Génère ou met à jour la facture.
     */
    protected function generateOrUpdateInvoice(Participant $participant)
    {

        $congres_id = $participant->congres_id;
        $dinner = CategorieRegistrant::DinnerforCongress($congres_id);
        $accompanying = CategorieRegistrant::accompanyingPersonForCongress($congres_id);
        $tours = CategorieRegistrant::ToursforCongress($congres_id);
        $deleguate = CategorieRegistrant::deleguateForCongress($congres_id);
        $data = [
            'participant_id' => $participant->id,
            'status' => 'pending',
        ];

        if ($participant->type_participant == 'accompagning') {
            $items = [
                [
                    'description' => app()->getLocale() == 'fr'
                        ? 'Frais d’inscription'
                        : 'Registration fee ',
                    'price' => $accompanying->montant ?? 0
                ],
                [
                    'description' => app()->getLocale() == 'fr' ? 'Diner gala' : 'Gala dinner',
                    'price' => $participant->diner == 'oui' ? $dinner->montant : 0
                ],
                [
                    'description' => app()->getLocale() == 'fr' ? 'Visites techniques' : 'Technical Tours',
                    'price' => $participant->visite == 'oui' ? $tours->montant : 0
                ],
            ];
        } elseif ($participant->type_participant == 'individual') {
            //$categorie = CategorieRegistrant::find($participant->type_member_id);
            //$tarif = $categorie->getTarifForCurrentPeriod($congres_id);
            $pass = JourPassDelegue::whereIn('id', json_decode($participant->deleguate_day))->get();



            $items = [
                
                [
                    'description' => app()->getLocale() == 'fr' ? 'Diner gala' : 'Gala dinner',
                    'price' => $participant->diner == 'oui' ? $dinner->montant : 0
                ],
                [
                    'description' => app()->getLocale() == 'fr' ? 'Visites techniques' : 'Technical Tours',
                    'price' => $participant->visite == 'oui' ? $tours->montant : 0
                ]

            ];

            // Ajouter les passes seulement s'il y en a
            if ($pass->count() > 0) {
                foreach ($pass as $passItem) {
                    $items[] = [
                        'description' => app()->getLocale() == 'fr'
                            ? 'Pass délégué - ' . \Carbon\Carbon::parse($passItem->date)->translatedFormat('d F Y')
                            : 'Delegate pass - ' . $passItem->date->format('F d, Y'),
                        'price' => $deleguate->montant ?? 0
                    ];
                }
            }else {
                $items[] = [
                    'description' => app()->getLocale() == 'fr'
                        ? 'Frais d’inscription - ' . $participant->participantCategory->libelle . ' - ' . $participant->typeMember->libelle
                        : 'Registration fee - ' . $participant->participantCategory->libelle . ' - ' . $participant->typeMember->libelle,
                    'price' => $tarif->montant ?? 'error'
                ];
            }
        } else {

            $categorie = CategorieRegistrant::find($participant->type_member_id);
            //$tarif = $categorie->getTarifForCurrentPeriod($congres_id);
            $items = [
                [
                    'description' => app()->getLocale() == 'fr'
                        ? 'Frais d’inscription - ' . $participant->participantCategory->libelle . ' - ' . $participant->typeMember->libelle
                        : 'Registration fee - ' . $participant->participantCategory->libelle . ' - ' . $participant->typeMember->libelle,
                    'price' => $tarif->montant ?? 'error'
                ],
                [
                    'description' => app()->getLocale() == 'fr' ? 'Diner gala' : 'Gala dinner',
                    'price' => $participant->diner == 'oui' ? $dinner->montant : 0
                ],
                [
                    'description' => app()->getLocale() == 'fr' ? 'Visites techniques' : 'Technical Tours',
                    'price' => $participant->visite == 'oui' ? $tours->montant : 0
                ],
            ];
        }



        $this->invoiceService->createOrUpdateInvoice($data, $items);
    }
}
