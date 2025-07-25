<?php

namespace App\Cart\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Money
{
    private function __construct(
        private int $amount, // en centavos
        private string $currency
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
        if (empty($currency)) {
            throw new InvalidArgumentException('Currency cannot be empty');
        }
    }

    public static function fromAmount(float $amount, string $currency = 'EUR'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public static function fromCents(int $cents, string $currency = 'EUR'): self
    {
        return new self($cents, $currency);
    }

    public function getAmount(): float
    {
        return $this->amount / 100;
    }

    public function getCents(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        $this->ensureSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    private function ensureSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot operate on different currencies');
        }
    }
}