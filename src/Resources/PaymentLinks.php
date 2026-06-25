<?php

namespace Kobara\Resources;

use Kobara\Http\HttpClient;

class PaymentLinks {
    private HttpClient $http;

    public function __construct(HttpClient $http) {
        $this->http = $http;
    }

    /**
     * Create a new payment link
     */
    public function create(array $payload, ?string $idempotencyKey = null): array {
        $headers = [];
        if ($idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        return $this->http->request('POST', '/payment-links', $payload, $headers);
    }

    /**
     * List payment links
     */
    public function list(?int $limit = null, ?int $offset = null): array {
        $params = [];
        if ($limit !== null) {
            $params['limit'] = $limit;
        }
        if ($offset !== null) {
            $params['offset'] = $offset;
        }

        return $this->http->request('GET', '/payment-links', $params);
    }
}
