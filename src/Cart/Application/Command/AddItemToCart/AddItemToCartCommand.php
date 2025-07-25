<?php

namespace App\Cart\Application\Command\AddItemToCart;

final readonly class AddItemToCartCommand
{
    public function __construct(
        private string $cartId,
        private string $productId,
        private int $quantity
    ) {}

    public function getCartId(): string
    {
        return $this->cartId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}