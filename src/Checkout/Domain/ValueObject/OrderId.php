<?php

namespace App\Checkout\Domain\ValueObject;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class OrderId
{
    private function __construct(private UuidInterface $value) {}

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    public function toString(): string
    {
        return $this->value->toString();
    }

    public function equals(OrderId $other): bool
    {
        return $this->value->equals($other->value);
    }
}