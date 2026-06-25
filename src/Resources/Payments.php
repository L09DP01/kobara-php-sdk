<?php

namespace Kobara\Resources;

use Kobara\Http\HttpClient;

class Payments {
    private HttpClient $http;

    public function __construct(HttpClient $http) {
        $this->http = $http;
    }

    /**
     * Create a new MonCash payment
     * 
     * @param array $payload {
     *     @type float $amount The amount to charge
     *     @type string $currency Currency (default: "HTG")
     *     @type string $description Optional description
     *     @type array $customer Optional customer info {name, email, phone}
     *     @type string $success_url Redirect URL on success
     *     @type string $cancel_url Redirect URL on cancel/error
     *     @type array $metadata Key-value pairs for custom data
     * }
     * @param string|null $idempotencyKey
     * @return array {status: "success", data: array}
     */
    public function create(array $payload, ?string $idempotencyKey = null): array {
        $headers = [];
        if ($idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        return $this->http->request('POST', '/payments', $payload, $headers);
    }

    /**
     * Retrieve a specific payment by ID
     */
    public function retrieve(string $id): array {
        return $this->http->request('GET', "/payments/{$id}");
    }

    /**
     * List all payments
     */
    public function list(?int $limit = null, ?int $offset = null, ?string $status = null): array {
        $params = [];
        if ($limit !== null) {
            $params['limit'] = $limit;
        }
        if ($offset !== null) {
            $params['offset'] = $offset;
        }
        if ($status !== null) {
            $params['status'] = $status;
        }

        return $this->http->request('GET', '/payments', $params);
    }
}
