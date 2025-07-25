<?php

namespace App\Cart\Domain\Event;

use App\Cart\Domain\ValueObject\CartId;

final readonly class CartCleared
{
    public function __construct(
        private CartId $cartId,
        private \DateTime $occurredAt = new \DateTime()
    ) {}

    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    public function getOccurredAt(): \DateTime
    {
        return $this->occurredAt;
    }
}