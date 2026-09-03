<?php

namespace Kobara;

use Kobara\Http\HttpClient;
use Kobara\Resources\Payments;
use Kobara\Resources\Withdrawals;
use Kobara\Resources\Webhooks;

class KobaraClient {
    public Payments $payments;
    public Withdrawals $withdrawals;
    public Webhooks $webhooks;

    private HttpClient $http;

    public function __construct(string $secretKey, ?string $baseUrl = null) {
        if (empty($secretKey)) {
            throw new \InvalidArgumentException("Secret API Key is required to initialize KobaraClient");
        }

        $baseUrl = $baseUrl ?: 'https://api.kobara.app/v1';
        $this->http = new HttpClient($secretKey, $baseUrl);

        // Instantiate resources
        $this->payments = new Payments($this->http);
        $this->withdrawals = new Withdrawals($this->http);
        $this->webhooks = new Webhooks();
    }
}
