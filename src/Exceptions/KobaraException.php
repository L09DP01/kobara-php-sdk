<?php

namespace Kobara\Exceptions;

class KobaraException extends \Exception {}

class KobaraAPIException extends KobaraException {
    protected int $statusCode;
    protected string $errorType;

    public function __construct(string $message, int $statusCode, string $errorType = 'api_error') {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->errorType = $errorType;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getErrorType(): string {
        return $this->errorType;
    }
}

class KobaraSignatureVerificationException extends KobaraException {}
