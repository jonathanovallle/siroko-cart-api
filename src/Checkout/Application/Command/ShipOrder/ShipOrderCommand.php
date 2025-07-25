<?php

namespace App\Checkout\Application\Command\ShipOrder;

final readonly class ShipOrderCommand
{
    public function __construct(private string $orderId) {}

    public function getOrderId(): string
    {
        return $this->orderId;
    }
}