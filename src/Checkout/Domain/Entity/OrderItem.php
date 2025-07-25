<?php

namespace App\Checkout\Domain\Entity;

use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Money;
use App\Cart\Domain\ValueObject\Quantity;

class OrderItem
{
    private ?Order $order = null;

    public function __construct(
        private ProductId $productId,
        private string $productName,
        private Money $unitPrice,
        private Quantity $quantity
    ) {}

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getUnitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getTotalPrice(): Money
    {
        return $this->unitPrice->multiply($this->quantity->getValue());
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }
}