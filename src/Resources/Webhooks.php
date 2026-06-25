<?php

namespace Kobara\Resources;

use Kobara\Exceptions\KobaraSignatureVerificationException;

class Webhooks {
    /**
     * Securely verify webhook payload signature locally using HMAC SHA-256
     */
    public static function constructEvent(string $payload, string $signature, string $secret): array {
        if (empty($payload)) {
            throw new KobaraSignatureVerificationException("Payload cannot be empty");
        }
        if (empty($signature)) {
            throw new KobaraSignatureVerificationException("Kobara-Signature header is missing");
        }
        if (empty($secret)) {
            throw new KobaraSignatureVerificationException("Webhook secret is missing");
        }

        // Support both raw hex signature and t=timestamp,v1=hash format
        $targetSignature = trim($signature);
        if (str_contains($signature, 'v1=')) {
            $parts = explode(',', $signature);
            foreach ($parts as $part) {
                $trimmedPart = trim($part);
                if (str_starts_with($trimmedPart, 'v1=')) {
                    $targetSignature = substr($trimmedPart, 3);
                    break;
                }
            }
        }

        $computed = hash_hmac('sha256', $payload, $secret);

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
