<?php

namespace Kobara\Resources;

use Kobara\Http\HttpClient;

class Withdrawals {
    private HttpClient $http;

    public function __construct(HttpClient $http) {
        $this->http = $http;
    }

    /**
     * Request a manual withdrawal
     */
    public function create(array $payload, ?string $idempotencyKey = null): array {
        $headers = [];
        if ($idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        return $this->http->request('POST', '/withdrawals', $payload, $headers);
    }

    /**
     * Retrieve a specific withdrawal by ID
     */
    public function retrieve(string $id): array {
        return $this->http->request('GET', "/withdrawals/{$id}");
    }
}
