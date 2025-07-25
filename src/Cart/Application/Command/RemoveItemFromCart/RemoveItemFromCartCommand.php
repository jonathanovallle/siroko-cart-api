<?php

namespace App\Cart\Application\Command\RemoveItemFromCart;

final readonly class RemoveItemFromCartCommand
{
    public function __construct(
        private string $cartId,
        private string $productId
    ) {}

    public function getCartId(): string
    {
        return $this->cartId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}