<?php

namespace App\Cart\Application\DTO;

use App\Cart\Domain\Entity\Product;

final readonly class ProductDTO
{
    public function __construct(
        private string $id,
        private string $name,
        private string $description,
        private float $price,
        private string $currency,
        private int $stock,
        private bool $active
    ) {}

    public static function fromProduct(Product $product): self
    {
        return new self(
            $product->getId()->toString(),
            $product->getName(),
            $product->getDescription(),
            $product->getPrice()->getAmount(),
            $product->getPrice()->getCurrency(),
            $product->getStock(),
            $product->isActive()
        );
    }

    public function getId(): string
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'stock' => $this->stock,
            'active' => $this->active,
        ];
    }
}