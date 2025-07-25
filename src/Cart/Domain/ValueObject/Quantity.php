<?php

namespace App\Cart\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Quantity
{
    private function __construct(private int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative');
        }
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function add(Quantity $other): self
    {
        return new self($this->value + $other->value);
    }

    public function subtract(Quantity $other): self
    {
        $result = $this->value - $other->value;
        if ($result < 0) {
            throw new InvalidArgumentException('Cannot subtract more than available quantity');
        }
        return new self($result);
    }

    public function isZero(): bool
    {
        return $this->value === 0;
    }

    public function equals(Quantity $other): bool
    {
        return $this->value === $other->value;
    }
}