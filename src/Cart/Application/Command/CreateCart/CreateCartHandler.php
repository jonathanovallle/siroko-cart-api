<?php

namespace App\Cart\Application\Command\CreateCart;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Repository\CartRepositoryInterface;

final class CreateCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function handle(CreateCartCommand $command): string
    {
        $cart = Cart::create();
        $this->cartRepository->save($cart);
        
        return $cart->getId()->toString();
    }
}