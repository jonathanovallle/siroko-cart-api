<?php

namespace App\Checkout\Application\DTO;

use App\Checkout\Domain\Entity\Order;

final readonly class OrderDTO
{
    public function __construct(
        private string $id,
        private string $cartId,
        private array $items,
        private string $status,
        private float $totalAmount,
        private string $currency,
        private string $customerEmail,
        private string $customerName,
        private string $customerAddress,
        private \DateTime $createdAt
    ) {}

    public static function fromOrder(Order $order): self
    {
        $items = array_map(
            fn($item) => OrderItemDTO::fromOrderItem($item),
            $order->getItems()
        );

        $customerInfo = $order->getCustomerInfo();

        return new self(
            $order->getId()->toString(),
            $order->getCartId()->toString(),
            $items,
            $order->getStatus()->getValue(),
            $order->getTotalAmount()->getAmount(),
            $order->getTotalAmount()->getCurrency(),
            $customerInfo->getEmail(),
            $customerInfo->getFullName(),
            $customerInfo->getAddress() . ', ' . $customerInfo->getCity() . ' ' . $customerInfo->getPostalCode(),
            $order->getCreatedAt()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCartId(): string
    {
        return $this->cartId;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerAddress(): string
    {
        return $this->customerAddress;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cartId,
            'items' => array_map(fn(OrderItemDTO $item) => $item->toArray(), $this->items),
            'status' => $this->status,
            'total_amount' => $this->totalAmount,
            'currency' => $this->currency,
            'customer_email' => $this->customerEmail,
            'customer_name' => $this->customerName,
            'customer_address' => $this->customerAddress,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}