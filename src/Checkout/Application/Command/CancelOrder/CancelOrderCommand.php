<?php

namespace App\Checkout\Application\Command\CancelOrder;

final readonly class CancelOrderCommand
{
    public function __construct(private string $orderId) {}

    public function getOrderId(): string
    {
        return $this->orderId;
    }
}