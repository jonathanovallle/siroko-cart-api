<?php

namespace App\Cart\Application\Command\RemoveItemFromCart;

use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use DomainException;

final class RemoveItemFromCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function handle(RemoveItemFromCartCommand $command): void
    {
        $cartId = CartId::fromString($command->getCartId());
        $productId = ProductId::fromString($command->getProductId());

        $cart = $this->cartRepository->findById($cartId);
        if (!$cart) {
            throw new DomainException('Cart not found');
        }

        $cart->removeItem($productId);
        $this->cartRepository->save($cart);
    }
}