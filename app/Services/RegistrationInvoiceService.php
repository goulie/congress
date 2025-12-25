<?php

namespace App\Services;

use App\Models\CategorieRegistrant;
use App\Models\Congress;
use App\Models\Invoice;
use App\Models\JourPassDelegue;
use App\Models\Participant;
use App\Models\StudentYwpValidation;
use App\Models\User;
use App\Notifications\StudentOrYwpregistrantNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegistrationInvoiceService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Créer ou mettre à jour la facture pour un participant
     * (Utilisable depuis n'importe quel controller)
     */
    public function processRegistration(Participant $participant, bool $isUpdate = false): array
    {
        try {
            DB::beginTransaction();

            // 1. Générer la facture
            $invoice = $this->generateInvoice($participant);

            // 2. Envoyer les emails appropriés
            $this->sendRegistrationEmails($participant, $invoice);

            // 3. Gérer les notifications admin si nécessaire
            $this->handleAdminNotifications($participant);

            DB::commit();

            return [
                'success' => true,
                'message' => $isUpdate
                    ? ($participant->langue == 'fr'
                        ? 'Inscription mise à jour avec succès'
                        : 'Registration updated successfully')
                    : ($participant->langue == 'fr'
                        ? 'Inscription créée avec succès'
                        : 'Registration created successfully'),
                'participant' => $participant,
                'invoice' => $invoice,
            ];
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Erreur lors du traitement de l\'inscription: ' . $ex->getMessage());

            return [
                'success' => false,
                'message' => $participant->langue == 'fr'
                    ? 'Erreur lors du traitement de l\'inscription'
                    : 'Error processing registration',
                'error' => $ex->getMessage(),
            ];
        }
    }

    /**
     * Générer ou mettre à jour la facture
     */
    public function generateInvoice(Participant $participant): ?Invoice
    {
        try {
            $congres = Congress::latest()->first();
            $congresId = $participant->congres_id;

            // Récupérer tous les tarifs nécessaires
            $tarifs = $this->getAllTarifs($congresId);

            // Vérifier si une facture existe déjà
            $invoice = Invoice::where('participant_id', $participant->id)->first();

            if ($invoice) {
                // Mise à jour de la facture existante
                $invoice->update([
                    'currency' => $congres->currency ?? 'FCFA',
                    'status' => Invoice::PAYMENT_STATUS_UNPAID,
                    'invoice_date' => Carbon::now(),
                ]);

                // Supprimer les anciens items
                $invoice->items()->delete();
            } else {
                // Création d'une nouvelle facture
                $invoiceNumber = $this->generateInvoiceNumber($congres);

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => $participant->user_id ?? auth()->id(),
                    'participant_id' => $participant->id,
                    'currency' => $congres->currency ?? 'FCFA',
                    'status' => Invoice::PAYMENT_STATUS_UNPAID,
                    'invoice_date' => Carbon::now(),
                    'congres_id' => $congresId,
                ]);
            }

            // Générer les items de facture selon le type de participant
            $items = $this->generateInvoiceItems($participant, $tarifs);

            // Ajouter les items à la facture
            foreach ($items as $item) {
                $invoice->items()->create([
                    'description_fr' => $item['description'],
                    'price' => $item['price'],
                    'currency' => $congres->currency ?? 'FCFA',
                    'tarif_id' => $item['tarif_id'] ?? null,
                ]);
            }

            // Calculer et mettre à jour le total
            $total = $invoice->items->sum('price');
            $invoice->update(['total_amount' => $total]);

            return $invoice->fresh(['items']);
        } catch (\Exception $ex) {
            Log::error('Erreur génération facture: ' . $ex->getMessage());
            return null;
        }
    }

    /**
     * Récupérer tous les tarifs nécessaires
     */
    protected function getAllTarifs($congresId): array
    {
        return [
            'dinner' => CategorieRegistrant::DinnerforCongress($congresId),
            'dinnerNonMember' => CategorieRegistrant::DinnerNonMemberforCongress($congresId),
            'tours' => CategorieRegistrant::ToursforCongress($congresId),
            'deleguate' => CategorieRegistrant::deleguateForCongress($congresId),
            'student_ywp' => CategorieRegistrant::studentForCongress($congresId),
            'non_member' => CategorieRegistrant::NonMemberPriceforCongress($congresId),
            'passDeleguate' => CategorieRegistrant::PassDeleguateforCongress($congresId),
        ];
    }

    /**
     * Générer les items de facture selon le type de participant
     */
    protected function generateInvoiceItems(Participant $participant, array $tarifs): array
    {
        $locale = $participant->langue;
        $type = $participant->type_participant;
        $categoryId = $participant->participant_category_id;

       

        // DÉLÉGUÉ
        if ($categoryId == 1) {
            return $this->getDelegateItems($participant, $tarifs, $locale);
        }

        // ÉTUDIANT/YWP
        if ($categoryId == 4) {
            return $this->getStudentItems($participant, $tarifs, $locale);
        }

        // AUTRES CATÉGORIES
        return $this->getOtherCategoryItems($participant, $tarifs, $locale);
    }

    

    /**
     * Items pour délégué
     */
    protected function getDelegateItems(Participant $participant, array $tarifs, string $locale): array
    {
        $items = [];
        $hasPassDay = $this->hasPassDays($participant);

        // TARIF PRINCIPAL
        if ($hasPassDay) {
            $items = array_merge($items, $this->getPassDayItems($participant, $tarifs, $locale));
        } else {
            $items[] = $this->getDelegateRegistrationItem($participant, $tarifs, $locale);
        }

        // Options
        $items[] = $this->getDinnerItem($participant, $tarifs, $locale, $hasPassDay);
        $items[] = $this->getTourItem($participant, $tarifs, $locale);

        return array_filter($items);
    }

    /**
     * Items pour étudiant/YWP
     */
    protected function getStudentItems(Participant $participant, array $tarifs, string $locale): array
    {
        $items = [];

        // Frais d'inscription étudiant
        $items[] = [
            'description' => $locale == 'fr'
                ? 'Frais d\'inscription - Étudiant/YWP'
                : 'Registration fee - Student/YWP',
            'price' => $tarifs['student_ywp']->montant ?? 0,
            'tarif_id' => $tarifs['student_ywp']->tarif_id ?? null
        ];

        // Options
        $items[] = $this->getDinnerItem($participant, $tarifs, $locale, false);
        $items[] = $this->getTourItem($participant, $tarifs, $locale);

        return array_filter($items);
    }

    /**
     * Items pour autres catégories
     */
    protected function getOtherCategoryItems(Participant $participant, array $tarifs, string $locale): array
    {
        $items = [];

        // Frais de base (à adapter selon vos besoins)
        $items[] = [
            'description' => $locale == 'fr'
                ? 'Frais d\'inscription'
                : 'Registration fee',
            'price' => 0,
            'tarif_id' => null
        ];

        // Options
        $items[] = $this->getDinnerItem($participant, $tarifs, $locale, false);
        $items[] = $this->getTourItem($participant, $tarifs, $locale);

        return array_filter($items);
    }

    /**
     * Vérifier si le participant a des jours de pass
     */
    protected function hasPassDays(Participant $participant): bool
    {
        if (empty($participant->deleguate_day)) {
            return false;
        }

        $decoded = json_decode($participant->deleguate_day, true);
        return is_array($decoded) && count($decoded) > 0;
    }

    /**
     * Obtenir les items pour les jours de pass
     */
    protected function getPassDayItems(Participant $participant, array $tarifs, string $locale): array
    {
        $items = [];
        $passIds = json_decode($participant->deleguate_day, true) ?: [];

        $passDays = JourPassDelegue::whereIn('id', $passIds)->get();

        foreach ($passDays as $day) {
            $items[] = [
                'description' => $locale === 'fr'
                    ? 'Pass délégué - ' . Carbon::parse($day->date)->translatedFormat('d F Y')
                    : 'Delegate day pass - ' . $day->date,
                'price' => $tarifs['passDeleguate']->montant ?? 0,
                'tarif_id' => $tarifs['passDeleguate']->tarif_id ?? null,
            ];
        }

        return $items;
    }

    /**
     * Obtenir l'item d'inscription délégué
     */
    protected function getDelegateRegistrationItem(Participant $participant, array $tarifs, string $locale): array
    {
        if ($participant->membre_aae === 'oui') {
            return [
                'description' => $locale === 'fr'
                    ? 'Frais d\'inscription - Délégué'
                    : 'Registration fee - Delegate',
                'price' => $tarifs['deleguate']->montant ?? 0,
                'tarif_id' => $tarifs['deleguate']->tarif_id ?? null,
            ];
        }

        return [
            'description' => $locale === 'fr'
                ? 'Frais d\'inscription - Délégué (Non-membre)'
                : 'Registration fee - Delegate (Non-member)',
            'price' => $tarifs['non_member']->montant ?? 0,
            'tarif_id' => $tarifs['non_member']->tarif_id ?? null,
        ];
    }

    /**
     * Obtenir l'item dîner
     */
    protected function getDinnerItem(Participant $participant, array $tarifs, string $locale, bool $hasPassDay): ?array
    {
        if ($participant->diner !== 'oui') {
            return null;
        }

        if ($hasPassDay) {
            return [
                'description' => $locale === 'fr'
                    ? 'Diner gala (Pass journalier)'
                    : 'Gala dinner (Day pass)',
                'price' => $tarifs['dinnerNonMember']->montant ?? 0,
                'tarif_id' => $tarifs['dinnerNonMember']->tarif_id ?? null,
            ];
        }

        return [
            'description' => $locale === 'fr'
                ? 'Diner gala'
                : 'Gala dinner',
            'price' => $tarifs['dinner']->montant ?? 0,
            'tarif_id' => $tarifs['dinner']->tarif_id ?? null,
        ];
    }

    /**
     * Obtenir l'item visite technique
     */
    protected function getTourItem(Participant $participant, array $tarifs, string $locale): ?array
    {
        if ($participant->visite !== 'oui') {
            return null;
        }

        return [
            'description' => $locale === 'fr'
                ? 'Visites techniques'
                : 'Technical tours',
            'price' => $tarifs['tours']->montant ?? 0,
            'tarif_id' => $tarifs['tours']->tarif_id ?? null,
        ];
    }

    /**
     * Envoyer les emails d'inscription
     */
    protected function sendRegistrationEmails(Participant $participant, ?Invoice $invoice): void
    {
        try {
            // 1. Email de confirmation d'inscription
            $this->emailService->sendRegistrationConfirmation($participant);

            // 2. Email de facture pour les délégués
            if ($participant->participant_category_id == 1 && $invoice) {
                $this->emailService->SendInvoiceEmail($invoice);
            }

            // 3. Email spécial pour étudiants/YWP (optionnel)
            /* if ($participant->participant_category_id == 4) {
                $this->emailService->sendStudentConfirmation($participant);
            } */
        } catch (\Exception $ex) {
            Log::error('Erreur envoi emails: ' . $ex->getMessage());
        }
    }

    /**
     * Gérer les notifications admin
     */
    protected function handleAdminNotifications(Participant $participant): void
    {
        // Notifications pour étudiants/YWP
        if ($participant->ywp_or_student == 'ywp' || $participant->ywp_or_student == 'student') {
            $admins = User::where('role_id', 6)->get();

            foreach ($admins as $admin) {
                $admin->notify(new StudentOrYwpregistrantNotification($participant));
            }

            // Enregistrer la validation en attente
            $participant->validation_ywp_students()->updateOrCreate(
                ['participant_id' => $participant->id],
                ['status' => StudentYwpValidation::STATUS_PENDING]
            );
        }
    }

    /**
     * Générer un numéro de facture unique
     */
    protected function generateInvoiceNumber(Congress $congres): string
    {
        $year = Carbon::parse($congres->end_date)->year;
        $random = strtoupper(Str::random(6));

        return "INV{$year}-{$random}";
    }

    /**
     * Vérifier si la facture doit être regénérée
     */
    public function shouldRegenerateInvoice(Participant $participant): bool
    {
        $billingFields = [
            'participant_category_id',
            'type_member_id',
            'diner',
            'visite',
            'deleguate_day',
            'pass_deleguate',
            'membre_aae'
        ];

        foreach ($billingFields as $field) {
            if ($participant->wasChanged($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Supprimer la facture d'un participant
     */
    public function deleteInvoice(Participant $participant): bool
    {
        try {
            $participant->invoices()->delete();
            return true;
        } catch (\Exception $ex) {
            Log::error('Erreur suppression facture: ' . $ex->getMessage());
            return false;
        }
    }
}
