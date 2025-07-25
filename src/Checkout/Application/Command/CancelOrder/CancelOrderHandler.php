<?php

namespace App\Checkout\Application\Command\CancelOrder;

use App\Checkout\Domain\Repository\OrderRepositoryInterface;
use App\Checkout\Domain\ValueObject\OrderId;
use DomainException;

final class CancelOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function handle(CancelOrderCommand $command): void
    {
        $orderId = OrderId::fromString($command->getOrderId());
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            throw new DomainException('Order not found');
        }

        $order->cancel();
        $this->orderRepository->save($order);
    }
}