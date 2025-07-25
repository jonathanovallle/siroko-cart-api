<?php

namespace App\Cart\Application\Command\ClearCart;

final readonly class ClearCartCommand
{
    public function __construct(private string $cartId) {}

    public function getCartId(): string
    {
        return $this->cartId;
    }
}