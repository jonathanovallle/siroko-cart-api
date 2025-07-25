<?php

namespace App\Checkout\Application\Query\GetOrder;

final readonly class GetOrderQuery
{
    public function __construct(private string $orderId) {}

    public function getOrderId(): string
    {
        return $this->orderId;
    }
}