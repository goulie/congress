<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

trait MemberApiTrait
{
    /**
     * Récupère les informations d'un membre via l'API
     *
     * @param string $membershipCode
     * @param string $locale
     * @return array
     */
    public function getMemberInfo(string $membershipCode, string $locale = 'fr'): array
    {
        try {
            $apiUrl = config('app.member_api_url', 'http://localhost:8000/api') . '/member/' . $membershipCode;

            $response = Http::timeout(30)
                ->retry(3, 100)
                ->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] ?? false) {
                    return [
                        'success' => true,
                        'data' => $data['data'],
                        'message' => $data['message'] ?? ($locale == 'fr'
                            ? 'Membre trouvé avec succès'
                            : 'Member found successfully')
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? ($locale == 'fr'
                            ? 'Erreur lors de la récupération du membre'
                            : 'Error retrieving member'),
                        'status_code' => $response->status()
                    ];

                    Log::error('Erreur API membre: ' . $data['message'] . ' code d\'erreur: ' . $response->status());
                }
            }

            // Gestion des erreurs HTTP
            return $this->handleApiError($response->status(), $locale);
        } catch (Exception $e) {
            Log::error('Erreur API membre: ' . $e->getMessage(), [
                'membership_code' => $membershipCode,
                'locale' => $locale
            ]);

            return [
                'success' => false,
                'message' => $locale == 'fr'
                    ? "Le code membre saisi est inexistant.<br/> Veuillez contacter le service des membres de l'AAEA par e-mail (mlawson@afwasa.org) pour:<br/> - Adhérer à l'AAEA. <br/> - Signaler un problème si vous êtes déjà membre. <br/> Ou veuillez-vous inscrire en tant que non-membre."
                    : "The membership code entered does not exist.<br/> Please contact AfWASA membership department by email (mlawson@afwasa.org) to : <br/> Join AfWASA. <br/> Report a problem if you are already a member. <br/> Or sign up as a non-member.",
                'error' => config('app.debug') ? $e->getMessage() : null,
                'member_status' => 'inexistant'
            ];
        }
    }

    /**
     * Vérifie si le membre est à jour de ses cotisations
     *
     * @param string $membershipCode
     * @param string $locale
     * @return array
     */
    public function checkMemberSubscription(string $membershipCode, string $locale = 'fr'): array
    {
        $memberInfo = $this->getMemberInfo($membershipCode, $locale);

        if (!$memberInfo['success']) {

            return $memberInfo;
        }

        // Vérifier si le membre est actif et à jour
        $member = $memberInfo['data']['member'] ?? $memberInfo['data'];
        /* 
    'nouptodat' => "Vous ne pouvez pas vous inscrire à la réunion car votre entreprise n'est pas à jour de ses cotisations. Veuillez contacter le service financier de l'AAEA par e-mail pour régulariser votre situation:",

    */
        if ($member['status'] !== 'actif') {

            if ($member['status'] === 'inactif') {
                return [
                    'success' => false,
                    'message' => $locale == 'fr' ? "Ce tarif ne peut vous être accordé, car ce matricule membre n’est pas à jour de ses cotisations. Vous pouvez soit contacter le service financier de l’AAEA (snguesssan@afwasa.org) pour régulariser votre situation, soit utiliser le tarif non-membre pour vous inscrire."
                        : "This rate cannot be granted to you because this membership code is not up-to-date with their subscriptions. You can either contact the AfWASA Finance department (snguesssan@afwasa.org) to regularize your situation, or use the non-member rate to register.",
                    'member_status' => $member['status']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $locale == 'fr' ? "Vous ne pouvez pas bénéficier de ce tarif, car ce code est inexistant. Veuillez contacter le service financier de l’AAEA par e-mail (mlawson@afwasa.org) pour adhérer en tant que membre de l’AAEA."
                        : "You cannot benefit from this rate because this code is invalid. Please contact the AfWASA Finance department (mlawson@afwasa.org) to become a member of AfWASA.",
                    'member_status' => $member['status']
                ];
            }
        }

        // Ajouter d'autres vérifications de cotisation si nécessaire
        if (isset($member['subscription_status']) && $member['subscription_status'] !== 'paid') {
            return [
                'success' => false,
                'message' => $locale == 'fr'
                    ? 'Le membre doit être à jour de ses cotisations.'
                    : 'The member must be up to date with their subscriptions.',
                'subscription_status' => $member['subscription_status']
            ];
        }

        return [
            'success' => true,
            'message' => $locale == 'fr'
                ? 'Membre à jour de ses cotisations.'
                : 'Member is up to date with subscriptions.',
            'data' => $memberInfo['data']
        ];
    }

    /**
     * Gère les erreurs de l'API
     *
     * @param int $statusCode
     * @param string $locale
     * @return array
     */
    private function handleApiError(int $statusCode, string $locale): array
    {
        $messages = [
            404 => $locale == 'fr' ? 'Membre non trouvé' : 'Member not found',
            403 => $locale == 'fr' ? 'Membre inactif' : 'Member inactive',
            422 => $locale == 'fr'
                ? 'Le membre doit être à jour de ses cotisations.'
                : 'The member must be up to date with their subscriptions.',
            500 => $locale == 'fr' ? 'Erreur interne du serveur' : 'Internal server error',
            503 => $locale == 'fr' ? 'Service indisponible' : 'Service unavailable',
        ];

        return [
            'success' => false,
            'message' => $messages[$statusCode] ?? ($locale == 'fr'
                ? 'Erreur inconnue'
                : 'Unknown error'),
            'status_code' => $statusCode
        ];
    }
}
