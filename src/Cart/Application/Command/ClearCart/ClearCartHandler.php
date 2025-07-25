<?php

namespace App\Cart\Application\Command\ClearCart;

use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use DomainException;

final class ClearCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function handle(ClearCartCommand $command): void
    {
        $cartId = CartId::fromString($command->getCartId());
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw new DomainException('Cart not found');
        }

        $cart->clear();
        $this->cartRepository->save($cart);
    }
}