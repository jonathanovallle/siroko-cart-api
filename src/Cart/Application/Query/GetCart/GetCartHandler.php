<?php

namespace App\Cart\Application\Query\GetCart;

use App\Cart\Application\DTO\CartDTO;
use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;

final class GetCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function handle(GetCartQuery $query): ?CartDTO
    {
        $cartId = CartId::fromString($query->getCartId());
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            return null;
        }

        return CartDTO::fromCart($cart);
    }
}