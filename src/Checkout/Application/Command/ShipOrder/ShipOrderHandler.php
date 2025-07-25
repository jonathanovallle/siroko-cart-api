<?php

namespace App\Checkout\Application\Command\ShipOrder;

use App\Checkout\Domain\Repository\OrderRepositoryInterface;
use App\Checkout\Domain\ValueObject\OrderId;
use DomainException;

final class ShipOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function handle(ShipOrderCommand $command): void
    {
        $orderId = OrderId::fromString($command->getOrderId());
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            throw new DomainException('Order not found');
        }

        $order->ship();
        $this->orderRepository->save($order);
    }
}