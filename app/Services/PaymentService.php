<?php

namespace App\Services;

use App\Models\Participant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected string $baseUrl;
    protected string $siteId;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.dbs.base_url');
        $this->siteId  = config('services.dbs.site_id');
        $this->apiKey  = config('services.dbs.key');
    }

    /**
     * Initier un paiement
     */
    public function createPayment(array $data): array
    {
        $payload = [
            'amount'              => $data['amount'],
            'site_id'             => $this->siteId,
            'apiKey'              => $this->apiKey,
            'currency'            => $data['currency'] ?? 'EUR',
            'lang'                => $data['lang'] ?? 'fr',
            'transaction_id'      => $data['transaction_id'],
            'customer_surname'    => $data['customer_surname'],
            'customer_name'       => $data['customer_name'],
            'description'         => $data['description'] ?? '',
            'notify_url'          => $data['notify_url'],
            'return_url'          => $data['return_url'],
            'channels'            => $data['channels'] ?? ['CARD'],
            'customer_email'      => $data['customer_email'],
            'customer_phone_number' => $data['customer_phone_number'],
            'customer_address'    => $data['customer_address'] ?? 'Abidjan',
            'customer_city'       => $data['customer_city'] ?? 'Abidjan',
            'customer_country'    => $data['customer_country'] ?? 'CI',
            'customer_state'      => $data['customer_state'] ?? 'CI',
            'customer_zip_code'   => $data['customer_zip_code'] ?? '237',
            'metadata'            => $data['metadata'] ?? null,
        ];

        $response = Http::timeout(30)
            ->acceptJson()

            ->post("{$this->baseUrl}/api/payment", $payload);
        ///Log::info($response);

        if (!$response->successful()) {

            Log::channel('payment')->info('DBS Payment service Error service' . $response);

            return [
                'success' => false,
                'status'  => $response->status(),
                'error'   => $response->body(),
            ];
        }
        $paymentUrl   = $response['data']['payment_url'] ?? null;
        $paymentToken = $response['data']['payment_token'] ?? null;

        $participant = Participant::where('uuid', $data['transaction_id'])->firstOrFail();

        // Mettre à jour la dernière facture
        $participant->invoices()
            ->latest()
            ->first()
            ?->update([
                'token' => $paymentToken
            ]);

        return [
            'success' => true,
            'data'    => $response,
            'url'     => $paymentUrl,
        ];
    }


    public function checkPayment(array $data): array
    {
        $payload = [
            'site_id'        => $this->siteId,
            'apiKey'         => $this->apiKey,
            'transaction_id' => $data['transaction_id'],
        ];

        $response = Http::timeout(30)
            ->acceptJson()
            ->post("{$this->baseUrl}/api/payment/check", $payload);

        if (!$response->successful()) {
            Log::channel('payment')->error('DBS Payment check error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'status'  => $response->status(),
                'error'   => $response->body(),
            ];
        }

        return [
            'success' => true,
            'data'    => $response->json(),
        ];
    }
}
