<?php

namespace App\Cart\Domain\Event;

use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;

final readonly class ItemUpdatedInCart
{
    public function __construct(
        private CartId $cartId,
        private ProductId $productId,
        private Quantity $newQuantity,
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

    public function getNewQuantity(): Quantity
    {
        return $this->newQuantity;
    }

    public function getOccurredAt(): \DateTime
    {
        return $this->occurredAt;
    }
}