<?php

namespace App\Checkout\Domain\Event;

use App\Checkout\Domain\ValueObject\OrderId;
use App\Cart\Domain\ValueObject\CartId;

final readonly class OrderCreated
{
    public function __construct(
        private OrderId $orderId,
        private CartId $cartId,
        private \DateTime $occurredAt
    ) {}

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    public function getOccurredAt(): \DateTime
    {
        return $this->occurredAt;
    }
}