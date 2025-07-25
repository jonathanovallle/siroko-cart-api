<?php

namespace App\Checkout\Application\Query\GetOrder;

use App\Checkout\Application\DTO\OrderDTO;
use App\Checkout\Domain\Repository\OrderRepositoryInterface;
use App\Checkout\Domain\ValueObject\OrderId;

final class GetOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function handle(GetOrderQuery $query): ?OrderDTO
    {
        $orderId = OrderId::fromString($query->getOrderId());
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            return null;
        }

        return OrderDTO::fromOrder($order);
    }
}