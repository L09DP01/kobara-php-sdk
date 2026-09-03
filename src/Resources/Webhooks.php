<?php

namespace Kobara\Resources;

use Kobara\Exceptions\KobaraSignatureVerificationException;

class Webhooks {
    /**
     * Securely verify webhook payload signature locally using HMAC SHA-256
     */
    public static function constructEvent(string $payload, string $signature, string $secret, int $toleranceSeconds = 300): array {
        if (empty($payload)) {
            throw new KobaraSignatureVerificationException("Payload cannot be empty");
        }
        if (empty($signature)) {
            throw new KobaraSignatureVerificationException("Kobara-Signature header is missing");
        }
        if (empty($secret)) {
            throw new KobaraSignatureVerificationException("Webhook secret is missing");
        }

        $parts = [];
        foreach (explode(',', $signature) as $part) {
            if (str_contains($part, '=')) {
                [$key, $value] = explode('=', trim($part), 2);
                $parts[$key] = $value;
            }
        }
        $timestamp = isset($parts['t']) && ctype_digit($parts['t']) ? (int) $parts['t'] : 0;
        $targetSignature = $parts['v1'] ?? '';
        if ($timestamp <= 0 || !preg_match('/^[a-f0-9]{64}$/i', $targetSignature)) {
            throw new KobaraSignatureVerificationException("Invalid Kobara-Signature format");
        }
        if (abs(time() - $timestamp) > $toleranceSeconds) {
            throw new KobaraSignatureVerificationException("Webhook timestamp is outside the tolerance window");
        }

        $computed = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        if (!hash_equals(strtolower($computed), strtolower($targetSignature))) {
            throw new KobaraSignatureVerificationException("Invalid signature. HMAC verification failed.");
        }

        $decoded = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new KobaraSignatureVerificationException("Failed to parse raw body payload as JSON: " . json_last_error_msg());
        }

        return $decoded;
    }
}
