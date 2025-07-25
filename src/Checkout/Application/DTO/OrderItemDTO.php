<?php

namespace App\Checkout\Application\DTO;

use App\Checkout\Domain\Entity\OrderItem;

final readonly class OrderItemDTO
{
    public function __construct(
        private string $productId,
        private string $productName,
        private float $unitPrice,
        private string $currency,
        private int $quantity,
        private float $totalPrice
    ) {}

    public static function fromOrderItem(OrderItem $orderItem): self
    {
        return new self(
            $orderItem->getProductId()->toString(),
            $orderItem->getProductName(),
            $orderItem->getUnitPrice()->getAmount(),
            $orderItem->getUnitPrice()->getCurrency(),
            $orderItem->getQuantity()->getValue(),
            $orderItem->getTotalPrice()->getAmount()
        );
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'unit_price' => $this->unitPrice,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
            'total_price' => $this->totalPrice,
        ];
    }
}