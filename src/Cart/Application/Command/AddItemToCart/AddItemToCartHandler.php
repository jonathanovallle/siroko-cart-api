<?php

namespace App\Cart\Application\Command\AddItemToCart;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\Repository\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;
use DomainException;

final class AddItemToCartHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function handle(AddItemToCartCommand $command): void
    {
        $cartId = CartId::fromString($command->getCartId());
        $productId = ProductId::fromString($command->getProductId());
        $quantity = Quantity::fromInt($command->getQuantity());

        $cart = $this->cartRepository->findById($cartId);
        if (!$cart) {
            throw new DomainException('Cart not found');
        }

        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new DomainException('Product not found');
        }

        $cart->addItem($product, $quantity);
        $this->cartRepository->save($cart);
    }
}