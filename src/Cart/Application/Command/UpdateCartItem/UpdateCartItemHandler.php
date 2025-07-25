<?php

namespace App\Cart\Application\Command\UpdateCartItem;

use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;
use DomainException;

final class UpdateCartItemHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function handle(UpdateCartItemCommand $command): void
    {
        $cartId = CartId::fromString($command->getCartId());
        $productId = ProductId::fromString($command->getProductId());
        $quantity = Quantity::fromInt($command->getQuantity());

        $cart = $this->cartRepository->findById($cartId);
        if (!$cart) {
            throw new DomainException('Cart not found');
        }

        $cart->updateItemQuantity($productId, $quantity);
        $this->cartRepository->save($cart);
    }
}