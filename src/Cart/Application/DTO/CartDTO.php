<?php

namespace App\Cart\Application\DTO;

use App\Cart\Domain\Entity\Cart;

final readonly class CartDTO
{
    public function __construct(
        private string $id,
        private array $items,
        private int $itemCount,
        private float $totalAmount,
        private string $currency,
        private \DateTime $createdAt
    ) {}

    public static function fromCart(Cart $cart): self
    {
        $items = array_map(
            fn($item) => CartItemDTO::fromCartItem($item),
            $cart->getItems()
        );

        return new self(
            $cart->getId()->toString(),
            $items,
            $cart->getItemCount(),
            $cart->getTotalAmount()->getAmount(),
            $cart->getTotalAmount()->getCurrency(),
            $cart->getCreatedAt()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'items' => array_map(fn(CartItemDTO $item) => $item->toArray(), $this->items),
            'item_count' => $this->itemCount,
            'total_amount' => $this->totalAmount,
            'currency' => $this->currency,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}