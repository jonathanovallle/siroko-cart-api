<?php

namespace App\Cart\Domain\Entity;

use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Money;

class Product
{
    public function __construct(
        private ProductId $id,
        private string $name,
        private string $description,
        private Money $price,
        private int $stock,
        private bool $active = true
    ) {}

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isAvailable(int $quantity = 1): bool
    {
        return $this->active && $this->stock >= $quantity;
    }

    public function reduceStock(int $quantity): void
    {
        if (!$this->isAvailable($quantity)) {
            throw new \DomainException('Insufficient stock available');
        }
        $this->stock -= $quantity;
    }
}