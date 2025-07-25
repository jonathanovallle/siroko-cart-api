<?php

namespace App\Checkout\Domain\Repository;

use App\Checkout\Domain\Entity\Order;
use App\Checkout\Domain\ValueObject\OrderId;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(OrderId $id): ?Order;
    public function findByCustomerEmail(string $email): array;
}