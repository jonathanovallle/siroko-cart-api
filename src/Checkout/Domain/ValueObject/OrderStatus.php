<?php

namespace App\Checkout\Domain\ValueObject;

use InvalidArgumentException;

final readonly class OrderStatus
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const PROCESSING = 'processing';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';

    private const VALID_STATUSES = [
        self::PENDING,
        self::PAID,
        self::PROCESSING,
        self::SHIPPED,
        self::DELIVERED,
        self::CANCELLED,
    ];

    private function __construct(private string $value)
    {
        if (!in_array($value, self::VALID_STATUSES)) {
            throw new InvalidArgumentException("Invalid order status: {$value}");
        }
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function paid(): self
    {
        return new self(self::PAID);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function shipped(): self
    {
        return new self(self::SHIPPED);
    }

    public static function delivered(): self
    {
        return new self(self::DELIVERED);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(OrderStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isPaid(): bool
    {
        return $this->value === self::PAID;
    }

    public function isCancelled(): bool
    {
        return $this->value === self::CANCELLED;
    }
}