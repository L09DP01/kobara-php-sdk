<?php

namespace Kobara\Resources;

use Kobara\Http\HttpClient;

class Payments {
    private HttpClient $http;

    public function __construct(HttpClient $http) {
        $this->http = $http;
    }

    /**
     * Create a payment using the unified checkout or a supported provider.
     * 
     * @param array $payload {
     *     @type float $amount The amount to charge
     *     @type string $currency Currency (default: "HTG")
     *     @type string $provider kobara, moncash, natcash, card, paypal, apple_pay, or google_pay
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
        $headers['Idempotency-Key'] = $idempotencyKey ?: bin2hex(random_bytes(16));

        return $this->http->request('POST', '/payments', $payload, $headers);
    }

}
