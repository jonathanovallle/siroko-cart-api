<?php

namespace App\Checkout\Application\Query\GetOrdersByCustomer;

use App\Checkout\Application\DTO\OrderDTO;
use App\Checkout\Domain\Repository\OrderRepositoryInterface;

final class GetOrdersByCustomerHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function handle(GetOrdersByCustomerQuery $query): array
    {
        $orders = $this->orderRepository->findByCustomerEmail($query->getEmail());
        
        return array_map(
            fn($order) => OrderDTO::fromOrder($order),
            $orders
        );
    }
}