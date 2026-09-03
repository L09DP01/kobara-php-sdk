<?php

namespace Kobara\Resources;

use Kobara\Http\HttpClient;

class Withdrawals {
    private HttpClient $http;

    public function __construct(HttpClient $http) {
        $this->http = $http;
    }

    /**
     * Request a MonCash or NatCash withdrawal.
     */
    public function create(array $payload, ?string $idempotencyKey = null): array {
        $headers = [];
        $headers['Idempotency-Key'] = $idempotencyKey ?: bin2hex(random_bytes(16));

        return $this->http->request('POST', '/withdrawals', $payload, $headers);
    }

}
