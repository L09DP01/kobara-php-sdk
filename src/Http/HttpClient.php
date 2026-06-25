<?php

namespace Kobara\Http;

use Kobara\Exceptions\KobaraAPIException;

class HttpClient {
    private string $apiKey;
    private string $baseUrl;

    public function __construct(string $apiKey, string $baseUrl) {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function request(string $method, string $endpoint, array $data = [], array $headers = []): array {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $ch = curl_init();

        $defaultHeaders = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        // Format custom headers (e.g. Idempotency-Key)
        foreach ($headers as $key => $val) {
            $defaultHeaders[] = "$key: $val";
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            $separator = (str_contains($url, '?')) ? '&' : '?';
            $url .= $separator . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMsg = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException("Failed to connect to Kobara API: " . $errorMsg);
        }

        curl_close($ch);

        $result = json_decode($response, true) ?: [];

        if ($httpCode < 200 || $httpCode >= 300) {
            $message = $result['message'] ?? ($result['error'] ?? "Unknown API Error");
            $errorType = $result['type'] ?? "api_error";
            throw new KobaraAPIException($message, $httpCode, $errorType);
        }

        return $result;
    }
}
