<?php

namespace App\Checkout\Domain\Service;

final readonly class PaymentResult
{
    public function __construct(
        private bool $successful,
        private string $transactionId,
        private ?string $errorMessage = null
    ) {}

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public static function success(string $transactionId): self
    {
        return new self(true, $transactionId);
    }

    public static function failure(string $errorMessage): self
    {
        return new self(false, '', $errorMessage);
    }
}