<?php

namespace App\Checkout\Domain\Event;

use App\Checkout\Domain\ValueObject\OrderId;

final readonly class OrderShipped
{
    public function __construct(
        private OrderId $orderId,
        private \DateTime $occurredAt
    ) {}

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getOccurredAt(): \DateTime
    {
        return $this->occurredAt;
    }
}