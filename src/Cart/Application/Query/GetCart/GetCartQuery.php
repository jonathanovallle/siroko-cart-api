<?php

namespace App\Cart\Application\Query\GetCart;

final readonly class GetCartQuery
{
    public function __construct(private string $cartId) {}

    public function getCartId(): string
    {
        return $this->cartId;
    }
}