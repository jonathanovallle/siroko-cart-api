<?php

namespace App\Checkout\Domain\Entity;

use App\Checkout\Domain\ValueObject\OrderId;
use App\Checkout\Domain\ValueObject\OrderStatus;
use App\Checkout\Domain\ValueObject\CustomerInfo;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\Money;
use App\Checkout\Domain\Event\OrderCreated;
use App\Checkout\Domain\Event\OrderPaid;
use App\Checkout\Domain\Event\OrderShipped;
use App\Checkout\Domain\Event\OrderCancelled;
use DomainException;

class Order
{
    private array $items = [];
    private array $domainEvents = [];

    public function __construct(
        private OrderId $id,
        private CartId $cartId,
        private CustomerInfo $customerInfo,
        private OrderStatus $status,
        private Money $totalAmount,
        private \DateTime $createdAt
    ) {
        $this->recordEvent(new OrderCreated($this->id, $this->cartId, $this->createdAt));
    }

    public static function fromCart(
        CartId $cartId,
        CustomerInfo $customerInfo,
        array $cartItems,
        Money $totalAmount
    ): self {
        $order = new self(
            OrderId::generate(),
            $cartId,
            $customerInfo,
            OrderStatus::pending(),
            $totalAmount,
            new \DateTime()
        );

        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem(
                $cartItem->getProductId(),
                $cartItem->getProductName(),
                $cartItem->getUnitPrice(),
                $cartItem->getQuantity()
            );
            $order->items[] = $orderItem;
        }

        return $order;
    }

    public function getId(): OrderId
    {
        return $this->id;
    }

    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    public function getCustomerInfo(): CustomerInfo
    {
        return $this->customerInfo;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getTotalAmount(): Money
    {
        return $this->totalAmount;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function markAsPaid(): void
    {
        if (!$this->status->isPending()) {
            throw new DomainException('Order must be pending to be marked as paid');
        }

        $this->status = OrderStatus::paid();
        $this->recordEvent(new OrderPaid($this->id, new \DateTime()));
    }

    public function ship(): void
    {
        if (!$this->status->isPaid()) {
            throw new DomainException('Order must be paid to be shipped');
        }

        $this->status = OrderStatus::shipped();
        $this->recordEvent(new OrderShipped($this->id, new \DateTime()));
    }

    public function cancel(): void
    {
        if ($this->status->getValue() === OrderStatus::SHIPPED || $this->status->getValue() === OrderStatus::DELIVERED) {
            throw new DomainException('Cannot cancel shipped or delivered order');
        }

        $this->status = OrderStatus::cancelled();
        $this->recordEvent(new OrderCancelled($this->id, new \DateTime()));
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}