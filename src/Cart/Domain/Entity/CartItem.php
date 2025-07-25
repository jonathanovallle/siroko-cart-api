<?php

namespace App\Cart\Domain\Entity;

use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;
use App\Cart\Domain\ValueObject\Money;

class CartItem
{
    private ?Cart $cart = null;

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

    public function updateQuantity(Quantity $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function increaseQuantity(Quantity $quantity): void
    {
        $this->quantity = $this->quantity->add($quantity);
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }
}