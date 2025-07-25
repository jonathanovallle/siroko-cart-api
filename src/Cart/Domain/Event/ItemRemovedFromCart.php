<?php

namespace App\Cart\Domain\Event;

use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;

final readonly class ItemRemovedFromCart
{
    public function __construct(
        private CartId $cartId,
        private ProductId $productId,
        private \DateTime $occurredAt = new \DateTime()
    ) {}

    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getOccurredAt(): \DateTime
    {
        return $this->occurredAt;
    }
}